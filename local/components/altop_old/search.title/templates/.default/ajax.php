<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

global $arSetting;?>

<script type="text/javascript">
	//<![CDATA[
	$(function() {
		$("a.search_close").click(function() {
			$("div.title-search-result").fadeOut(300);
		});
		$(this).keydown(function(eventObject){
			if (eventObject.which == 27)
				$("div.title-search-result").fadeOut(300);
		});
		
		/***OFFERS_LIST_PROPS***/
		<?foreach($arResult["CATEGORIES"] as $category_id => $arCategory):
			foreach($arCategory["ITEMS"] as $key => $arElement):		
				if(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])):
					if($arSetting["OFFERS_VIEW"]["VALUE"] == "LIST"):
						foreach($arElement["OFFERS"] as $key => $arOffer):?>
							$("#catalog-offer-item-search-<?=$arOffer['ID']?> .catalog-item-prop").clone().appendTo("#catalog-offer-item-search-<?=$arOffer['ID']?> .catalog-item-props-mob");
						<?endforeach;
					endif;
				endif;
			endforeach;
		endforeach;?>
		
		/***ADD2BASKET***/
		$(".add2basket_search_form").submit(function() {
			var form = $(this);

			$(".more_options_body").fadeOut(300);
			$(".more_options").fadeOut(300);

			imageItem = form.find(".item_image").attr("value");
			$("#addItemInCart .item_image_full").html(imageItem);

			titleItem = form.find(".item_title").attr("value");
			$("#addItemInCart .item_title").text(titleItem);		

			var ModalName = $("#addItemInCart");
			CentriredModalWindow(ModalName);
			OpenModalWindow(ModalName);

			$.post($(this).attr("action"), $(this).serialize(), function(data) {
				try {
					$.post("<?=SITE_DIR?>ajax/basket_line.php", function(data) {
						refreshCartLine(data);
					});
					$.post("<?=SITE_DIR?>ajax/delay_line.php", function(data) {
						$(".delay_line").replaceWith(data);
					});
					form.children(".btn_buy").addClass("hidden");
					form.children(".result").removeClass("hidden");
				} catch (e) {}
			});
			return false;
		});
	});
	//]]>
</script>

