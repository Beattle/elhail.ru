<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Loader,
	Bitrix\Main\Config\Option,
	Bitrix\Main\Application,
	Bitrix\Main\Data\StaticHtmlCache;

$moduleClass = "CElektroinstrument";
$moduleID = "altop.elektroinstrument";

if(!Loader::IncludeModule($moduleID))
	return;

$arParams["SITE_BACKGROUNDS"] = array("TREE", "YELLOW_POLYGONS", "TURQUOISE_POLYGONS", "PURPLE_POLYGONS", "POLYGONS", "CONCRETE", "BRICKS", "CLOTH", "TILE", "CHAIN_ARMOUR", "MATERIAL");

$arParams["MODULE_ID"] = $moduleID;

//SET_OPTION_SITE_BACKGROUND//
foreach($arParams["SITE_BACKGROUNDS"] as $arSiteBg) {
	if(!Option::get($moduleID, "SITE_BACKGROUND_".$arSiteBg)) {
		$arFile = CFile::MakeFileArray(dirname(__FILE__)."/images/".mb_strtolower($arSiteBg).".jpg");
		$arFile["MODULE_ID"] = $moduleID;
		$arSiteBgPic = CFile::SaveFile($arFile, $moduleID);
		if($arSiteBgPic > 0) {
			$arSiteBgPicIds[] = $arSiteBgPic;
			Option::set($moduleID, "SITE_BACKGROUND_".$arSiteBg, $arSiteBgPic);
		}
	}
}
if(!Option::get($moduleID, "SITE_BACKGROUND_PICTURE_IDS") && count($arSiteBgPicIds) > 0)
	Option::set($moduleID, "SITE_BACKGROUND_PICTURE_IDS", serialize($arSiteBgPicIds));

//SET_OPTION_OPTIONS//
$request = Application::getInstance()->getContext()->getRequest();

$changeTheme = $request->getPost("CHANGE_THEME");
$theme = $request->getPost("THEME");

if($request->isPost() && $changeTheme == "Y" && check_bitrix_sessid()) {
	foreach($moduleClass::$arParametrsList as $blockCode => $arBlock){
		foreach($arBlock["OPTIONS"] as $optionCode => $arOption) {
			if($arOption["IN_SETTINGS_PANEL"] == "Y" && $theme == "default") {
				$newVal = $arOption["DEFAULT"];
			} else {
				$post = $request->getPost($optionCode);
				if($optionCode == "COLOR_SCHEME_CUSTOM"){
					$post = $moduleClass::CheckColor($post);
				} elseif($optionCode == "SITE_BACKGROUND") {					
					if($post != "N")
						$post = "Y";
				} elseif($optionCode == "SITE_BACKGROUND_PICTURE") {
					$postSiteBg = $request->getPost("SITE_BACKGROUND");
					foreach($arParams["SITE_BACKGROUNDS"] as $arSiteBg) {
						if($postSiteBg == $arSiteBg)
							$post = Option::get($moduleID, "SITE_BACKGROUND_".$arSiteBg);
					}
				}
				$newVal = $post;
				if($arOption["TYPE"] == "multiselectbox") {
					if(!is_array($newVal))
						$newVal = array();
				}
			}			
			$arTab["OPTIONS"][$optionCode] = $newVal;
		}
	}
	Option::set($moduleID, "OPTIONS", serialize((array)$arTab["OPTIONS"]), SITE_ID);
	
	if(CHTMLPagesCache::isOn()) {
		$staticHtmlCache = StaticHtmlCache::getInstance();
		$staticHtmlCache->deleteAll();
	}
	
	BXClearCache(true, "/".SITE_ID."/bitrix/catalog.section/");
	BXClearCache(true, "/".SITE_ID."/bitrix/catalog.element/");
	BXClearCache(true, "/".SITE_ID."/bitrix/catalog.bigdata.products/");
	BXClearCache(true, "/".SITE_ID."/bitrix/catalog.set.constructor/");
}

