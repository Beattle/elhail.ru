<?define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if(!CModule::IncludeModule("sale") || !CModule::IncludeModule("catalog") || !CModule::IncludeModule("iblock"))
	return;

if($_SERVER["REQUEST_METHOD"]=="POST" && strlen($_POST["action"]) > 0 && check_bitrix_sessid()) {
	$APPLICATION->RestartBuffer();

	switch($_POST["action"]) {		
		case "ajax_recount_prices":
			if(strlen($_POST["currency"]) > 0) {
				$arPices = array("sumValue" => "", "sumCurrency" => "", "formatReferenceSum" => "", "formatOldSum" => "", "formatDiscDiffSum" => "");
				
				if($_POST["sumPrice"]) {					
					$price = CCurrencyLang::GetCurrencyFormat($_POST["currency"], "ru");
					if(empty($price["THOUSANDS_SEP"])):
						$price["THOUSANDS_SEP"] = " ";
					endif;
					if($price["HIDE_ZERO"] == "Y"):
						if(round($_POST["sumPrice"], $price["DECIMALS"]) == round($_POST["sumPrice"], 0)):
							$price["DECIMALS"] = 0;
						endif;
					endif;
					$currency = str_replace("#", " ", $price["FORMAT_STRING"]);
					
					$arPices["sumValue"] = number_format($_POST["sumPrice"], $price["DECIMALS"], $price["DEC_POINT"], $price["THOUSANDS_SEP"]);
					
					$arPices["sumCurrency"] = $currency;
				}
				if($_POST["sumReferencePrice"] && $_POST["sumReferencePrice"] != $_POST["sumPrice"]) {
					$arPices["formatReferenceSum"] = CCurrencyLang::CurrencyFormat($_POST["sumReferencePrice"], $_POST["currency"], true);
				}
				if($_POST["sumOldPrice"] && $_POST["sumOldPrice"] != $_POST["sumPrice"]) {
					$arPices["formatOldSum"] = CCurrencyLang::CurrencyFormat($_POST["sumOldPrice"], $_POST["currency"], true);
				}
				if($_POST["sumDiffDiscountPrice"]) {
					$arPices["formatDiscDiffSum"] = CCurrencyLang::CurrencyFormat($_POST["sumDiffDiscountPrice"], $_POST["currency"], true);
				}

				if(SITE_CHARSET != "utf-8") {
					$arPices = $APPLICATION->ConvertCharsetArray($arPices, SITE_CHARSET, "utf-8");
				}
				
				echo json_encode($arPices);
			}
			break;
		
		case "catalogSetAdd2Basket":
			if(is_array($_POST["set_ids"])) {
				foreach($_POST["set_ids"] as $key => $itemID) {
					$product_properties = array();

					$propCodes = array_fill_keys($_POST["setOffersCartProps"], true);					

					$sortIndex = 1;

					$rsProps = CIBlockElement::GetProperty(
						$itemID["IBLOCK_ID"],
						$itemID["ID"],
						array("sort" => "asc", "enum_sort" => "asc", "value_id" => "asc"),
						array("EMPTY" => "N")
					);
					while($oneProp = $rsProps->Fetch()) {
						if(!isset($propCodes[$oneProp['CODE']]))
							continue;						
						
						switch($oneProp["PROPERTY_TYPE"]) {
							case "S":
							case "N":
								$product_properties[] = array(
									"NAME" => $oneProp["NAME"],
									"CODE" => $oneProp["CODE"],
									"VALUE" => $oneProp["VALUE"],
									"SORT" => $sortIndex++,
								);
								break;
							case "G":
								$rsSection = CIBlockSection::GetList(
									array(),
									array("=ID" => $oneProp["VALUE"]),
									false,
									array("ID", "IBLOCK_ID", "NAME")
								);
								if($arSection = $rsSection->Fetch()) {
									$product_properties[] = array(
										"NAME" => $oneProp["NAME"],
										"CODE" => $oneProp["CODE"],
										"VALUE" => $arSection["NAME"],
										"SORT" => $sortIndex++,
									);
								}
								break;
							case "E":
								$rsElement = CIBlockElement::GetList(
									array(),
									array("=ID" => $oneProp["VALUE"]),
									false,
									false,
									array("ID", "IBLOCK_ID", "NAME")
								);
								if($arElement = $rsElement->Fetch()) {
									$product_properties[] = array(
										"NAME" => $oneProp["NAME"],
										"CODE" => $oneProp["CODE"],
										"VALUE" => $arElement["NAME"],
										"SORT" => $sortIndex++,
									);
								}
								break;
							case "L":
								$product_properties[] = array(
									"NAME" => $oneProp["NAME"],
									"CODE" => $oneProp["CODE"],
									"VALUE" => $oneProp["VALUE_ENUM"],
									"SORT" => $sortIndex++,
								);
								break;
						}
					}

					if($key == 0) {
						if(!empty($_POST["setSelectProps"])) {
							$select_props = explode("||", $_POST["setSelectProps"]);
							foreach($select_props as $arSelProp):
								$product_properties[] = unserialize(gzuncompress(stripslashes(base64_decode(strtr($arSelProp, '-_,', '+/='))))) + array("SORT" => $sortIndex++);								
							endforeach;
						}
					}
					
					$ratio = 1;
					if($_POST["itemsRatio"][$itemID["ID"]]) {
						$ratio = $_POST["itemsRatio"][$itemID["ID"]];
					}

					if(intval($itemID["ID"])) {
						$resBasket = CSaleBasket::GetList(
							array(), 
							array(
								"PRODUCT_ID" => intval($itemID["ID"]),
								"FUSER_ID" => CSaleBasket::GetBasketUserID(),
								"LID" => $_POST["lid"],
								"ORDER_ID" => "NULL",
								"DELAY" => "Y"
							), 
							false, 
							false, 
							array("ID")
						);
						if($ar = $resBasket->Fetch()) {
							CSaleBasket::Update($ar["ID"], array("QUANTITY" => $ratio, "DELAY" => "N"));
						} else {
							Add2BasketByProductID(intval($itemID["ID"]), $ratio, array("LID" => $_POST["lid"]), $product_properties);
						}
					}
				}
			}
			break;
	}
	die();
}?>