<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;

$boolCatalog = CModule::IncludeModule("catalog");

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$arIBlock = array();
$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch())
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];

$arProperty = array();
$arProperty_N = array();
$arProperty_X = array();
if (0 < intval($arCurrentValues["IBLOCK_ID"]))
{
	$rsProp = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("IBLOCK_ID"=>$arCurrentValues["IBLOCK_ID"], "ACTIVE"=>"Y"));
	while ($arr=$rsProp->Fetch())
	{
		$code = $arr["CODE"];
		$label = "[".$arr["CODE"]."] ".$arr["NAME"];

		if($arr["PROPERTY_TYPE"] != "F")
			$arProperty[$code] = $label;

		if($arr["PROPERTY_TYPE"]=="N")
			$arProperty_N[$code] = $label;

		if($arr["PROPERTY_TYPE"]!="F")
		{
			if($arr["MULTIPLE"] == "Y")
				$arProperty_X[$code] = $label;
			elseif($arr["PROPERTY_TYPE"] == "L")
				$arProperty_X[$code] = $label;
			elseif($arr["PROPERTY_TYPE"] == "E" && $arr["LINK_IBLOCK_ID"] > 0)
				$arProperty_X[$code] = $label;
		}
	}
}

$arOffers = CIBlockPriceTools::GetOffersIBlock($arCurrentValues["IBLOCK_ID"]);
$OFFERS_IBLOCK_ID = is_array($arOffers)? $arOffers["OFFERS_IBLOCK_ID"]: 0;
$arProperty_Offers = array();
$arProperty_OffersWithoutFile = array();
if($OFFERS_IBLOCK_ID)
{
	$rsProp = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("IBLOCK_ID"=>$OFFERS_IBLOCK_ID, "ACTIVE"=>"Y"));
	while($arr=$rsProp->Fetch())
	{
		$arr['ID'] = intval($arr['ID']);
		if ($arOffers['OFFERS_PROPERTY_ID'] == $arr['ID'])
			continue;
		$strPropName = '['.$arr['ID'].']'.('' != $arr['CODE'] ? '['.$arr['CODE'].']' : '').' '.$arr['NAME'];
		if ('' == $arr['CODE'])
			$arr['CODE'] = $arr['ID'];
		$arProperty_Offers[$arr["CODE"]] = $strPropName;
		if ('F' != $arr['PROPERTY_TYPE'])
			$arProperty_OffersWithoutFile[$arr["CODE"]] = $strPropName;
	}
}

$arSort = CIBlockParameters::GetElementSortFields(
	array('SHOWS', 'SORT', 'TIMESTAMP_X', 'NAME', 'ID', 'ACTIVE_FROM', 'ACTIVE_TO'),
	array('KEY_LOWERCASE' => 'Y')
);

$arPrice = array();
if ($boolCatalog)
{
	$arSort = array_merge($arSort, CCatalogIBlockParameters::GetCatalogSortFields());
	$rsPrice = CCatalogGroup::GetList($v1="sort", $v2="asc");
	while($arr=$rsPrice->Fetch()) $arPrice[$arr["NAME"]] = "[".$arr["NAME"]."] ".$arr["NAME_LANG"];
}
else
{
	$arPrice = $arProperty_N;
}

$arAscDesc = array(
	"asc" => GetMessage("IBLOCK_SORT_ASC"),
	"desc" => GetMessage("IBLOCK_SORT_DESC"),
);

