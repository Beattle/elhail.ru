<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

foreach($arResult["ITEMS"] as $key => $arItem){
	if($arItem["CODE"] == "COLOR" && !empty($arItem["VALUES"])){
		$properties = CIBlockProperty::GetList(Array("sort"=> "asc", "name"=> "asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=> $arParams["IBLOCK_ID"], "CODE" => $arItem["CODE"]));
		while($prop_fields = $properties->GetNext()){
			$IBLOCK_ID = $prop_fields["LINK_IBLOCK_ID"];
		}
		
		foreach($arItem["VALUES"] as $val => $ar){
			$arSelect = Array("ID", "NAME", "PROPERTY_HEX", "PROPERTY_PICT");
			$arFilter = Array("IBLOCK_ID" => $IBLOCK_ID, "NAME"=> $ar["VALUE"]);
			$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
			if($ob = $res->GetNextElement()){
				$arFields = $ob->GetFields();
				$arResult["ITEMS"][$key]["VALUES"][$val]["NAME"] = $arFields["NAME"];
				$arResult["ITEMS"][$key]["VALUES"][$val]["HEX"] = $arFields["PROPERTY_HEX_VALUE"];
				$arResult["ITEMS"][$key]["VALUES"][$val]["PICT"] = CFile::ResizeImageGet($arFields["PROPERTY_PICT_VALUE"], array("width" => 24, "height" => 24), BX_RESIZE_IMAGE_PROPORTIONAL, true);
			}
		}
	}
}
?>

