<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
	
if(!CModule::IncludeModule("iblock"))
	return;	

$arResult = array();

$element_id = preg_replace("~\D+~", "", $arParams["ELEMENT_ID"]);

$arElement = CIBlockElement::GetList(
	array(), 
	array("=ID" => $element_id), 
	false, 
	false, 
	array("ID", "IBLOCK_ID", "NAME", "DETAIL_PICTURE", "PROPERTY_CML2_LINK")
)->Fetch();	

$arResult["ELEMENT_NAME"] = $arElement["NAME"];

if($arElement["DETAIL_PICTURE"] > 0) {
	$arFileTmp = CFile::ResizeImageGet(
		$arElement["DETAIL_PICTURE"],
		array("width" => 178, "height" => 178),
		BX_RESIZE_IMAGE_PROPORTIONAL,
		true
	);		
	$arResult["PREVIEW_IMG"] = array(
		"SRC" => $arFileTmp["src"],
		"WIDTH" => $arFileTmp["width"],
		"HEIGHT" => $arFileTmp["height"],
	);
} else {
	if(!empty($arElement["PROPERTY_CML2_LINK_VALUE"])) {
		$arElement2 = CIBlockElement::GetList(
			array(), 
			array("=ID" => $arElement["PROPERTY_CML2_LINK_VALUE"]), 
			false, 
			false, 
			array("NAME", "DETAIL_PICTURE")
		)->Fetch();
		if($arElement2["DETAIL_PICTURE"] > 0) {				
			$arFileTmp = CFile::ResizeImageGet(
				$arElement2["DETAIL_PICTURE"],
				array("width" => 178, "height" => 178),
				BX_RESIZE_IMAGE_PROPORTIONAL,
				true
			);		
			$arResult["PREVIEW_IMG"] = array(
				"SRC" => $arFileTmp["src"],
				"WIDTH" => $arFileTmp["width"],
				"HEIGHT" => $arFileTmp["height"],
			);
		}
	}
}

if(!$USER->IsAuthorized()) {
	$arResult["CAPTCHA_CODE"] = $APPLICATION->CaptchaGetCode();
}
	
$arResult["EMAIL_TO"] = trim($arParams["EMAIL_TO"]);
if(strlen($arParams["EMAIL_TO"]) <= 0)
	$arResult["EMAIL_TO"] = COption::GetOptionString("main", "email_from");

$arResult["REQUIRED"] = implode("/", $arParams["REQUIRED_FIELDS"]);

if($USER->IsAuthorized())
	$arResult["NAME"] = $USER->GetFullName();

$arResult["MESSAGE"] = $arParams["ELEMENT_NAME"];
	
$this->IncludeComponentTemplate();?>