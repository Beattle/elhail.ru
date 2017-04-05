<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Loader;
global $APPLICATION;

$strMainID = $this->GetEditAreaId($arResult["ELEMENT"]["ID"]);

foreach($arResult["SET_ITEMS"]["SECTIONS"] as $arSection):
	foreach($arSection["ITEMS"] as $arItem):
		if($arItem["CAN_BUY"] && $arItem["PRICE_DISCOUNT_VALUE"] <= 0):
			//ASK_PRICE//
			global $arAskPriceFilter;
			$arAskPriceFilter = array(
				"ELEMENT_ID" => $arItem["ID"],
				"ELEMENT_AREA_ID" => $strMainID."_".$arItem["ID"],
				"ELEMENT_NAME" => $arItem["NAME"],
				"BUTTON_ID" => "ask_price_anch_".$strMainID."_".$arItem["ID"],
				"HIDE_ICONS" => "Y"
			);?>
			<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/form_ask_price.php"), false, array("HIDE_ICONS" => "Y"));?>
		<?elseif(!$arItem["CAN_BUY"]):
			//UNDER_ORDER//
			global $arUnderOrderFilter;
			$arUnderOrderFilter = array(
				"ELEMENT_ID" => $arItem["ID"],
				"ELEMENT_AREA_ID" => $strMainID."_".$arItem["ID"],
				"ELEMENT_NAME" => $arItem["NAME"],
				"BUTTON_ID" => "under_order_anch_".$strMainID."_".$arItem["ID"],
				"HIDE_ICONS" => "Y"
			);?>
			<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/form_under_order.php"), false, array("HIDE_ICONS" => "Y"));?>
		<?endif;
	endforeach;
endforeach;

//SET_CURRENCIES//
$loadCurrency = Loader::includeModule("currency");

CJSCore::Init(array("currency"));?>

<script type="text/javascript">
	BX.Currency.setCurrencies(<?=$templateData["CURRENCIES"]?>);
</script>