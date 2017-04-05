<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Loader,
	Bitrix\Iblock;

if(!Loader::includeModule("iblock") || !Loader::includeModule("catalog"))
	return;

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000000;

$arParams["ELEMENT_ID"] = intval($arParams["ELEMENT_ID"]);

$arParams["ELEMENT_AREA_ID"] = trim($arParams["ELEMENT_AREA_ID"]);
if(empty($arParams["ELEMENT_AREA_ID"]))
	return;

$arParams["ELEMENT_PROPS"] = trim($arParams["ELEMENT_PROPS"]);

$arParams["SELECT_PROP_DIV"] = trim($arParams["SELECT_PROP_DIV"]);

if(empty($arParams["REQUIRED"]))
	$arParams["REQUIRED"] = array("NAME", "PHONE");

if(empty($arParams["BUY_MODE"]))
	$arParams["BUY_MODE"] = "ONE";

$arParams["BUTTON_ID"] = trim($arParams["BUTTON_ID"]);
if(empty($arParams["BUTTON_ID"]))
	return;

global $arSetting, $USER, $APPLICATION;
$arParams["USE_CAPTCHA"] = $arSetting["FORMS_USE_CAPTCHA"]["VALUE"];
$arParams["IS_AUTHORIZED"] = $USER->IsAuthorized() ? "Y" : "N";
$arParams["CAPTCHA_CODE"] = $arParams["IS_AUTHORIZED"] != "Y" && $arParams["USE_CAPTCHA"] == "Y" ? $APPLICATION->CaptchaGetCode() : "";

$arParams["PHONE_MASK"] = $arSetting["FORMS_PHONE_MASK"]["VALUE"];
$arParams["VALIDATE_PHONE_MASK"] = $arSetting["FORMS_VALIDATE_PHONE_MASK"]["VALUE"];

$arParams["PROPERTIES"] = array("NAME", "PHONE", "EMAIL", "MESSAGE");

$arParams["PARAMS_STRING"] = strtr(base64_encode(addslashes(gzcompress(serialize($arParams),9))), '+/=', '-_,');

if($this->StartResultCache()) {
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
				$arElement = CIBlockElement::GetList(
					array(),
					array(
						"ID" => $mxResult["ID"]
					),
					false,
					false,
					array("ID", "IBLOCK_ID", "PREVIEW_PICTURE", "DETAIL_PICTURE")
				)->Fetch();
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