//RESULT//
$arResult = array();
$arFrontParametrs = $moduleClass::GetFrontParametrsValues(SITE_ID);
foreach($moduleClass::$arParametrsList as $blockCode => $arBlock){
	foreach($arBlock["OPTIONS"] as $optionCode => $arOption){		
		$arResult[$optionCode] = $arOption;
		$arResult[$optionCode]["VALUE"] = $arFrontParametrs[$optionCode];
		//CURRENT for compatibility with old versions
		if($arResult[$optionCode]["LIST"]){
			foreach($arResult[$optionCode]["LIST"] as $variantCode => $variantTitle){
				if(!is_array($variantTitle)){
					$arResult[$optionCode]["LIST"][$variantCode] = array("TITLE" => $variantTitle);
				}
				if($arResult[$optionCode]["TYPE"] == "selectbox"){
					if($arResult[$optionCode]["VALUE"] == $variantCode){
						$arResult[$optionCode]["LIST"][$variantCode]["CURRENT"] = "Y";
					}
				} elseif($arResult[$optionCode]["TYPE"] == "multiselectbox"){
					if(in_array($variantCode, $arResult[$optionCode]["VALUE"])){
						$arResult[$optionCode]["LIST"][$variantCode]["CURRENT"] = "Y";
					}
				}
			}
		}
	}
}

//COLOR_SCHEME//
if($arResult["COLOR_SCHEME"]["VALUE"] != "YELLOW") {	
	require_once $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$moduleID."/less/lessc.inc.php";	
	$less = new lessc;
	try {	
		if($arResult["COLOR_SCHEME"]["VALUE"] == "CUSTOM") {
			$baseColorCustom = str_replace("#", "", $arResult["COLOR_SCHEME_CUSTOM"]["VALUE"]);			
			$less->setVariables(array("bcolor" => (strlen($baseColorCustom) ? "#".$baseColorCustom : $arResult["COLOR_SCHEME"]["LIST"][$arResult["COLOR_SCHEME"]["DEFAULT"]]["COLOR"])));
		} else {
			$less->setVariables(array("bcolor" => $arResult["COLOR_SCHEME"]["LIST"][$arResult["COLOR_SCHEME"]["VALUE"]]["COLOR"]));
		}
		if(defined("SITE_TEMPLATE_PATH")) {
			$schemeDirPath = $_SERVER["DOCUMENT_ROOT"].SITE_TEMPLATE_PATH."/schemes/".$arResult["COLOR_SCHEME"]["VALUE"]."/";
			if(!is_dir($schemeDirPath))
				mkdir($schemeDirPath, 0755, true);

			$inputFile = $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$moduleID."/less/colors.less";
			$outputFile = $schemeDirPath."colors.css";			

			$cache = $less->cachedCompile($inputFile);
			$newCache = file_get_contents($outputFile);								

			if(md5($newCache) != md5($cache["compiled"])) {
				$output = $less->compileFile($inputFile, $outputFile);
			} else {
				$output = $less->checkedCompile($inputFile, $outputFile);
			}			
		}
	} catch(exception $e) {
		echo "Fatal error: ".$e->getMessage();
		die();
	}	
	$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/schemes/".$arResult["COLOR_SCHEME"]["VALUE"]."/colors.css", true);
}
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/custom.css", true);

//SITE_BACKGROUND//
if($arResult["SITE_BACKGROUND"]["VALUE"] == "Y" && $arResult["SITE_BACKGROUND_PICTURE"]["VALUE"] > 0) {
	$arFile = CFile::GetFileArray($arResult["SITE_BACKGROUND_PICTURE"]["VALUE"]);
	if(is_array($arFile)) {
		$APPLICATION->SetPageProperty(
			"backgroundImage",
			" style=\"background-image: url('".CHTTP::urnEncode($arFile["SRC"], "UTF-8")."')\""
		);
	}
}

//FALLING_SNOW//
if(in_array("FALLING_SNOW", $arResult["GENERAL_SETTINGS"]["VALUE"])) {
	$moduleClass::StartFallingSnow(SITE_TEMPLATE_PATH);
}

//SETTINGS_PANEL//
global $USER;
if($USER->IsAdmin() && $arResult["SHOW_SETTINGS_PANEL"]["VALUE"] == "Y") {
	$this->IncludeComponentTemplate();
}

return $arResult;?>