<?if(!empty($arResult["CATEGORIES"])):?>
	<a href="javascript:void(0)" class="pop-up-close search_close"><i class="fa fa-times"></i></a>		
	<div id="catalog_search">
		<?foreach($arResult["CATEGORIES"] as $category_id => $arCategory):?>
			<?foreach($arCategory["ITEMS"] as $i => $arItem):				
				
				$strMainID = $this->GetEditAreaId($arItem["ITEM_ID"]);
				$arItemIDs = array(
					"ID" => $strMainID."_search"
				);
						
				if($category_id === "all"):
					if($arParams["SHOW_ALL_RESULTS"]=="Y"):?>
						<a class="search_all" href="<?=$arItem['URL']?>"><?=$arItem["NAME"]?></a>
					<?endif;
				elseif(isset($arItem["ICON"])):?>
					<div class="tvr_search">						
						<a class="image" href="<?=$arItem['URL']?>">
							<?if(!empty($arItem["PICTURE"]["SRC"])):?>
								<img src="<?=$arItem['PICTURE']['SRC']?>" width="<?=$arItem['PICTURE']['WIDTH']?>" height="<?=$arItem['PICTURE']['HEIGHT']?>" alt="<?=$arItem['NAME']?>" />
							<?elseif(!empty($arItem["PREVIEW_PICTURE"]["SRC"])):?>
								<img src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" width="<?=$arItem['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$arItem['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=$arItem['NAME']?>" />
							<?else:?>
								<img src="<?=SITE_TEMPLATE_PATH?>/images/no-photo.jpg" width="62" height="62" alt="<?=$arItem['NAME']?>" />
							<?endif?>
						</a>						
						
						<div class="<?if(!empty($arItem['PRICES']) || !empty($arItem['TOTAL_OFFERS']['MIN_PRICE'])): echo 'item_'; else: echo 'cat_'; endif;?>title">
							<?if(!empty($arItem["PROPERTIES"]["ARTNUMBER"]["VALUE"])):?>
								<span class="article"><?=GetMessage("CATALOG_ELEMENT_ARTNUMBER").$arItem["PROPERTIES"]["ARTNUMBER"]["VALUE"];?></span>
							<?endif;?>
							<a href="<?=$arItem['URL']?>"><?=$arItem["NAME"]?></a>
							<?if(!empty($arItem["DISPLAY_PROPERTIES"])):?>
								<div class="properties">
									<?foreach($arItem["DISPLAY_PROPERTIES"] as $k => $v):?>
										<span class="property"><?=$v["NAME"].": ".strip_tags($v["DISPLAY_VALUE"])?></span>
									<?endforeach;?>
								</div>
							<?endif;?>
						</div>
						
						<?/***PRICE***/
						if($arParams["SHOW_PRICE"]=="Y"):
							if(isset($arItem["OFFERS"]) && !empty($arItem["OFFERS"])):
								$price = CCurrencyLang::GetCurrencyFormat($arItem["TOTAL_OFFERS"]["MIN_PRICE"]["CURRENCY"], "ru");
								if(empty($price["THOUSANDS_SEP"])):
									$price["THOUSANDS_SEP"] = " ";
								endif;
								$price["REFERENCE_DECIMALS"] = $price["DECIMALS"];
								if($price["HIDE_ZERO"] == "Y"):
									if($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"])):
										if(round($arItem["TOTAL_OFFERS"]["MIN_PRICE"]["DISCOUNT_VALUE"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $price["DECIMALS"]) == round($arItem["TOTAL_OFFERS"]["MIN_PRICE"]["DISCOUNT_VALUE"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], 0)):
											$price["REFERENCE_DECIMALS"] = 0;
										endif;
									endif;
									if(round($arItem["TOTAL_OFFERS"]["MIN_PRICE"]["DISCOUNT_VALUE"], $price["DECIMALS"]) == round($arItem["TOTAL_OFFERS"]["MIN_PRICE"]["DISCOUNT_VALUE"], 0)):
										$price["DECIMALS"] = 0;
									endif;
								endif;
								$currency = str_replace("#", " ", $price["FORMAT_STRING"]);?>

								<div class="search_price">
									<?if($arItem["TOTAL_OFFERS"]["MIN_PRICE"]["DISCOUNT_VALUE"] <= 0):?>
										<span class="no-price">											
											<span class="unit">
												<?=GetMessage("CATALOG_ELEMENT_NO_PRICE")?>
												<br />
												<span><?=(!empty($arItem["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_MEASURE_NAME"])) ? GetMessage("CATALOG_ELEMENT_UNIT")." ".$arItem["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_MEASURE_NAME"] : "";?></span>
											</span>
										</span>													
									<?else:?>										
										<span class="price">
											<?=($arItem["TOTAL_OFFERS"]["FROM"] == "Y") ? "<span class='from'>".GetMessage("CATALOG_ELEMENT_FROM")."</span>" : "";?>
											<?=number_format($arItem["TOTAL_OFFERS"]["MIN_PRICE"]["DISCOUNT_VALUE"], $price["DECIMALS"], $price["DEC_POINT"], $price["THOUSANDS_SEP"]);
											if($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"])):?>
												<span class="price-reference">
													<?=number_format($arItem["TOTAL_OFFERS"]["MIN_PRICE"]["DISCOUNT_VALUE"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $price["REFERENCE_DECIMALS"], $price["DEC_POINT"], $price["THOUSANDS_SEP"]);?>
												</span>
											<?endif;?>
											<span class="unit">												
												<?=$currency?>
												<span><?=(!empty($arItem["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_MEASURE_NAME"])) ? GetMessage("CATALOG_ELEMENT_UNIT")." ".$arItem["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_MEASURE_NAME"] : "";?></span>
											</span>											
										</span>									
									<?endif;?>
								</div>
							<?else:
								foreach($arItem["PRICES"] as $code=>$arPrice):
									if($arPrice["MIN_PRICE"] == "Y"):
										if($arPrice["CAN_ACCESS"]):
											
											$price = CCurrencyLang::GetCurrencyFormat($arPrice["CURRENCY"], "ru");
											if(empty($price["THOUSANDS_SEP"])):
												$price["THOUSANDS_SEP"] = " ";
											endif;
											$price["REFERENCE_DECIMALS"] = $price["DECIMALS"];
											if($price["HIDE_ZERO"] == "Y"):
												if($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"])):
													if(round($arPrice["DISCOUNT_VALUE"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $price["DECIMALS"]) == round($arPrice["DISCOUNT_VALUE"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], 0)):
														$price["REFERENCE_DECIMALS"] = 0;													
													endif;
												endif;
												if(round($arPrice["DISCOUNT_VALUE"], $price["DECIMALS"]) == round($arPrice["DISCOUNT_VALUE"], 0)):
													$price["DECIMALS"] = 0;
												endif;
											endif;
											$currency = str_replace("#", " ", $price["FORMAT_STRING"]);?>

											<div class="search_price">
												<?if($arPrice["DISCOUNT_VALUE"] <= 0):
													$arItem["ASK_PRICE"]=1;?>
													<span class="no-price">
														<span class="unit">
															<?=GetMessage("CATALOG_ELEMENT_NO_PRICE")?>
															<br />
															<span><?=(!empty($arItem["CATALOG_MEASURE_NAME"])) ? GetMessage("CATALOG_ELEMENT_UNIT")." ".$arItem["CATALOG_MEASURE_NAME"] : "";?></span>
														</span>
													</span>																
												<?else:?>													
													<span class="price">
														<?=number_format($arPrice["DISCOUNT_VALUE"], $price["DECIMALS"], $price["DEC_POINT"], $price["THOUSANDS_SEP"]);
														if($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"])):?>
															<span class="price-reference">
																<?=number_format($arPrice["DISCOUNT_VALUE"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $price["REFERENCE_DECIMALS"], $price["DEC_POINT"], $price["THOUSANDS_SEP"]);?>
															</span>
														<?endif;?>
														<span class="unit">
															<?=$currency?>
															<span><?=(!empty($arItem["CATALOG_MEASURE_NAME"])) ? GetMessage("CATALOG_ELEMENT_UNIT")." ".$arItem["CATALOG_MEASURE_NAME"] : "";?></span>
														</span>														
													</span>
												<?endif;?>
											</div>
										
										<?endif;
									endif;
								endforeach;
							endif;
						endif;
						
						/***BUY***/
						if($arParams["SHOW_ADD_TO_CART"]=="Y"):
							if(isset($arItem["OFFERS"]) && !empty($arItem["OFFERS"])):?>
								<div class="buy_more">
									<div class="add2basket_block">													
										<script type="text/javascript">
											$(function() {
												$("#add2basket_offer_search_form_<?=$arItem['ITEM_ID']?>").submit(function() {
													var form = $(this);
													$(window).resize(function () {
														modalHeight = $(window).height()/2 - $("#<?=$arItemIDs['ID']?>").height()/2 + $(window).scrollTop() - $(".title-search-result").offset().top;
														$("#<?=$arItemIDs['ID']?>").css({
															"top": modalHeight + "px"
														});
													});
													$(window).resize();
													$("#<?=$arItemIDs['ID']?>_body").fadeIn(300);
													$("#<?=$arItemIDs['ID']?>").fadeIn(300);
															
													quantityItem = form.find("#quantity_search_<?=$arItem['ITEM_ID']?>").attr("value");
													$("#<?=$arItemIDs['ID']?> .quantity").attr("value", quantityItem);
													return false;
												});
												$("#<?=$arItemIDs['ID']?>_close, #<?=$arItemIDs['ID']?>_body").click(function(e){
													e.preventDefault();
													$("#<?=$arItemIDs['ID']?>_body").fadeOut(300);
													$("#<?=$arItemIDs['ID']?>").fadeOut(300);
												});
											});
										</script>
										<form action="<?=$APPLICATION->GetCurPage()?>" id="add2basket_offer_search_form_<?=$arItem['ITEM_ID']?>">
											<a href="javascript:void(0)" class="minus" onclick="if (BX('quantity_search_<?=$arItem["ITEM_ID"]?>').value > <?=$arItem["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_MEASURE_RATIO"]?>) BX('quantity_search_<?=$arItem["ITEM_ID"]?>').value = parseFloat(BX('quantity_search_<?=$arItem["ITEM_ID"]?>').value)-<?=$arItem["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_MEASURE_RATIO"]?>;"><span>-</span></a>
											<input type="text" id="quantity_search_<?=$arItem['ITEM_ID']?>" name="quantity" class="quantity" value="<?=$arItem['TOTAL_OFFERS']['MIN_PRICE']['CATALOG_MEASURE_RATIO']?>"/>
											<a href="javascript:void(0)" class="plus" onclick="BX('quantity_search_<?=$arItem["ITEM_ID"]?>').value = parseFloat(BX('quantity_search_<?=$arItem["ITEM_ID"]?>').value)+<?=$arItem["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_MEASURE_RATIO"]?>;"><span>+</span></a>			
											<button type="submit" name="add2basket" class="btn_buy" value="<?=GetMessage('CATALOG_ELEMENT_ADD_TO_CART')?>"><i class="fa fa-shopping-cart"></i></button>
										</form>
									</div>
								</div>
							<?else:
								if(isset($arItem["SELECT_PROPS"]) && !empty($arItem["SELECT_PROPS"])):?>
									<script type="text/javascript">
										$(function() {
											$("#add2basket_search_select_form_<?=$arItem['ITEM_ID']?>").submit(function() {
												var form = $(this);
												$(window).resize(function () {
													modalHeight = $(window).height()/2 - $("#<?=$arItemIDs['ID']?>").height()/2 + $(window).scrollTop() - $(".title-search-result").offset().top;
													$("#<?=$arItemIDs['ID']?>").css({
														"top": modalHeight + "px"
													});
												});
												$(window).resize();
												$("#<?=$arItemIDs['ID']?>_body").fadeIn(300);
												$("#<?=$arItemIDs['ID']?>").fadeIn(300);
																
												quantityItem = form.find("#quantity_search_<?=$arItem['ITEM_ID']?>").attr("value");
												$("#<?=$arItemIDs['ID']?> .quantity").attr("value", quantityItem);
												return false;
											});
											$("#<?=$arItemIDs['ID']?>_close, #<?=$arItemIDs['ID']?>_body").click(function(e){
												e.preventDefault();
												$("#<?=$arItemIDs['ID']?>_body").fadeOut(300);
												$("#<?=$arItemIDs['ID']?>").fadeOut(300);
											});
										});
									</script>
								<?endif;														
								if($arItem["CAN_BUY"]):?>
									<div class="buy_more">
										<div class="add2basket_block">
											<?if($arItem["ASK_PRICE"]):?>
												<a class="btn_buy apuo" id="ask_price_anch_search_<?=$arItem['ITEM_ID']?>" href="javascript:void(0)" rel="nofollow"><i class="fa fa-comment-o"></i><span><?=GetMessage("CATALOG_ELEMENT_ASK_PRICE_SHORT")?></span></a>	
												<?$APPLICATION->IncludeComponent("altop:ask.price", "",
													Array(
														"ELEMENT_ID" => "search_".$arItem["ITEM_ID"],		
														"ELEMENT_NAME" => strip_tags($arItem["NAME"]),
														"EMAIL_TO" => "",				
														"REQUIRED_FIELDS" => array("NAME", "EMAIL", "TEL"),
													),
													false
												);?>
											<?else:?>
												<?if(isset($arItem["SELECT_PROPS"]) && !empty($arItem["SELECT_PROPS"])):?>
													<form action="<?=$APPLICATION->GetCurPage()?>" id="add2basket_search_select_form_<?=$arItem['ITEM_ID']?>">
												<?else:?>
													<form action="<?=SITE_DIR?>ajax/add2basket.php" class="add2basket_search_form">
												<?endif;?>
													<a href="javascript:void(0)" class="minus" onclick="if (BX('quantity_search_<?=$arItem["ITEM_ID"]?>').value > <?=$arItem["CATALOG_MEASURE_RATIO"]?>) BX('quantity_search_<?=$arItem["ITEM_ID"]?>').value = parseFloat(BX('quantity_search_<?=$arItem["ITEM_ID"]?>').value)-<?=$arItem["CATALOG_MEASURE_RATIO"]?>;"><span>-</span></a>
													<input type="text" id="quantity_search_<?=$arItem['ITEM_ID']?>" name="quantity" class="quantity" value="<?=$arItem['CATALOG_MEASURE_RATIO']?>"/>
													<a href="javascript:void(0)" class="plus" onclick="BX('quantity_search_<?=$arItem["ITEM_ID"]?>').value = parseFloat(BX('quantity_search_<?=$arItem["ITEM_ID"]?>').value)+<?=$arItem["CATALOG_MEASURE_RATIO"]?>;"><span>+</span></a>
													<?if(!isset($arItem["SELECT_PROPS"]) || empty($arItem["SELECT_PROPS"])):?>
														<input type="hidden" name="ID" value="<?=$arItem['ITEM_ID']?>"/>
														<?$props = array();
														if(!empty($arItem["PROPERTIES"]["ARTNUMBER"]["VALUE"])):			
															$props[] = array(
																"NAME" => $arItem["PROPERTIES"]["ARTNUMBER"]["NAME"],
																"CODE" => $arItem["PROPERTIES"]["ARTNUMBER"]["CODE"],
																"VALUE" => $arItem["PROPERTIES"]["ARTNUMBER"]["VALUE"]
															);												
														endif;
														if(!empty($arItem["DISPLAY_PROPERTIES"])):										
															foreach($arItem["DISPLAY_PROPERTIES"] as $propOffer) {
																$props[] = array(
																	"NAME" => $propOffer["NAME"],
																	"CODE" => $propOffer["CODE"],
																	"VALUE" => strip_tags($propOffer["DISPLAY_VALUE"])
																);
															}
														endif;
														$props = !empty($props) ? strtr(base64_encode(addslashes(gzcompress(serialize($props),9))), '+/=', '-_,') : "";?>
														<input type="hidden" name="PROPS" value="<?=$props?>" />
														<input type="hidden" name="item_image" class="item_image" value="&lt;img class='item_image' src='<?=$arItem["PICTURE_150"]["SRC"]?>' alt='<?=strip_tags($arItem["NAME"])?>'/&gt;"/>
														<input type="hidden" name="item_title" class="item_title" value="<?=strip_tags($arItem['NAME']);?>"/>						
													<?endif;?>															
													<button type="submit" name="add2basket" class="btn_buy" value="<?=GetMessage('CATALOG_ELEMENT_ADD_TO_CART')?>"><i class="fa fa-shopping-cart"></i></button>
													<?if(!isset($arItem["SELECT_PROPS"]) || empty($arItem["SELECT_PROPS"])):?>
														<small class="result hidden"><i class="fa fa-check"></i></small>
													<?endif;?>
												</form>
											<?endif;?>
										</div>
									</div>
								<?elseif(!$arItem["CAN_BUY"]):
									if(!empty($arItem["PRICES"])):?>
										<div class="buy_more">
											<div class="add2basket_block">
												<a class="btn_buy apuo" id="order_anch_search_<?=$arItem['ITEM_ID']?>" href="javascript:void(0)" rel="nofollow"><i class="fa fa-clock-o"></i><span><?=GetMessage("CATALOG_ELEMENT_UNDER_ORDER")?></span></a>
												<?$APPLICATION->IncludeComponent("altop:ask.price", "order",
													Array(
														"ELEMENT_ID" => "search_".$arItem["ITEM_ID"],		
														"ELEMENT_NAME" => strip_tags($arItem["NAME"]),
														"EMAIL_TO" => "",				
														"REQUIRED_FIELDS" => array("NAME", "TEL", "TIME"),
													),
													false,
													array("HIDE_ICONS" => "Y")
												);?>
											</div>
										</div>
									<?endif;												
								endif;
							endif;
						endif;?>										
					</div>							
				<?endif;
			endforeach;
		endforeach;?>
	</div>			
	
	<?/***OFFERS***/
	foreach($arResult["CATEGORIES"] as $category_id => $arCategory):
		foreach($arCategory["ITEMS"] as $key => $arElement):
			if((isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])) || (isset($arElement["SELECT_PROPS"]) && !empty($arElement["SELECT_PROPS"]))):
				$strMainID = $this->GetEditAreaId($arElement["ITEM_ID"]);
				$arItemIDs = array(
					"ID" => $strMainID."_search",
					"PICT" => $strMainID."_search_picture",
					"PRICE" => $strMainID."_search_price",
					"BUY" => $strMainID."_search_buy",
					"PROP_DIV" => $strMainID."_search_sku_tree",
					"PROP" => $strMainID."_search_prop_",
					"SELECT_PROP_DIV" => $strMainID."_search_propdiv",
					"SELECT_PROP" => $strMainID."_search_select_prop_"
				);
				$strObName = "ob".preg_replace("/[^a-zA-Z0-9_]/", "x", $strMainID)."_search";?>

				<div class="pop-up-bg more_options_body" id="<?=$arItemIDs['ID']?>_body"></div>
				<div class="pop-up more_options<?=(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])) ? (($arSetting['OFFERS_VIEW']['VALUE'] == 'LIST') ? ' offers-list' : '') : '';?>" id="<?=$arItemIDs['ID']?>">
					<a href="javascript:void(0)" class="pop-up-close more_options_close" id="<?=$arItemIDs['ID']?>_close"><i class="fa fa-times"></i></a>
					<div class="h1"><?=GetMessage("CATALOG_ELEMENT_MORE_OPTIONS")?></div>
					<div class="item_info">
						<div class="item_image" id="<?=$arItemIDs['PICT']?>">
							<?if(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])):
								if($arSetting["OFFERS_VIEW"]["VALUE"] != "LIST"):
									foreach($arElement["OFFERS"] as $key_off => $arOffer):?>
										<div id="img_search_<?=$arElement['ITEM_ID']?>_<?=$arOffer['ID']?>" class="img <?=$arElement['ITEM_ID']?> hidden">
											<?if(isset($arOffer["PREVIEW_IMG"])):?>
												<img src="<?=$arOffer['PREVIEW_IMG']['SRC']?>" width="<?=$arOffer['PREVIEW_IMG']['WIDTH']?>" height="<?=$arOffer['PREVIEW_IMG']['HEIGHT']?>" alt="<?=strip_tags($arElement['NAME']);?>"/>
											<?else:?>
												<img src="<?=$arElement['PICTURE_150']['SRC']?>" width="<?=$arElement['PICTURE_150']['WIDTH']?>" height="<?=$arElement['PICTURE_150']['HEIGHT']?>" alt="<?=strip_tags($arElement['NAME']);?>"/>
											<?endif;?>
										</div>
									<?endforeach;
								endif;
							endif;
							if(!isset($arElement["OFFERS"]) || empty($arElement["OFFERS"]) || $arSetting["OFFERS_VIEW"]["VALUE"] == "LIST"):?>
								<div class="img">
									<?if(isset($arElement["PICTURE_150"])):?>
										<img src="<?=$arElement["PICTURE_150"]["SRC"]?>" width="<?=$arElement["PICTURE_150"]["WIDTH"]?>" height="<?=$arElement["PICTURE_150"]["HEIGHT"]?>" alt="<?=$arElement["NAME"]?>"/>
									<?else:?>
										<img src="<?=SITE_TEMPLATE_PATH?>/images/no-photo.jpg" width="150" height="150" alt="<?=$arElement['NAME']?>" />
									<?endif;?>
								</div>
							<?endif;?>
							<div class="item_name">
								<?=strip_tags($arElement["NAME"]);?>
							</div>
						</div>
						<div class="item_block<?=($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"]) ? ' reference' : '').(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"]) ? ($arSetting['OFFERS_VIEW']['VALUE'] == 'LIST' ? ' offers-list' : '') : '');?>">							
							<?/***OFFERS_PROPS***/
							if($arSetting["OFFERS_VIEW"]["VALUE"] != "LIST"):
								if(!empty($arElement["OFFERS_PROP"])):?>
									<table class="offer_block" id="<?=$arItemIDs['PROP_DIV'];?>">
										<?$arSkuProps = array();
										foreach($arResult["SKU_PROPS"] as &$arProp) {
											if(!isset($arElement["OFFERS_PROP"][$arProp["CODE"]]))
												continue;
											$arSkuProps[] = array(
												"ID" => $arProp["ID"],
												"SHOW_MODE" => $arProp["SHOW_MODE"]
											);?>
											<tr class="<?=$arProp['CODE']?>" id="<?=$arItemIDs['PROP'].$arProp['ID'];?>_cont">
												<td class="h3">
													<?=htmlspecialcharsex($arProp["NAME"]);?>:
												</td>
												<td class="props">
													<ul id="<?=$arItemIDs['PROP'].$arProp['ID'];?>_list" class="<?=$arProp['CODE']?><?=$arProp['SHOW_MODE'] == 'PICT' ? ' COLOR' : '';?>">
														<?foreach($arProp["VALUES"] as $arOneValue) {
															$arOneValue["NAME"] = htmlspecialcharsbx($arOneValue["NAME"]);?>
															<li data-treevalue="<?=$arProp['ID'].'_'.$arOneValue['ID'];?>" data-onevalue="<?=$arOneValue['ID'];?>" style="display:none;">
																<span title="<?=$arOneValue['NAME'];?>">
																	<?if("TEXT" == $arProp["SHOW_MODE"]) {
																		echo $arOneValue["NAME"];
																	} elseif("PICT" == $arProp["SHOW_MODE"]) {
																		if(!empty($arOneValue["PICT"]["src"])):?>
																			<img src="<?=$arOneValue['PICT']['src']?>" width="<?=$arOneValue['PICT']['width']?>" height="<?=$arOneValue['PICT']['height']?>" alt="<?=$arOneValue['NAME']?>" />
																		<?else:?>
																			<i style="background:#<?=$arOneValue['HEX']?>"></i>
																		<?endif;
																	}?>
																</span>
															</li>
														<?}?>
													</ul>
													<div class="bx_slide_left" style="display:none;" id="<?=$arItemIDs['PROP'].$arProp['ID']?>_left" data-treevalue="<?=$arProp['ID']?>"></div>
													<div class="bx_slide_right" style="display:none;" id="<?=$arItemIDs['PROP'].$arProp['ID']?>_right" data-treevalue="<?=$arProp['ID']?>"></div>
													<div class="clr"></div>
												</td>
											</tr>
										<?}
										unset($arProp);?>
									</table>
								<?endif;
							endif;

							/***SELECT_PROPS***/
							if(!empty($arElement["SELECT_PROPS"])):?>
								<table class="offer_block" id="<?=$arItemIDs['SELECT_PROP_DIV'];?>">
									<?$arSelProps = array();
									foreach($arElement["SELECT_PROPS"] as $key_prop => &$arProp):
										$arSelProps[] = array(
											"ID" => $arProp["ID"]
										);?>
										<tr class="<?=$arProp['CODE']?>" id="<?=$arItemIDs['SELECT_PROP'].$arProp['ID'];?>">
											<td class="h3"><?=htmlspecialcharsex($arProp["NAME"]);?></td>
											<td class="props">												
												<ul class="<?=$arProp['CODE']?>">
													<?$props = array();
													foreach($arProp["DISPLAY_VALUE"] as $arOneValue) {
														$props[$key_prop] = array(
															"NAME" => $arProp["NAME"],
															"CODE" => $arProp["CODE"],
															"VALUE" => strip_tags($arOneValue)
														);
														$props[$key_prop] = strtr(base64_encode(addslashes(gzcompress(serialize($props[$key_prop]),9))), '+/=', '-_,');?>
														<li data-select-onevalue="<?=$props[$key_prop]?>">
															<span title="<?=$arOneValue;?>"><?=$arOneValue?></span>
														</li>
													<?}?>
												</ul>
												<div class="clr"></div>
											</td>
										</tr>
									<?endforeach;
									unset($arProp);?>
								</table>
							<?endif;

							if(!isset($arElement["OFFERS"]) || empty($arElement["OFFERS"]) || $arSetting["OFFERS_VIEW"]["VALUE"] != "LIST"):?>								
								<div class="catalog_price" id="<?=$arItemIDs['PRICE'];?>">
									<?/***PRICE***/
									if(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])):
										foreach($arElement["OFFERS"] as $key_off => $arOffer):?>
											<div id="price_search_<?=$arElement['ITEM_ID']?>_<?=$arOffer['ID']?>" class="price <?=$arElement['ITEM_ID']?> hidden">
												<?foreach($arOffer["PRICES"] as $code => $arPrice):
													if($arPrice["MIN_PRICE"] == "Y"):
														if($arPrice["CAN_ACCESS"]):
															
															$price = CCurrencyLang::GetCurrencyFormat($arPrice["CURRENCY"], "ru");
															if(empty($price["THOUSANDS_SEP"])):
																$price["THOUSANDS_SEP"] = " ";
															endif;															
															if($price["HIDE_ZERO"] == "Y"):																
																if(round($arPrice["DISCOUNT_VALUE"], $price["DECIMALS"]) == round($arPrice["DISCOUNT_VALUE"], 0)):
																	$price["DECIMALS"] = 0;
																endif;
															endif;
															$currency = str_replace("#", " ", $price["FORMAT_STRING"]);

															if($arPrice["DISCOUNT_VALUE"] <= 0):
																$arElement["OFFERS"][$key_off]["ASK_PRICE"] = 1;?>
																<span class="no-price">
																	<?=GetMessage("CATALOG_ELEMENT_NO_PRICE")?>
																	<?=(!empty($arOffer["CATALOG_MEASURE_NAME"])) ? GetMessage("CATALOG_ELEMENT_UNIT")." ".$arOffer["CATALOG_MEASURE_NAME"] : "";?>
																</span>														
															<?else:
																if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>
																	<span class="price-old">
																		<?=$arPrice["PRINT_VALUE"];?>
																	</span>
																	<span class="price-percent">
																		<?=GetMessage("CATALOG_ELEMENT_SKIDKA")." ".$arPrice["PRINT_DISCOUNT_DIFF"];?>
																	</span>
																<?endif;?>
																<span class="price-normal">
																	<?=number_format($arPrice["DISCOUNT_VALUE"], $price["DECIMALS"], $price["DEC_POINT"], $price["THOUSANDS_SEP"]);?>
																	<span class="unit">
																		<?=$currency?>
																		<?=(!empty($arOffer["CATALOG_MEASURE_NAME"])) ? GetMessage("CATALOG_ELEMENT_UNIT")." ".$arOffer["CATALOG_MEASURE_NAME"] : "";?>
																	</span>
																</span>
																<?if($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"])):?>
																	<span class="price-reference">
																		<?=CCurrencyLang::CurrencyFormat($arPrice["DISCOUNT_VALUE"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $arPrice["CURRENCY"], true);?>
																	</span>
																<?endif;
															endif;													
														endif;
													endif;
												endforeach;?>
												<div class="available">
													<?if($arOffer["CAN_BUY"]):?>													
														<div class="avl">
															<i class="fa fa-check-circle"></i>
															<span>
																<?=GetMessage("CATALOG_ELEMENT_AVAILABLE");
																if($arOffer["CATALOG_QUANTITY_TRACE"] == "Y"):
																	if(in_array("PRODUCT_QUANTITY", $arSetting["GENERAL_SETTINGS"]["VALUE"])):
																		echo " ".$arOffer["CATALOG_QUANTITY"];
																	endif;
																endif;?>
															</span>
														</div>
													<?elseif(!$arOffer["CAN_BUY"]):?>												
														<div class="not_avl">
															<i class="fa fa-times-circle"></i>
															<span><?=GetMessage("CATALOG_ELEMENT_NOT_AVAILABLE")?></span>
														</div>
													<?endif;?>
												</div>
											</div>
										<?endforeach;
									else:
										foreach($arElement["PRICES"] as $code => $arPrice):
											if($arPrice["MIN_PRICE"] == "Y"):
												if($arPrice["CAN_ACCESS"]):
																				
													$price = CCurrencyLang::GetCurrencyFormat($arPrice["CURRENCY"], "ru");
													if(empty($price["THOUSANDS_SEP"])):
														$price["THOUSANDS_SEP"] = " ";
													endif;													
													if($price["HIDE_ZERO"] == "Y"):														
														if(round($arPrice["DISCOUNT_VALUE"], $price["DECIMALS"]) == round($arPrice["DISCOUNT_VALUE"], 0)):
															$price["DECIMALS"] = 0;
														endif;
													endif;
													$currency = str_replace("#", " ", $price["FORMAT_STRING"]);

													if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>
														<span class="price-old">
															<?=$arPrice["PRINT_VALUE"];?>
														</span>
														<span class="price-percent">
															<?=GetMessage("CATALOG_ELEMENT_SKIDKA")." ".$arPrice["PRINT_DISCOUNT_DIFF"];?>
														</span>
													<?endif;?>
													<span class="price-normal">
														<?=number_format($arPrice["DISCOUNT_VALUE"], $price["DECIMALS"], $price["DEC_POINT"], $price["THOUSANDS_SEP"]);?>
														<span class="unit">
															<?=$currency?>
															<?=(!empty($arElement["CATALOG_MEASURE_NAME"])) ? GetMessage("CATALOG_ELEMENT_UNIT")." ".$arElement["CATALOG_MEASURE_NAME"] : "";?>
														</span>
													</span>
													<?if($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"])):?>
														<span class="price-reference">
															<?=CCurrencyLang::CurrencyFormat($arPrice["DISCOUNT_VALUE"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $arPrice["CURRENCY"], true);?>
														</span>
													<?endif;
												endif;
											endif;
										endforeach;?>
										<div class="available">
											<?if($arElement["CAN_BUY"]):?>												
												<div class="avl">
													<i class="fa fa-check-circle"></i>
													<span>
														<?=GetMessage("CATALOG_ELEMENT_AVAILABLE");
														if($arElement["CATALOG_QUANTITY_TRACE"] == "Y"):
															if(in_array("PRODUCT_QUANTITY", $arSetting["GENERAL_SETTINGS"]["VALUE"])):
																echo " ".$arElement["CATALOG_QUANTITY"];
															endif;
														endif;?>
													</span>
												</div>
											<?elseif(!$arElement["CAN_BUY"]):?>												
												<div class="not_avl">
													<i class="fa fa-times-circle"></i>
													<span><?=GetMessage("CATALOG_ELEMENT_NOT_AVAILABLE")?></span>
												</div>
											<?endif;?>
										</div>
									<?endif;?>
								</div>

								<div class="catalog_buy_more" id="<?=$arItemIDs['BUY'];?>">
									<?/***BUY***/
									if(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])):
										foreach($arElement["OFFERS"] as $key_off => $arOffer):?>
											<div id="buy_more_search_<?=$arElement['ITEM_ID']?>_<?=$arOffer['ID']?>" class="buy_more <?=$arElement['ITEM_ID']?> hidden">
												<?if($arOffer["CAN_BUY"]):
													if($arOffer["ASK_PRICE"]):?>
														<a class="btn_buy apuo" id="ask_price_anch_search_<?=$arOffer['ID']?>" href="javascript:void(0)" rel="nofollow"><i class="fa fa-comment-o"></i><span><?=GetMessage("CATALOG_ELEMENT_ASK_PRICE_FULL")?></span></a>
														<?$properties = false;
														foreach($arOffer["DISPLAY_PROPERTIES"] as $propOffer) {
															$properties[] = $propOffer["NAME"].": ".strip_tags($propOffer["DISPLAY_VALUE"]);
														}
														$properties = implode("; ", $properties);
														if(!empty($properties)):
															$offer_name = strip_tags($arElement["NAME"])." (".$properties.")";
														else:
															$offer_name = strip_tags($arElement["NAME"]);
														endif;?>
														<?$APPLICATION->IncludeComponent("altop:ask.price", "",
															Array(
																"ELEMENT_ID" => "search_".$arOffer["ID"],		
																"ELEMENT_NAME" => $offer_name,
																"EMAIL_TO" => "",				
																"REQUIRED_FIELDS" => array("NAME", "TEL", "TIME")
															),
															false,
															array("HIDE_ICONS" => "Y")
														);?>
													<?elseif(!$arOffer["ASK_PRICE"]):?>												
														<div class="add2basket_block">
															<form action="<?=SITE_DIR?>ajax/add2basket.php" class="add2basket_search_form">
																<div class="qnt_cont">
																	<a href="javascript:void(0)" class="minus" onclick="if (BX('quantity_search_<?=$arOffer["ID"]?>').value > <?=$arOffer["CATALOG_MEASURE_RATIO"]?>) BX('quantity_search_<?=$arOffer["ID"]?>').value = parseFloat(BX('quantity_search_<?=$arOffer["ID"]?>').value)-<?=$arOffer["CATALOG_MEASURE_RATIO"]?>;"><span>-</span></a>
																	<input type="text" id="quantity_search_<?=$arOffer['ID']?>" name="quantity" class="quantity" value="<?=$arOffer['CATALOG_MEASURE_RATIO']?>"/>
																	<a href="javascript:void(0)" class="plus" onclick="BX('quantity_search_<?=$arOffer["ID"]?>').value = parseFloat(BX('quantity_search_<?=$arOffer["ID"]?>').value)+<?=$arOffer["CATALOG_MEASURE_RATIO"]?>;"><span>+</span></a>
																</div>
																<input type="hidden" name="ID" class="offer_id" value="<?=$arOffer['ID']?>" />
																<?$props = array();
																if(!empty($arOffer["PROPERTIES"]["ARTNUMBER"]["VALUE"])):		
																	$props[] = array(
																		"NAME" => $arOffer["PROPERTIES"]["ARTNUMBER"]["NAME"],
																		"CODE" => $arOffer["PROPERTIES"]["ARTNUMBER"]["CODE"],
																		"VALUE" => $arOffer["PROPERTIES"]["ARTNUMBER"]["VALUE"]
																	);
																endif;
																foreach($arOffer["DISPLAY_PROPERTIES"] as $propOffer) {
																	$props[] = array(
																		"NAME" => $propOffer["NAME"],
																		"CODE" => $propOffer["CODE"],
																		"VALUE" => strip_tags($propOffer["DISPLAY_VALUE"])
																	);
																}
																$props = !empty($props) ? strtr(base64_encode(addslashes(gzcompress(serialize($props),9))), '+/=', '-_,') : "";?>
																<input type="hidden" name="PROPS" value="<?=$props?>" />
																<?if(!empty($arElement["SELECT_PROPS"])):?>
																	<input type="hidden" name="SELECT_PROPS" id="select_props_search_<?=$arOffer['ID']?>" value="" />
																<?endif;
																if(!empty($arOffer["PREVIEW_IMG"]["SRC"])):?>
																	<input type="hidden" name="item_image" class="item_image" value="&lt;img class='item_image' src='<?=$arOffer["PREVIEW_IMG"]["SRC"]?>' alt='<?=strip_tags($arElement["NAME"])?>'/&gt;"/>
																<?else:?>
																	<input type="hidden" name="item_image" class="item_image" value="&lt;img class='item_image' src='<?=$arElement["PICTURE_150"]["SRC"]?>' alt='<?=strip_tags($arElement["NAME"])?>'/&gt;"/>
																<?endif;?>
																<input type="hidden" name="item_title" class="item_title" value="<?=strip_tags($arElement['NAME']);?>"/>						
																<button type="submit" name="add2basket" class="btn_buy" value="<?=GetMessage('CATALOG_ELEMENT_ADD_TO_CART')?>"><i class="fa fa-shopping-cart"></i><span><?=GetMessage("CATALOG_ELEMENT_ADD_TO_CART")?></span></button>
																<small class="result hidden"><i class="fa fa-check"></i><span><?=GetMessage("CATALOG_ELEMENT_ADDED")?></span></small>
															</form>
														</div>
													<?endif;													
												elseif(!$arOffer["CAN_BUY"]):?>
													<a class="btn_buy apuo" id="order_anch_search_<?=$arOffer['ID']?>" href="javascript:void(0)" rel="nofollow"><i class="fa fa-clock-o"></i><span><?=GetMessage("CATALOG_ELEMENT_UNDER_ORDER")?></span></a>
													<?$properties = false;
													foreach($arOffer["DISPLAY_PROPERTIES"] as $propOffer) {
														$properties[] = $propOffer["NAME"].": ".strip_tags($propOffer["DISPLAY_VALUE"]);
													}
													$properties = implode("; ", $properties);
													if(!empty($properties)):
														$offer_name = strip_tags($arElement["NAME"])." (".$properties.")";
													else:
														$offer_name = strip_tags($arElement["NAME"]);
													endif;?>
													<?$APPLICATION->IncludeComponent("altop:ask.price", "order",
														Array(
															"ELEMENT_ID" => "search_".$arOffer["ID"],		
															"ELEMENT_NAME" => $offer_name,
															"EMAIL_TO" => "",				
															"REQUIRED_FIELDS" => array("NAME", "TEL", "TIME")
														),
														false,
														array("HIDE_ICONS" => "Y")
													);?>
												<?endif;?>
											</div>
										<?endforeach;
									else:?>
										<div class="buy_more">
											<?if($arElement["CAN_BUY"]):?>
												<div class="add2basket_block">
													<form action="<?=SITE_DIR?>ajax/add2basket.php" class="add2basket_search_form">
														<div class="qnt_cont">
															<a href="javascript:void(0)" class="minus" onclick="if (BX('quantity_search_select_<?=$arElement["ITEM_ID"]?>').value > <?=$arElement["CATALOG_MEASURE_RATIO"]?>) BX('quantity_search_select_<?=$arElement["ITEM_ID"]?>').value = parseFloat(BX('quantity_search_select_<?=$arElement["ITEM_ID"]?>').value)-<?=$arElement["CATALOG_MEASURE_RATIO"]?>;"><span>-</span></a>
															<input type="text" id="quantity_search_select_<?=$arElement['ITEM_ID']?>" name="quantity" class="quantity" value="<?=$arElement['CATALOG_MEASURE_RATIO']?>"/>
															<a href="javascript:void(0)" class="plus" onclick="BX('quantity_search_select_<?=$arElement["ITEM_ID"]?>').value = parseFloat(BX('quantity_search_select_<?=$arElement["ITEM_ID"]?>').value)+<?=$arElement["CATALOG_MEASURE_RATIO"]?>;"><span>+</span></a>
														</div>
														<input type="hidden" name="ID" class="id" value="<?=$arElement['ITEM_ID']?>" />
														<?$props = array();
														if(!empty($arElement["PROPERTIES"]["ARTNUMBER"]["VALUE"])):
															$props[] = array(
																"NAME" => $arElement["PROPERTIES"]["ARTNUMBER"]["NAME"],
																"CODE" => $arElement["PROPERTIES"]["ARTNUMBER"]["CODE"],
																"VALUE" => $arElement["PROPERTIES"]["ARTNUMBER"]["VALUE"]
															);												
														endif;
														if(!empty($arElement["DISPLAY_PROPERTIES"])):
															foreach($arElement["DISPLAY_PROPERTIES"] as $propOffer) {
																$props[] = array(
																	"NAME" => $propOffer["NAME"],
																	"CODE" => $propOffer["CODE"],
																	"VALUE" => strip_tags($propOffer["DISPLAY_VALUE"])
																);
															}
														endif;
														$props = !empty($props) ? strtr(base64_encode(addslashes(gzcompress(serialize($props),9))), '+/=', '-_,') : "";?>
														<input type="hidden" name="PROPS" value="<?=$props?>" />
														<input type="hidden" name="SELECT_PROPS" id="select_props_search_<?=$arElement['ITEM_ID']?>" value="" />
														<input type="hidden" name="item_image" class="item_image" value="&lt;img class='item_image' src='<?=$arElement["PICTURE_150"]["SRC"]?>' alt='<?=strip_tags($arElement["NAME"])?>'/&gt;"/>
														<input type="hidden" name="item_title" class="item_title" value="<?=strip_tags($arElement['NAME'])?>"/>
														<button type="submit" name="add2basket" class="btn_buy" value="<?=GetMessage('CATALOG_ELEMENT_ADD_TO_CART')?>"><i class="fa fa-shopping-cart"></i><span><?=GetMessage("CATALOG_ELEMENT_ADD_TO_CART")?></span></button>
														<small class="result hidden"><i class="fa fa-check"></i><span><?=GetMessage("CATALOG_ELEMENT_ADDED")?></span></small>
													</form>
												</div>
											<?endif;?>
										</div>
									<?endif;?>							
								</div>
							<?endif;

							/***OFFERS_LIST***/
							if(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])):
								if($arSetting["OFFERS_VIEW"]["VALUE"] == "LIST"):?>
									<div class="catalog-detail-offers-list">
										<div class="h3"><?=GetMessage("CATALOG_ELEMENT_OFFERS_LIST")?></div>
										<div class="offers-items">
											<div class="thead">
												<div class="offers-items-image"><?=GetMessage("CATALOG_ELEMENT_OFFERS_LIST_IMAGE")?></div>
												<div class="offers-items-name"><?=GetMessage("CATALOG_ELEMENT_OFFERS_LIST_NAME")?></div>
												<?$i = 1;										
												foreach($arResult["SKU_PROPS"] as $arProp):
													if(!isset($arElement["OFFERS_PROP"][$arProp["CODE"]]))
														continue;
													if($i > 3)
														continue;?>						
													<div class="offers-items-prop"><?=htmlspecialcharsex($arProp["NAME"]);?></div>
													<?$i++;											
												endforeach;?>
												<div class="offers-items-price"></div>
												<div class="offers-items-buy"><?=GetMessage("CATALOG_ELEMENT_OFFERS_LIST_PRICE")?></div>
											</div>
											<div class="tbody">
												<?foreach($arElement["OFFERS"] as $keyOffer => $arOffer):
													$sticker = "";
													if($arOffer["MIN_PRICE"]["DISCOUNT_DIFF_PERCENT"] > 0) {
														$sticker .= "<span class='discount'>-".$arOffer["MIN_PRICE"]["DISCOUNT_DIFF_PERCENT"]."%</span>";	
													}?>
													<div class="catalog-item" id="catalog-offer-item-search-<?=$arOffer['ID']?>">
														<div class="catalog-item-info">							
															<div class="catalog-item-image-cont">
																<div class="catalog-item-image">
																	<?if(!empty($arOffer["PREVIEW_IMG"]["SRC"])):?>
																		<img src="<?=$arOffer['PREVIEW_IMG']['SRC']?>" width="<?=$arOffer['PREVIEW_IMG']['WIDTH']?>" height="<?=$arOffer['PREVIEW_IMG']['HEIGHT']?>" alt="<?=strip_tags((isset($arOffer['NAME']) && !empty($arOffer['NAME'])) ? $arOffer['NAME'] : $arElement['NAME']);?>" />
																	<?else:?>
																		<img src="<?=$arElement['PICTURE_150']['SRC']?>" width="<?=$arElement['PICTURE_150']['WIDTH']?>" height="<?=$arElement['PICTURE_150']['HEIGHT']?>" alt="<?=strip_tags((isset($arOffer['NAME']) && !empty($arOffer['NAME'])) ? $arOffer['NAME'] : $arElement['NAME']);?>"/>
																	<?endif;?>
																	<div class="sticker">
																		<?=$sticker?>
																	</div>
																</div>
															</div>
															<div class="catalog-item-title">
																<span class="name"><?=strip_tags((isset($arOffer["NAME"]) && !empty($arOffer["NAME"])) ? $arOffer["NAME"] : $arElement["NAME"]);?></span>
																<span class="article"><?=GetMessage("CATALOG_ELEMENT_ARTNUMBER")?><?=!empty($arOffer["PROPERTIES"]["ARTNUMBER"]["VALUE"]) ? $arOffer["PROPERTIES"]["ARTNUMBER"]["VALUE"] : "-";?></span>
															</div>
															<?if(!empty($arOffer["DISPLAY_PROPERTIES"])):
																$i = 1;
																foreach($arOffer["DISPLAY_PROPERTIES"] as $k => $v):
																	if(!isset($arElement["OFFERS_PROP"][$v["CODE"]]))
																		continue;
																	if($i > 3)
																		continue;?>	
																	<div class="catalog-item-prop">
																		<?foreach($arResult["SKU_PROPS"] as $arProp):
																			if($arProp["CODE"] == $v["CODE"]):
																				if($arProp["SHOW_MODE"] == "TEXT"):
																					echo strip_tags($v["DISPLAY_VALUE"]);
																				elseif($arProp["SHOW_MODE"] == "PICT"):?>
																					<span class="prop_cont">
																						<span class="prop" title="<?=$arProp['VALUES'][$v['VALUE']]['NAME']?>">
																							<?if(!empty($arProp["VALUES"][$v["VALUE"]]["PICT"]["src"])):?>
																								<img src="<?=$arProp['VALUES'][$v['VALUE']]['PICT']['src']?>" width="<?=$arProp['VALUES'][$v['VALUE']]['PICT']['width']?>" height="<?=$arProp['VALUES'][$v['VALUE']]['PICT']['height']?>" alt="<?=$arProp['VALUES'][$v['VALUE']]['NAME']?>" />
																							<?else:?>
																								<i style="background:#<?=$arProp['VALUES'][$v['VALUE']]['HEX']?>"></i>
																							<?endif;?>
																						</span>
																					</span>
																				<?endif;
																			endif;
																		endforeach;?>
																	</div>
																	<?$i++;
																endforeach;
															endif;?>									
															<div class="item-price">
																<?foreach($arOffer["PRICES"] as $code => $arPrice):
																	if($arPrice["MIN_PRICE"] == "Y"):
																		if($arPrice["CAN_ACCESS"]):
																			
																			$price = CCurrencyLang::GetCurrencyFormat($arPrice["CURRENCY"], "ru");
																			if(empty($price["THOUSANDS_SEP"])):
																				$price["THOUSANDS_SEP"] = " ";
																			endif;
																			$price["REFERENCE_DECIMALS"] = $price["DECIMALS"];
																			if($price["HIDE_ZERO"] == "Y"):
																				if($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"])):
																					if(round($arPrice["DISCOUNT_VALUE"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $price["DECIMALS"]) == round($arPrice["DISCOUNT_VALUE"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], 0)):
																						$price["REFERENCE_DECIMALS"] = 0;
																					endif;
																				endif;
																				if(round($arPrice["DISCOUNT_VALUE"], $price["DECIMALS"]) == round($arPrice["DISCOUNT_VALUE"], 0)):
																					$price["DECIMALS"] = 0;
																				endif;
																			endif;
																			$currency = str_replace("#", " ", $price["FORMAT_STRING"]);

																			if($arPrice["DISCOUNT_VALUE"] <= 0):
																				$arOffer["ASK_PRICE"] = 1;?>
																				<span class="catalog-item-no-price">
																					<span class="unit">
																						<?=GetMessage("CATALOG_ELEMENT_NO_PRICE")?>
																						<br />
																						<span><?=(!empty($arOffer["CATALOG_MEASURE_NAME"])) ? GetMessage("CATALOG_ELEMENT_UNIT")." ".$arOffer["CATALOG_MEASURE_NAME"] : "";?></span>
																					</span>
																				</span>
																			<?else:?>												
																				<span class="catalog-item-price">
																					<?=number_format($arPrice["DISCOUNT_VALUE"], $price["DECIMALS"], $price["DEC_POINT"], $price["THOUSANDS_SEP"]);?>
																					<span class="unit">
																						<?=$currency?>
																						<span><?=(!empty($arOffer["CATALOG_MEASURE_NAME"])) ? GetMessage("CATALOG_ELEMENT_UNIT")." ".$arOffer["CATALOG_MEASURE_NAME"] : "";?></span>
																					</span>
																					<?if($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"])):?>
																						<span class="catalog-item-price-reference">
																							<?=number_format($arPrice["DISCOUNT_VALUE"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $price["REFERENCE_DECIMALS"], $price["DEC_POINT"], $price["THOUSANDS_SEP"]);?>
																							<span><?=$currency?></span>
																						</span>
																					<?endif;?>
																				</span>
																				<?if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>
																					<span class="catalog-item-price-old">
																						<?=$arPrice["PRINT_VALUE"];?>
																					</span>
																					<span class="catalog-item-price-percent">
																						<?=GetMessage('CATALOG_ELEMENT_SKIDKA')?>
																						<br />
																						<?=$arPrice["PRINT_DISCOUNT_DIFF"]?>
																					</span>
																				<?endif;												
																			endif;
																		endif;
																	endif;
																endforeach;?>
															</div>
															<?if(!empty($arOffer["DISPLAY_PROPERTIES"])):?>
																<div class="catalog-item-props-mob"></div>
															<?endif;?>
															<div class="buy_more">
																<div class="available">
																	<?if($arOffer["CAN_BUY"]):?>
																		<div class="avl">
																			<i class="fa fa-check-circle"></i>
																			<span>
																				<?=GetMessage("CATALOG_ELEMENT_AVAILABLE");
																				if($arOffer["CATALOG_QUANTITY_TRACE"] == "Y"):
																					if(in_array("PRODUCT_QUANTITY", $arSetting["GENERAL_SETTINGS"]["VALUE"])):
																						echo " ".$arOffer["CATALOG_QUANTITY"];
																					endif;
																				endif;?>
																			</span>
																		</div>
																	<?elseif(!$arOffer["CAN_BUY"]):?>
																		<div class="not_avl">
																			<i class="fa fa-times-circle"></i>
																			<span><?=GetMessage("CATALOG_ELEMENT_NOT_AVAILABLE")?></span>
																		</div>
																	<?endif;?>
																</div>
																<div class="clr"></div>
																<?if($arOffer["CAN_BUY"]):
																	if($arOffer["ASK_PRICE"]):?>
																		<a class="btn_buy apuo" id="ask_price_anch_search_<?=$arOffer['ID']?>" href="javascript:void(0)" rel="nofollow"><i class="fa fa-comment-o"></i><span class="short"><?=GetMessage("CATALOG_ELEMENT_ASK_PRICE_SHORT")?></span></a>
																		<?$properties = false;
																		foreach($arOffer["DISPLAY_PROPERTIES"] as $propOffer) {
																			$properties[] = $propOffer["NAME"].": ".strip_tags($propOffer["DISPLAY_VALUE"]);
																		}
																		$properties = implode("; ", $properties);
																		if(!empty($properties)):
																			$offer_name = (strip_tags((isset($arOffer["NAME"]) && !empty($arOffer["NAME"])) ? $arOffer["NAME"] : $arElement["NAME"]))." (".$properties.")";
																		else:
																			$offer_name = strip_tags((isset($arOffer["NAME"]) && !empty($arOffer["NAME"])) ? $arOffer["NAME"] : $arElement["NAME"]);
																		endif;?>
																		<?$APPLICATION->IncludeComponent("altop:ask.price", "",
																			Array(
																				"ELEMENT_ID" => "search_".$arOffer["ID"],		
																				"ELEMENT_NAME" => $offer_name,
																				"EMAIL_TO" => "",				
																				"REQUIRED_FIELDS" => array("NAME", "TEL", "TIME")
																			),
																			false,
																			array("HIDE_ICONS" => "Y")
																		);?>
																	<?elseif(!$arOffer["ASK_PRICE"]):?>
																		<div class="add2basket_block">
																			<?foreach($arOffer["PRICES"] as $code => $arPrice):
																				if($arPrice["MIN_PRICE"] == "Y"):
																					$props = array();
																					if(!empty($arOffer["PROPERTIES"]["ARTNUMBER"]["VALUE"])):	
																						$props[] = array(
																							"NAME" => $arOffer["PROPERTIES"]["ARTNUMBER"]["NAME"],
																							"CODE" => $arOffer["PROPERTIES"]["ARTNUMBER"]["CODE"],
																							"VALUE" => $arOffer["PROPERTIES"]["ARTNUMBER"]["VALUE"]
																						);
																					endif;
																					foreach($arOffer["DISPLAY_PROPERTIES"] as $propOffer) {
																						$props[] = array(
																							"NAME" => $propOffer["NAME"],
																							"CODE" => $propOffer["CODE"],
																							"VALUE" => strip_tags($propOffer["DISPLAY_VALUE"])
																						);
																					}
																					$props = !empty($props) ? strtr(base64_encode(addslashes(gzcompress(serialize($props),9))), '+/=', '-_,') : "";?>
																					<div class="delay">
																						<a href="javascript:void(0)" id="catalog-item-delay-search-<?=$arOffer['ID']?>" class="catalog-item-delay" onclick="return addToDelay('<?=$arOffer["ID"]?>', '<?=$arOffer["CATALOG_MEASURE_RATIO"]?>', '<?=$props?>', '', 'catalog-item-delay-search-<?=$arOffer["ID"]?>', '<?=SITE_DIR?>')" rel="nofollow"><i class="fa fa-heart-o"></i><i class="fa fa-check"></i></a>
																					</div>
																				<?endif;
																			endforeach;?>
																			<form action="<?=SITE_DIR?>ajax/add2basket.php" id="add2basket_form_search_<?=$arOffer['ID']?>" class="add2basket_search_form">
																				<div class="qnt_cont">
																					<a href="javascript:void(0)" class="minus" onclick="if (BX('quantity_search_<?=$arOffer["ID"]?>').value > <?=$arOffer["CATALOG_MEASURE_RATIO"]?>) BX('quantity_search_<?=$arOffer["ID"]?>').value = parseFloat(BX('quantity_search_<?=$arOffer["ID"]?>').value)-<?=$arOffer["CATALOG_MEASURE_RATIO"]?>;"><span>-</span></a>
																					<input type="text" id="quantity_search_<?=$arOffer['ID']?>" name="quantity" class="quantity" value="<?=$arOffer['CATALOG_MEASURE_RATIO']?>"/>
																					<a href="javascript:void(0)" class="plus" onclick="BX('quantity_search_<?=$arOffer["ID"]?>').value = parseFloat(BX('quantity_search_<?=$arOffer["ID"]?>').value)+<?=$arOffer["CATALOG_MEASURE_RATIO"]?>;"><span>+</span></a>
																				</div>
																				<input type="hidden" name="ID" class="offer_id" value="<?=$arOffer['ID']?>" />
																				<?$props = array();
																				if(!empty($arOffer["PROPERTIES"]["ARTNUMBER"]["VALUE"])):	
																					$props[] = array(
																						"NAME" => $arOffer["PROPERTIES"]["ARTNUMBER"]["NAME"],
																						"CODE" => $arOffer["PROPERTIES"]["ARTNUMBER"]["CODE"],
																						"VALUE" => $arOffer["PROPERTIES"]["ARTNUMBER"]["VALUE"]
																					);
																				endif;
																				foreach($arOffer["DISPLAY_PROPERTIES"] as $propOffer) {
																					$props[] = array(
																						"NAME" => $propOffer["NAME"],
																						"CODE" => $propOffer["CODE"],
																						"VALUE" => strip_tags($propOffer["DISPLAY_VALUE"])
																					);
																				}
																				$props = !empty($props) ? strtr(base64_encode(addslashes(gzcompress(serialize($props),9))), '+/=', '-_,') : "";?>
																				<input type="hidden" name="PROPS" value="<?=$props?>" />
																				<?if(!empty($arElement["SELECT_PROPS"])):?>
																					<input type="hidden" name="SELECT_PROPS" id="select_props_search_<?=$arOffer['ID']?>" value="" />
																				<?endif;
																				if(!empty($arOffer["PREVIEW_IMG"]["SRC"])):?>
																					<input type="hidden" name="item_image" class="item_image" value="&lt;img class='item_image' src='<?=$arOffer["PREVIEW_IMG"]["SRC"]?>' alt='<?=strip_tags((isset($arOffer["NAME"]) && !empty($arOffer["NAME"])) ? $arOffer["NAME"] : $arElement["NAME"]);?>'/&gt;"/>
																				<?else:?>
																					<input type="hidden" name="item_image" class="item_image" value="&lt;img class='item_image' src='<?=$arElement["PICTURE_150"]["SRC"]?>' alt='<?=strip_tags((isset($arOffer["NAME"]) && !empty($arOffer["NAME"])) ? $arOffer["NAME"] : $arElement["NAME"]);?>'/&gt;"/>
																				<?endif;?>
																				<input type="hidden" name="item_title" class="item_title" value="<?=strip_tags((isset($arOffer['NAME']) && !empty($arOffer['NAME'])) ? $arOffer['NAME'] : $arElement['NAME']);?>"/>
																				<button type="submit" name="add2basket" class="btn_buy" value="<?=GetMessage('CATALOG_ELEMENT_ADD_TO_CART')?>"><i class="fa fa-shopping-cart"></i></button>
																				<small class="result offer-item hidden"><i class="fa fa-check"></i></small>
																			</form>
																			<button name="boc_anch" id="boc_anch_search_<?=$arOffer['ID']?>" class="btn_buy boc_anch" value="<?=GetMessage('CATALOG_ELEMENT_BOC')?>"><i class="fa fa-bolt"></i><?=GetMessage('CATALOG_ELEMENT_BOC_SHORT')?></button>
																			<?$APPLICATION->IncludeComponent("altop:buy.one.click", ".default", 
																				array(
																					"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
																					"IBLOCK_ID" => $arParams["IBLOCK_ID"],
																					"ELEMENT_ID" => $arOffer["ID"],
																					"ELEMENT_CODE" => "search",
																					"ELEMENT_PROPS" => $props,
																					"REQUIRED_ORDER_FIELDS" => array(
																						0 => "NAME",
																						1 => "TEL",
																					),
																					"DEFAULT_PERSON_TYPE" => "1",
																					"DEFAULT_ORDER_PROP_NAME" => "1",
																					"DEFAULT_ORDER_PROP_TEL" => "3",
																					"DEFAULT_ORDER_PROP_EMAIL" => "2",
																					"DEFAULT_DELIVERY" => "0",
																					"DEFAULT_PAYMENT" => "0",										
																					"BUY_MODE" => "ONE",
																					"DUPLICATE_LETTER_TO_EMAILS" => array(
																						0 => "sales",
																					),
																				),
																				false,
																				array("HIDE_ICONS" => "Y")
																			);?>
																		</div>
																	<?endif;
																elseif(!$arOffer["CAN_BUY"]):?>
																	<a class="btn_buy apuo" id="order_anch_search_<?=$arOffer['ID']?>" href="javascript:void(0)" rel="nofollow"><i class="fa fa-clock-o"></i><span class="short"><?=GetMessage("CATALOG_ELEMENT_UNDER_ORDER")?></span></a>
																	<?$properties = false;
																	foreach($arOffer["DISPLAY_PROPERTIES"] as $propOffer) {
																		$properties[] = $propOffer["NAME"].": ".strip_tags($propOffer["DISPLAY_VALUE"]);
																	}
																	$properties = implode("; ", $properties);
																	if(!empty($properties)):
																		$offer_name = (strip_tags((isset($arOffer["NAME"]) && !empty($arOffer["NAME"])) ? $arOffer["NAME"] : $arElement["NAME"]))." (".$properties.")";
																	else:
																		$offer_name = strip_tags((isset($arOffer["NAME"]) && !empty($arOffer["NAME"])) ? $arOffer["NAME"] : $arElement["NAME"]);
																	endif;?>
																	<?$APPLICATION->IncludeComponent("altop:ask.price", "order",
																		Array(
																			"ELEMENT_ID" => "search_".$arOffer["ID"],		
																			"ELEMENT_NAME" => $offer_name,
																			"EMAIL_TO" => "",				
																			"REQUIRED_FIELDS" => array("NAME", "TEL", "TIME")
																		),
																		false,
																		array("HIDE_ICONS" => "Y")
																	);?>
																<?endif;?>										
															</div>										
														</div>
													</div>							
												<?endforeach;?>
											</div>
										</div>
									</div>
								<?endif;
							endif;?>
						</div>
					</div>
				</div>
				<?if(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])):
					$arJSParams = array(
						"PRODUCT_TYPE" => $arElement["CATALOG_TYPE"],
						"VISUAL" => array(
							"ID" => $arItemIDs["ID"],
							"PICT_ID" => $arItemIDs["PICT"],
							"PRICE_ID" => $arItemIDs["PRICE"],
							"BUY_ID" => $arItemIDs["BUY"],
							"TREE_ID" => $arItemIDs["PROP_DIV"],
							"TREE_ITEM_ID" => $arItemIDs["PROP"]
						),
						"PRODUCT" => array(
							"ID" => $arElement["ITEM_ID"],
							"NAME" => $arElement["NAME"]
						),
						"OFFERS_VIEW" => $arSetting["OFFERS_VIEW"]["VALUE"],
						"OFFERS" => $arElement["JS_OFFERS"],
						"OFFER_SELECTED" => $arElement["OFFERS_SELECTED"],
						"TREE_PROPS" => $arSkuProps
					);
				else:
					$arJSParams = array(
						"PRODUCT_TYPE" => $arElement["CATALOG_TYPE"],
						"VISUAL" => array(
							"ID" => $arItemIDs["ID"]
						),
						"PRODUCT" => array(
							"ID" => $arElement["ITEM_ID"],
							"NAME" => $arElement["NAME"]
						)
					);
				endif;
				if(isset($arElement["SELECT_PROPS"]) && !empty($arElement["SELECT_PROPS"])):
					$arJSParams["VISUAL"]["SELECT_PROP_ID"] = $arItemIDs["SELECT_PROP_DIV"];
					$arJSParams["VISUAL"]["SELECT_PROP_ITEM_ID"] = $arItemIDs["SELECT_PROP"];
					$arJSParams["SELECT_PROPS"] = $arSelProps;
				endif;?>
				<script type="text/javascript">
					var <?=$strObName;?> = new JCCatalogSectionSearch(<?=CUtil::PhpToJSObject($arJSParams, false, true);?>);
				</script>
			<?endif;
		endforeach;
	endforeach;
else:?>
	<a href="javascript:void(0)" class="pop-up-close search_close"><i class="fa fa-times"></i></a>	
	<div id="catalog_search_empty">
		<?=GetMessage("CATALOG_EMPTY_RESULT")?>
	</div>			
<?endif;?>