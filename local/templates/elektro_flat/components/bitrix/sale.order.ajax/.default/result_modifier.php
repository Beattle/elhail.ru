<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!CModule::IncludeModule("iblock") || !CModule::IncludeModule("catalog"))
	return;

//DELIVERY_LOGOTIP//
foreach($arResult["DELIVERY"] as $key => $arDelivery) {
	if(is_array($arDelivery["LOGOTIP"])) {
		if($arDelivery["LOGOTIP"]["WIDTH"] > 80 || $arDelivery["LOGOTIP"]["HEIGHT"] > 31) {
			$arFileTmp = CFile::ResizeImageGet(
				$arDelivery["LOGOTIP"],
				array("width" => 80, "height" => 31),
				BX_RESIZE_IMAGE_PROPORTIONAL,
				true
			);
			$arResult["DELIVERY"][$key]["LOGOTIP"] = array(
				"SRC" => $arFileTmp["src"],
				"WIDTH" => $arFileTmp["width"],
				"HEIGHT" => $arFileTmp["height"],
			);
		}
	}
}

//PAY_SYSTEM_LOGOTIP//
foreach($arResult["PAY_SYSTEM"] as $key => $arPaySystem) {
	if(is_array($arPaySystem["PSA_LOGOTIP"])) {
		if($arPaySystem["PSA_LOGOTIP"]["WIDTH"] > 80 || $arPaySystem["PSA_LOGOTIP"]["HEIGHT"] > 31) {
			$arFileTmp = CFile::ResizeImageGet(
				$arPaySystem["PSA_LOGOTIP"],
				array("width" => 80, "height" => 31),
				BX_RESIZE_IMAGE_PROPORTIONAL,
				true
			);
			$arResult["PAY_SYSTEM"][$key]["PSA_LOGOTIP"] = array(
				"SRC" => $arFileTmp["src"],
				"WIDTH" => $arFileTmp["width"],
				"HEIGHT" => $arFileTmp["height"],
			);
		}
	}
}

//PICTURE//
foreach($arResult["BASKET_ITEMS"] as $key => $arBasketItems) {
	$ar = CIBlockElement::GetList(
		array(), 
		array("ID" => $arBasketItems["PRODUCT_ID"]), 
		false, 
		false, 
		array("ID", "IBLOCK_ID", "DETAIL_PICTURE")
	)->Fetch();		
	if($ar["DETAIL_PICTURE"] > 0) {
		$arResult["BASKET_ITEMS"][$key]["DETAIL_PICTURE"] = CFile::ResizeImageGet($ar["DETAIL_PICTURE"], array("width" => 30, "height" => 30), BX_RESIZE_IMAGE_PROPORTIONAL, true);
	} else {
		$mxResult = CCatalogSku::GetProductInfo($ar["ID"]);
		if(is_array($mxResult)) {
			$ar = CIBlockElement::GetList(
				array(), 
				array("ID" => $mxResult["ID"]), 
				false, 
				false, 
				array("ID", "IBLOCK_ID", "DETAIL_PICTURE")
			)->Fetch();
			if($ar["DETAIL_PICTURE"] > 0) {
				$arResult["BASKET_ITEMS"][$key]["DETAIL_PICTURE"] = CFile::ResizeImageGet($ar["DETAIL_PICTURE"], array("width" => 30, "height" => 30), BX_RESIZE_IMAGE_PROPORTIONAL, true);
			}
		}
	}
}

//AUTH_SERVICES//
$arResult["AUTH_SERVICES"] = false;
if(!$USER->IsAuthorized() && CModule::IncludeModule("socialservices")) {
	$oAuthManager = new CSocServAuthManager();
	$arServices = $oAuthManager->GetActiveAuthServices($arResult);

	if(!empty($arServices)) {
		$arResult["AUTH_SERVICES"] = $arServices;
	}
}?>