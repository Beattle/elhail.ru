<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Iblock,
	Bitrix\Main\Type\Collection;

global $arSetting;

//ELEMENTS//
foreach($arResult["ITEMS"] as $key => $arElement) {
	if($arElement["PROPERTIES"]["TOVAR_VYVEDEN_IZ_ASSORTIMENTA"]["VALUE_XML_ID"] == true){
		unset($arResult["ITEMS"][$key]);
	}
	//PREVIEW_PICTURE//	
	if(is_array($arElement["PREVIEW_PICTURE"])) {
		if($arElement["PREVIEW_PICTURE"]["WIDTH"] > $arParams["DISPLAY_IMG_WIDTH"] || $arElement["PREVIEW_PICTURE"]["HEIGHT"] > $arParams["DISPLAY_IMG_HEIGHT"]) {
			$arFileTmp = CFile::ResizeImageGet(
				$arElement["PREVIEW_PICTURE"],
				array("width" => $arParams["DISPLAY_IMG_WIDTH"], "height" => $arParams["DISPLAY_IMG_HEIGHT"]),
				BX_RESIZE_IMAGE_PROPORTIONAL,
				true
			);
			$arResult["ITEMS"][$key]["PREVIEW_PICTURE"] = array(
				"SRC" => $arFileTmp["src"],
				"WIDTH" => $arFileTmp["width"],
				"HEIGHT" => $arFileTmp["height"]
			);
		}
	} elseif(is_array($arElement["DETAIL_PICTURE"])) {
		if($arElement["DETAIL_PICTURE"]["WIDTH"] > $arParams["DISPLAY_IMG_WIDTH"] || $arElement["DETAIL_PICTURE"]["HEIGHT"] > $arParams["DISPLAY_IMG_HEIGHT"]) {
			$arFileTmp = CFile::ResizeImageGet(
				$arElement["DETAIL_PICTURE"],
				array("width" => $arParams["DISPLAY_IMG_WIDTH"], "height" => $arParams["DISPLAY_IMG_HEIGHT"]),
				BX_RESIZE_IMAGE_PROPORTIONAL,
				true
			);
			$arResult["ITEMS"][$key]["PREVIEW_PICTURE"] = array(
				"SRC" => $arFileTmp["src"],
				"WIDTH" => $arFileTmp["width"],
				"HEIGHT" => $arFileTmp["height"]
			);
		} else {
			$arResult["ITEMS"][$key]["PREVIEW_PICTURE"] = $arElement["DETAIL_PICTURE"];
		}
	}

	//MANUFACTURER//
	if(!empty($arElement["PROPERTIES"]["MANUFACTURER"]["VALUE"])) {
		$obElement = CIBlockElement::GetByID($arElement["PROPERTIES"]["MANUFACTURER"]["VALUE"]);
		if($arEl = $obElement->GetNext()) {
			$arResult["ITEMS"][$key]["PROPERTIES"]["MANUFACTURER"]["NAME"] = $arEl["NAME"];
			
			//PREVIEW_PICTURE//
			if($arEl["PREVIEW_PICTURE"] > 0) {
				$arFile = CFile::GetFileArray($arEl["PREVIEW_PICTURE"]);		
				if($arFile["WIDTH"] > 69 || $arFile["HEIGHT"] > 24) {
					$arFileTmp = CFile::ResizeImageGet(
						$arFile,
						array("width" => 69, "height" => 24),
						BX_RESIZE_IMAGE_PROPORTIONAL,
						true
					);
					$arResult["ITEMS"][$key]["PROPERTIES"]["MANUFACTURER"]["PREVIEW_PICTURE"] = array(
						"SRC" => $arFileTmp["src"],
						"WIDTH" => $arFileTmp["width"],
						"HEIGHT" => $arFileTmp["height"],
					);
				} else {
					$arResult["ITEMS"][$key]["PROPERTIES"]["MANUFACTURER"]["PREVIEW_PICTURE"] = $arFile;
				}
			}
		}
	}
	
	//OFFERS//
	if(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])) {
		//TOTAL_OFFERS//			
		$totalDiscount = array();		
		
		$minPrice = false;
		$minPrintPrice = false;
		$minDiscount = false;
		$minDiscountDiff = false;
		$minDiscountDiffPercent = false;
		$minCurr = false;		
		
		$arResult["ITEMS"][$key]["TOTAL_OFFERS"] = array();
		
		foreach($arElement["OFFERS"] as $key_off => $arOffer) {
			if($arOffer["MIN_PRICE"]["DISCOUNT_VALUE"] == 0)
				continue;

			$totalDiscount[] = $arOffer["MIN_PRICE"]["DISCOUNT_VALUE"];
			
			if($minDiscount === false || $minDiscount > $arOffer["MIN_PRICE"]["DISCOUNT_VALUE"]) {
				$minPrice = $arOffer["MIN_PRICE"]["VALUE"];			
				$minPrintPrice = $arOffer["MIN_PRICE"]["PRINT_VALUE"];
				$minDiscount = $arOffer["MIN_PRICE"]["DISCOUNT_VALUE"];
				$minDiscountDiff = $arOffer["MIN_PRICE"]["PRINT_DISCOUNT_DIFF"];
				$minDiscountDiffPercent = $arOffer["MIN_PRICE"]["DISCOUNT_DIFF_PERCENT"];
				$minCurr = $arOffer["MIN_PRICE"]["CURRENCY"];
			}
		}
		
		if(count($totalDiscount) > 0) {
			$arResult["ITEMS"][$key]["TOTAL_OFFERS"]["MIN_PRICE"] = array(
				"VALUE" => $minPrice,
				"PRINT_VALUE" => $minPrintPrice,
				"DISCOUNT_VALUE" => $minDiscount,
				"PRINT_DISCOUNT_DIFF" => $minDiscountDiff,
				"DISCOUNT_DIFF_PERCENT" => $minDiscountDiffPercent,
				"CURRENCY" => $minCurr
			);
		} else {
			$arResult["ITEMS"][$key]["TOTAL_OFFERS"]["MIN_PRICE"] = array(
				"VALUE" => "0",
				"CURRENCY" => $arElement["OFFERS"][0]["MIN_PRICE"]["CURRENCY"]
			);
		}
		
		if(count(array_unique($totalDiscount)) > 1) {
			$arResult["ITEMS"][$key]["TOTAL_OFFERS"]["FROM"] = "Y";
		} else {
			$arResult["ITEMS"][$key]["TOTAL_OFFERS"]["FROM"] = "N";
		}
		//END_TOTAL_OFFERS//
	}
	//END_OFFERS//
}?>