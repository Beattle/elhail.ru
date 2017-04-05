<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader,
	Bitrix\Iblock;

if(!Loader::includeModule("iblock") || !Loader::includeModule("catalog"))
	return;

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000000;

$arParams["IBLOCK_ID"] = intval($arParams["IBLOCK_ID"]);
if($arParams["IBLOCK_ID"] <= 0)
	return;

$arParams["ELEMENT_ID"] = intval($arParams["ELEMENT_ID"]);
$arParams["ELEMENT_AREA_ID"] = trim($arParams["ELEMENT_AREA_ID"]);
if($arParams["ELEMENT_ID"] > 0 && empty($arParams["ELEMENT_AREA_ID"]))
	return;

$arParams["ELEMENT_NAME"] = trim($arParams["ELEMENT_NAME"]);
$arParams["ELEMENT_PRICE"] = trim($arParams["ELEMENT_PRICE"]);

$arParams["BUTTON_ID"] = trim($arParams["BUTTON_ID"]);
if(empty($arParams["BUTTON_ID"]))
	return;

$arParams["SELECT_PROP_DIV"] = trim($arParams["SELECT_PROP_DIV"]);

global $arSetting, $USER, $APPLICATION;
$arParams["USE_CAPTCHA"] = $arSetting["FORMS_USE_CAPTCHA"]["VALUE"];
$arParams["IS_AUTHORIZED"] = $USER->IsAuthorized() ? "Y" : "N";
$arParams["CAPTCHA_CODE"] = $arParams["IS_AUTHORIZED"] != "Y" && $arParams["USE_CAPTCHA"] == "Y" ? $APPLICATION->CaptchaGetCode() : "";

$arParams["PHONE_MASK"] = $arSetting["FORMS_PHONE_MASK"]["VALUE"];
$arParams["VALIDATE_PHONE_MASK"] = $arSetting["FORMS_VALIDATE_PHONE_MASK"]["VALUE"];

$arParams["PARAMS_STRING"] = strtr(base64_encode(addslashes(gzcompress(serialize($arParams),9))), '+/=', '-_,');

if($this->StartResultCache()) {
	//IBLOCK//
	$arIblock = CIBlock::GetList(array("SORT" => "ASC"), array("ID" => $arParams["IBLOCK_ID"], "ACTIVE" => "Y"))->Fetch();
	
	if(empty($arIblock)) {
		$this->abortResultCache();
		return;
	}
	
	$arResult["IBLOCK"]["ID"] = $arIblock["ID"];
	$arResult["IBLOCK"]["CODE"] = $arIblock["CODE"];
	$arResult["IBLOCK"]["NAME"] = $arIblock["NAME"];
	
	//IBLOCK_PROPS//
	$rsProps = CIBlock::GetProperties($arIblock["ID"], array("SORT" => "ASC", "NAME" => "ASC"), array("ACTIVE" => "Y", "PROPERTY_TYPE" => "S"));
	while($arProps = $rsProps->fetch()) {
		$arResult["IBLOCK"]["PROPERTIES"][] = $arProps;
	}
	
	if(!isset($arResult["IBLOCK"]["PROPERTIES"]) || empty($arResult["IBLOCK"]["PROPERTIES"])) {
		$this->abortResultCache();
		return;
	}
	
	$arResult["IBLOCK"]["STRING"] = strtr(base64_encode(addslashes(gzcompress(serialize($arResult["IBLOCK"]),9))), '+/=', '-_,');
	
	//ELEMENT//
	if($arParams["ELEMENT_ID"] > 0) {
		$arElement = CIBlockElement::GetList(
			array(),
			array(
				"ID" => $arParams["ELEMENT_ID"]
			),
			false,
			false,
			array("ID", "IBLOCK_ID", "NAME", "PREVIEW_PICTURE", "DETAIL_PICTURE")
		)->Fetch();

		if(empty($arElement)) {
			$this->abortResultCache();
			return;
		}
		
		$arResult["ELEMENT"]["ID"] = $arElement["ID"];
		$arResult["ELEMENT"]["NAME"] = $arElement["NAME"];

		if($arElement["PREVIEW_PICTURE"] <= 0 && $arElement["DETAIL_PICTURE"] <= 0) {
			$mxResult = CCatalogSku::GetProductInfo($arElement["ID"]);
			if(is_array($mxResult)) {
				$arElement = Iblock\ElementTable::getList(array(
					"select" => array(
						"ID", "IBLOCK_ID", "PREVIEW_PICTURE", "DETAIL_PICTURE"
					),
					"filter" => array(
						"ID" => $mxResult["ID"]
					)
				))->Fetch();
			}
		}

		if($arElement["PREVIEW_PICTURE"] > 0) {
			$arFile = CFile::GetFileArray($arElement["PREVIEW_PICTURE"]);
			if($arFile["WIDTH"] > 178 || $arFile["HEIGHT"] > 178) {
				$arFileTmp = CFile::ResizeImageGet(
					$arFile,
					array("width" => 178, "height" => 178),
					BX_RESIZE_IMAGE_PROPORTIONAL,
					true
				);		
				$arResult["ELEMENT"]["PREVIEW_PICTURE"] = array(
					"SRC" => $arFileTmp["src"],
					"WIDTH" => $arFileTmp["width"],
					"HEIGHT" => $arFileTmp["height"],
				);
			} else {
				$arResult["ELEMENT"]["PREVIEW_PICTURE"] = $arFile;
			}
		} elseif($arElement["DETAIL_PICTURE"] > 0) {
			$arFile = CFile::GetFileArray($arElement["DETAIL_PICTURE"]);
			if($arFile["WIDTH"] > 178 || $arFile["HEIGHT"] > 178) {
				$arFileTmp = CFile::ResizeImageGet(
					$arFile,
					array("width" => 178, "height" => 178),
					BX_RESIZE_IMAGE_PROPORTIONAL,
					true
				);		
				$arResult["ELEMENT"]["PREVIEW_PICTURE"] = array(
					"SRC" => $arFileTmp["src"],
					"WIDTH" => $arFileTmp["width"],
					"HEIGHT" => $arFileTmp["height"],
				);
			} else {
				$arResult["ELEMENT"]["PREVIEW_PICTURE"] = $arFile;
			}
		}
	}

	//USER//
	if($arParams["IS_AUTHORIZED"] == "Y") {
		$arResult["USER"]["NAME"] = $USER->GetFullName();
		$arResult["USER"]["EMAIL"] = $USER->GetEmail();
	}
	
	$this->IncludeComponentTemplate();
}?>