$arComponentParameters = array(
	"GROUPS" => array(
		"PRICES" => array(
			"NAME" => GetMessage("IBLOCK_PRICES"),
		),
	),
	"PARAMETERS" => array(
		"IBLOCK_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("IBLOCK_TYPE"),
			"TYPE" => "LIST",
				"VALUES" => $arIBlockType,
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("IBLOCK_IBLOCK"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arIBlock,
			"REFRESH" => "Y",
		),
		"ELEMENT_SORT_FIELD" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("IBLOCK_ELEMENT_SORT_FIELD"),
			"TYPE" => "LIST",
			"VALUES" => $arSort,
			"ADDITIONAL_VALUES" => "Y",
			"DEFAULT" => "sort",
		),
		"ELEMENT_SORT_ORDER" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("IBLOCK_ELEMENT_SORT_ORDER"),
			"TYPE" => "LIST",
			"VALUES" => $arAscDesc,
			"DEFAULT" => "asc",
			"ADDITIONAL_VALUES" => "Y",
		),
		"ELEMENT_SORT_FIELD2" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("IBLOCK_ELEMENT_SORT_FIELD2"),
			"TYPE" => "LIST",
			"VALUES" => $arSort,
			"ADDITIONAL_VALUES" => "Y",
			"DEFAULT" => "id",
		),
		"ELEMENT_SORT_ORDER2" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("IBLOCK_ELEMENT_SORT_ORDER2"),
			"TYPE" => "LIST",
			"VALUES" => $arAscDesc,
			"DEFAULT" => "desc",
			"ADDITIONAL_VALUES" => "Y",
		),
		"SECTION_URL" => CIBlockParameters::GetPathTemplateParam(
			"SECTION",
			"SECTION_URL",
			GetMessage("IBLOCK_SECTION_URL"),
			"",
			"URL_TEMPLATES"
		),
		"DETAIL_URL" => CIBlockParameters::GetPathTemplateParam(
			"DETAIL",
			"DETAIL_URL",
			GetMessage("IBLOCK_DETAIL_URL"),
			"",
			"URL_TEMPLATES"
		),
		"BASKET_URL" => array(
			"PARENT" => "URL_TEMPLATES",
			"NAME" => GetMessage("IBLOCK_BASKET_URL"),
			"TYPE" => "STRING",
			"DEFAULT" => "/personal/basket.php",
		),
		"ACTION_VARIABLE" => array(
			"PARENT" => "URL_TEMPLATES",
			"NAME"		=> GetMessage("IBLOCK_ACTION_VARIABLE"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "action"
		),
		"PRODUCT_ID_VARIABLE" => array(
			"PARENT" => "URL_TEMPLATES",
			"NAME"		=> GetMessage("IBLOCK_PRODUCT_ID_VARIABLE"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "id"
		),
		"PRODUCT_QUANTITY_VARIABLE" => array(
			"PARENT" => "URL_TEMPLATES",
			"NAME" => GetMessage("CP_BCT_PRODUCT_QUANTITY_VARIABLE"),
			"TYPE" => "STRING",
			"DEFAULT" => "quantity",
		),
		"PRODUCT_PROPS_VARIABLE" => array(
			"PARENT" => "URL_TEMPLATES",
			"NAME" => GetMessage("CP_BCT_PRODUCT_PROPS_VARIABLE"),
			"TYPE" => "STRING",
			"DEFAULT" => "prop",
		),
		"SECTION_ID_VARIABLE" => array(
			"PARENT" => "URL_TEMPLATES",
			"NAME"		=> GetMessage("IBLOCK_SECTION_ID_VARIABLE"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "SECTION_ID"
		),
		"DISPLAY_COMPARE" => Array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("T_IBLOCK_DESC_DISPLAY_COMPARE"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"ELEMENT_COUNT" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("IBLOCK_ELEMENT_COUNT"),
			"TYPE" => "STRING",
			"DEFAULT" => "9",
		),
		"LINE_ELEMENT_COUNT" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("IBLOCK_LINE_ELEMENT_COUNT"),
			"TYPE" => "STRING",
			"DEFAULT" => "3",
		),
		"PROPERTY_CODE" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("IBLOCK_PROPERTY"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arProperty,
			"ADDITIONAL_VALUES" => "Y",
		),
		"PROPERTY_CODE_MOD" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("IBLOCK_PROPERTY_MOD"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arProperty,
			"ADDITIONAL_VALUES" => "Y",
		),
		"FLAG_PROPERTY_CODE" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("IBLOCK_PROPERTY_FLAG"),
			"TYPE" => "LIST",
			"MULTIPLE" => "N",
			"VALUES" => $arProperty,
			"ADDITIONAL_VALUES" => "Y",
		),
		"OFFERS_FIELD_CODE" => CIBlockParameters::GetFieldCode(GetMessage("CP_BCT_OFFERS_FIELD_CODE"), "VISUAL"),
		"OFFERS_PROPERTY_CODE" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("CP_BCT_OFFERS_PROPERTY_CODE"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arProperty_Offers,
			"ADDITIONAL_VALUES" => "Y",
		),
		"OFFERS_SORT_FIELD" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("CP_BCT_OFFERS_SORT_FIELD"),
			"TYPE" => "LIST",
			"VALUES" => $arSort,
			"ADDITIONAL_VALUES" => "Y",
			"DEFAULT" => "sort",
		),
		"OFFERS_SORT_ORDER" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("CP_BCT_OFFERS_SORT_ORDER"),
			"TYPE" => "LIST",
			"VALUES" => $arAscDesc,
			"DEFAULT" => "asc",
			"ADDITIONAL_VALUES" => "Y",
		),
		"OFFERS_SORT_FIELD2" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("CP_BCT_OFFERS_SORT_FIELD2"),
			"TYPE" => "LIST",
			"VALUES" => $arSort,
			"ADDITIONAL_VALUES" => "Y",
			"DEFAULT" => "id",
		),
		"OFFERS_SORT_ORDER2" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("CP_BCT_OFFERS_SORT_ORDER2"),
			"TYPE" => "LIST",
			"VALUES" => $arAscDesc,
			"DEFAULT" => "desc",
			"ADDITIONAL_VALUES" => "Y",
		),
		"OFFERS_LIMIT" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage('CP_BCT_OFFERS_LIMIT'),
			"TYPE" => "STRING",
			"DEFAULT" => 5,
		),
		"PRICE_CODE" => array(
			"PARENT" => "PRICES",
			"NAME" => GetMessage("IBLOCK_PRICE_CODE"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arPrice,
		),
		"USE_PRICE_COUNT" => array(
			"PARENT" => "PRICES",
			"NAME" => GetMessage("IBLOCK_USE_PRICE_COUNT"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			),
		"SHOW_PRICE_COUNT" => array(
			"PARENT" => "PRICES",
			"NAME" => GetMessage("IBLOCK_SHOW_PRICE_COUNT"),
			"TYPE" => "STRING",
			"DEFAULT" => "1"
		),
		"PRICE_VAT_INCLUDE" => array(
			"PARENT" => "PRICES",
			"NAME" => GetMessage("IBLOCK_VAT_INCLUDE"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"PRODUCT_PROPERTIES" => array(
			"PARENT" => "PRICES",
			"NAME" => GetMessage("CP_BCT_PRODUCT_PROPERTIES"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arProperty_X,
		),
		"USE_PRODUCT_QUANTITY" => array(
			"PARENT" => "PRICES",
			"NAME" => GetMessage("CP_BCT_USE_PRODUCT_QUANTITY"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y",
		),
		"CACHE_TIME"  =>  Array("DEFAULT"=>36000000),
		"CACHE_GROUPS" => array(
			"PARENT" => "CACHE_SETTINGS",
			"NAME" => GetMessage("CP_BCT_CACHE_GROUPS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
	),
);

if ($boolCatalog)
{
	$arComponentParameters["PARAMETERS"]['HIDE_NOT_AVAILABLE'] = array(
		'PARENT' => 'DATA_SOURCE',
		'NAME' => GetMessage('CP_BCT_HIDE_NOT_AVAILABLE'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
	);

	if (CModule::IncludeModule('currency'))
	{
		$arComponentParameters["PARAMETERS"]['CONVERT_CURRENCY'] = array(
			'PARENT' => 'PRICES',
			'NAME' => GetMessage('CP_BCT_CONVERT_CURRENCY'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'N',
			'REFRESH' => 'Y',
		);

		if (isset($arCurrentValues['CONVERT_CURRENCY']) && 'Y' == $arCurrentValues['CONVERT_CURRENCY'])
		{
			$arCurrencyList = array();
			$rsCurrencies = CCurrency::GetList(($by = 'SORT'), ($order = 'ASC'));
			while ($arCurrency = $rsCurrencies->Fetch())
			{
				$arCurrencyList[$arCurrency['CURRENCY']] = $arCurrency['CURRENCY'];
			}
			$arComponentParameters['PARAMETERS']['CURRENCY_ID'] = array(
				'PARENT' => 'PRICES',
				'NAME' => GetMessage('CP_BCT_CURRENCY_ID'),
				'TYPE' => 'LIST',
				'VALUES' => $arCurrencyList,
				'DEFAULT' => CCurrency::GetBaseCurrency(),
				"ADDITIONAL_VALUES" => "Y",
			);
		}
	}
}

if(!$OFFERS_IBLOCK_ID)
{
	unset($arComponentParameters["PARAMETERS"]["OFFERS_FIELD_CODE"]);
	unset($arComponentParameters["PARAMETERS"]["OFFERS_PROPERTY_CODE"]);
	unset($arComponentParameters["PARAMETERS"]["OFFERS_SORT_FIELD"]);
	unset($arComponentParameters["PARAMETERS"]["OFFERS_SORT_ORDER"]);
	unset($arComponentParameters["PARAMETERS"]["OFFERS_SORT_FIELD2"]);
	unset($arComponentParameters["PARAMETERS"]["OFFERS_SORT_ORDER2"]);
}
else
{
	$arComponentParameters["PARAMETERS"]["OFFERS_CART_PROPERTIES"] = array(
		"PARENT" => "PRICES",
		"NAME" => GetMessage("CP_BCT_OFFERS_CART_PROPERTIES"),
		"TYPE" => "LIST",
		"MULTIPLE" => "Y",
		"VALUES" => $arProperty_OffersWithoutFile,
	);
}
?>