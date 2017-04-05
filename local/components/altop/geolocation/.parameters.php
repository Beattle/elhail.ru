<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;

$arTypesEx = CIBlockParameters::GetIBlockTypes(array("-"=>" "));

$arIBlocks=array();
$db_iblock = CIBlock::GetList(array("SORT"=>"ASC"), array("SITE_ID"=>$_REQUEST["site"], "TYPE" => ($arCurrentValues["IBLOCK_TYPE"]!="-"?$arCurrentValues["IBLOCK_TYPE"]:"")));
while($arRes = $db_iblock->Fetch())
	$arIBlocks[$arRes["ID"]] = $arRes["NAME"];

$arComponentParameters = array(
	"GROUPS" => array(
		"CONFIRM_CITY_SETTINGS" => array(
			"NAME" => GetMessage("GEOLOCATION_CONFIRM_CITY_SETTINGS"),
		),
		"CHANGE_CITY_SETTINGS" => array(
			"NAME" => GetMessage("GEOLOCATION_CHANGE_CITY_SETTINGS"),
		),
	),
	"PARAMETERS" => array(
		"IBLOCK_TYPE" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("GEOLOCATION_IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arTypesEx,			
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("GEOLOCATION_IBLOCK_ID"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlocks,			
			"ADDITIONAL_VALUES" => "Y",
			"REFRESH" => "Y",
		),
		"SHOW_CONFIRM" => array(
			"PARENT" => "CONFIRM_CITY_SETTINGS",
			"NAME" => GetMessage("GEOLOCATION_SHOW_CONFIRM"),
			"TYPE" => "CHECKBOX",			
			"DEFAULT" => "Y",
		),
		"SHOW_DEFAULT_LOCATIONS" => array(
			"PARENT" => "CHANGE_CITY_SETTINGS",
			"NAME" => GetMessage("GEOLOCATION_SHOW_DEFAULT_LOCATIONS"),
			"TYPE" => "CHECKBOX",			
			"DEFAULT" => "Y",
		),
		"SHOW_TEXT_BLOCK" => array(
			"PARENT" => "CHANGE_CITY_SETTINGS",
			"NAME" => GetMessage("GEOLOCATION_SHOW_TEXT_BLOCK"),
			"TYPE" => "CHECKBOX",			
			"DEFAULT" => "Y",
			"REFRESH" => "Y",
		)
	)
);

if($arCurrentValues["SHOW_TEXT_BLOCK"] == "Y") {
	$arComponentParameters["PARAMETERS"]["SHOW_TEXT_BLOCK_TITLE"] = array(
		"PARENT" => "CHANGE_CITY_SETTINGS",
		"NAME" => GetMessage("GEOLOCATION_SHOW_TEXT_BLOCK_TITLE"),
		"TYPE" => "CHECKBOX",			
		"DEFAULT" => "Y",
		"REFRESH" => "Y",
	);
	if($arCurrentValues["SHOW_TEXT_BLOCK_TITLE"] != "N") {
		$arComponentParameters["PARAMETERS"]["TEXT_BLOCK_TITLE"] = array(
			"PARENT" => "CHANGE_CITY_SETTINGS",
			"NAME" => GetMessage("GEOLOCATION_TEXT_BLOCK_TITLE"),
			"TYPE" => "STRING",		
			"DEFAULT" => GetMessage("GEOLOCATION_TEXT_BLOCK_TITLE_DEFAULT")
		);
	}
}

$arComponentParameters["PARAMETERS"]["CACHE_TIME"] = array(
	"DEFAULT" => 36000000
);?>