<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Loader,
	Bitrix\Iblock;

if(!Loader::includeModule("iblock"))
	return;

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000000;

$arParams["IBLOCK_ID"] = intval($arParams["IBLOCK_ID"]);
$arParams["ELEMENT_ID"] = intval($arParams["ELEMENT_ID"]);
if($arParams["IBLOCK_ID"] <= 0 || $arParams["ELEMENT_ID"] <= 0)
	return;

$arParams["ELEMENT_AREA_ID"] = trim($arParams["ELEMENT_AREA_ID"]);
if(empty($arParams["ELEMENT_AREA_ID"]))
	return;

global $arSetting, $USER, $APPLICATION;

$arParams["PRE_MODERATION"] = trim($arParams["PRE_MODERATION"]);
if($USER->IsAdmin())
	$arParams["PRE_MODERATION"] = "N";
elseif($arParams["PRE_MODERATION"] != "N")
	$arParams["PRE_MODERATION"] = "Y";

$arParams["USE_CAPTCHA"] = $arSetting["FORMS_USE_CAPTCHA"]["VALUE"];
$arParams["IS_AUTHORIZED"] = $USER->IsAuthorized() ? "Y" : "N";
$arParams["CAPTCHA_CODE"] = $arParams["IS_AUTHORIZED"] != "Y" && $arParams["USE_CAPTCHA"] == "Y" ? $APPLICATION->CaptchaGetCode() : "";

$arParams["COMMENT_URL"] = $APPLICATION->GetCurPage();

$arParams["PROPERTIES"] = array("NAME", "MESSAGE");

$arParams["PARAMS_STRING"] = strtr(base64_encode(addslashes(gzcompress(serialize($arParams),9))), '+/=', '-_,');

if($this->StartResultCache()) {
	//IBLOCK//
	$arIblock = CIBlock::GetList(array("SORT" => "ASC"), array("ID" => $arParams["IBLOCK_ID"], "ACTIVE" => "Y"))->Fetch();
	
	if(empty($arIblock)) {
		$this->abortResultCache();
		return;
	}
	
	$arResult["IBLOCK"]["ID"] = $arIblock["ID"];
	
	//IBLOCK_PROPS//
	$rsProps = CIBlock::GetProperties($arIblock["ID"], array("SORT" => "ASC", "NAME" => "ASC"), array("ACTIVE" => "Y"));
	while($arProps = $rsProps->fetch()) {
		$arResult["IBLOCK"]["PROPERTIES"][] = $arProps;
	}
	
	if(!isset($arResult["IBLOCK"]["PROPERTIES"]) || empty($arResult["IBLOCK"]["PROPERTIES"])) {
		$this->abortResultCache();
		return;
	}
	
	$arResult["IBLOCK"]["STRING"] = strtr(base64_encode(addslashes(gzcompress(serialize($arResult["IBLOCK"]),9))), '+/=', '-_,');

	//ELEMENT//
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

	$arResult["ELEMENT"]["STRING"] = strtr(base64_encode(addslashes(gzcompress(serialize($arResult["ELEMENT"]),9))), '+/=', '-_,');

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
	
	//ITEMS//
	$rsElements = CIBlockElement::GetList(
		array(
			"SORT" => "ASC",
			"ACTIVE_FROM" => "DESC",
			"CREATED" => "DESC"
		),
		array(
			"ACTIVE" => "Y",
			"IBLOCK_ID" => $arParams["IBLOCK_ID"],
			"PROPERTY_OBJECT_ID" => $arParams["ELEMENT_ID"]
		),
		false,
		array("nPageSize" => 5),
		array("ID", "IBLOCK_ID", "DATE_ACTIVE_FROM", "DETAIL_TEXT", "DATE_CREATE", "CREATED_BY")
	);	
	while($obElement = $rsElements->GetNextElement()) {
		$arElement = $obElement->GetFields();		
		
		$rsUser = $USER->GetByID($arElement["CREATED_BY"]);
		if($arUser = $rsUser->Fetch()) {
			if(!empty($arUser["PERSONAL_PHOTO"])) {
				$arFile = CFile::GetFileArray($arUser["PERSONAL_PHOTO"]);
				if($arFile["WIDTH"] > 57 || $arFile["HEIGHT"] > 57) {
					$arFileTmp = CFile::ResizeImageGet(
						$arFile,
						array("width" => 57, "height" => 57),
						BX_RESIZE_IMAGE_PROPORTIONAL,
						true
					);
					$arElement["CREATED_USER_PERSONAL_PHOTO"] = array(
						"SRC" => $arFileTmp["src"],
						"WIDTH" => $arFileTmp["width"],
						"HEIGHT" => $arFileTmp["height"],
					);
				} else {
					$arElement["CREATED_USER_PERSONAL_PHOTO"] = $arFile;
				}
			}
		}

		$arElement["PROPERTIES"] = $obElement->GetProperties();
		
		$arResult["ITEMS"][] = $arElement;
	}

	//NAVIGATION//
	$arResult["NAV_STRING"] = $rsElements->GetPageNavStringEx($navComponentObject, "", "reviews");
	
	//USER//
	if($arParams["IS_AUTHORIZED"] == "Y") {
		$arResult["USER"]["NAME"] = $USER->GetFullName();
	}
	
	$this->IncludeComponentTemplate();
}?>