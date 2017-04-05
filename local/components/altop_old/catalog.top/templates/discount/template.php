<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$frame = $this->createFrame("discount")->begin();
	if(count($arResult["ITEMS"]) > 0):
		global $arSetting;?>
		<script type="text/javascript">
			//<![CDATA[
			$(function() {
				/***OFFERS_LIST_PROPS***/
				<?foreach($arResult["ITEMS"] as $key => $arItem):
					if(isset($arItem["OFFERS"]) && !empty($arItem["OFFERS"])):
						if($arSetting["OFFERS_VIEW"]["VALUE"] == "LIST"):
							foreach($arItem["OFFERS"] as $key => $arOffer):?>
								$("#catalog-offer-item-disc-<?=$arOffer['ID']?> .catalog-item-prop").clone().appendTo("#catalog-offer-item-disc-<?=$arOffer['ID']?> .catalog-item-props-mob");
							<?endforeach;
						endif;
					endif;
				endforeach;?>
				
				/***ADD2BASKET***/
				$(".add2basket_disc_form").submit(function() {
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

		<div class="catalog-item-cards">
			<?foreach($arResult["ITEMS"] as $key => $arItem):
				
				$strMainID = $this->GetEditAreaId($arItem["ID"]);
				$arItemIDs = array(
					"ID" => $strMainID."_disc"
				);

				$bPicture = is_array($arItem["PREVIEW_IMG"]);

				$sticker = "";
				$timeBuy = "";
				$class = "";
				if(array_key_exists("PROPERTIES", $arItem) && is_array($arItem["PROPERTIES"])):
					/***NEW***/
					if(array_key_exists("NEWPRODUCT", $arItem["PROPERTIES"]) && !$arItem["PROPERTIES"]["NEWPRODUCT"]["VALUE"] == false):
						$sticker .= "<span class='new'>".GetMessage("CATALOG_ELEMENT_NEWPRODUCT")."</span>";
					endif;
					/***HIT***/
					if(array_key_exists("SALELEADER", $arItem["PROPERTIES"]) && !$arItem["PROPERTIES"]["SALELEADER"]["VALUE"] == false):
						$sticker .= "<span class='hit'>".GetMessage("CATALOG_ELEMENT_SALELEADER")."</span>";
					endif;
					/***DISCOUNT***/
					if(isset($arItem["OFFERS"]) && !empty($arItem["OFFERS"])):						
						if($arItem["TOTAL_OFFERS"]["MIN_PRICE"]["DISCOUNT_DIFF_PERCENT"] > 0):
							$sticker .= "<span class='discount'>-".$arItem["TOTAL_OFFERS"]["MIN_PRICE"]["DISCOUNT_DIFF_PERCENT"]."%</span>";
						else:
							if(array_key_exists("DISCOUNT", $arItem["PROPERTIES"]) && !$arItem["PROPERTIES"]["DISCOUNT"]["VALUE"] == false):
								$sticker .= "<span class='discount'>%</span>";
							endif;
						endif;
					else:
						if($arItem["MIN_PRICE"]["DISCOUNT_DIFF_PERCENT"] > 0):
							$sticker .= "<span class='discount'>-".$arItem["MIN_PRICE"]["DISCOUNT_DIFF_PERCENT"]."%</span>";
						else:
							if(array_key_exists("DISCOUNT", $arItem["PROPERTIES"]) && !$arItem["PROPERTIES"]["DISCOUNT"]["VALUE"] == false):
								$sticker .= "<span class='discount'>%</span>";
							endif;
						endif;
					endif;
					/***TIME_BUY***/
					if(array_key_exists("TIME_BUY", $arItem["PROPERTIES"]) && !$arItem["PROPERTIES"]["TIME_BUY"]["VALUE"] == false):
						if(!empty($arItem["CURRENT_DISCOUNT"]["ACTIVE_TO"])):						
							if(isset($arItem["OFFERS"]) && !empty($arItem["OFFERS"])):
								$class = " item-tb";
								$timeBuy = "<div class='time_buy_sticker'><span class='time_buy_figure'></span><span class='time_buy_text'>".GetMessage("CATALOG_ELEMENT_TIME_BUY")."</span></div>";
							else:
								if($arItem["CAN_BUY"]):
									$class = " item-tb";
									$timeBuy = "<div class='time_buy_sticker'><span class='time_buy_figure'></span><span class='time_buy_text'>".GetMessage("CATALOG_ELEMENT_TIME_BUY")."</span></div>";
								endif;
							endif;
						endif;
					endif;
				endif;?>

				<div class="catalog-item-card<?=$class?>">
					<div class="catalog-item-info">							
						<div class="item-image-cont">
							<div class="item-image">
								<?/***PICTURE***/?>									
								<a href="<?=$arItem['DETAIL_PAGE_URL']?>">
									<?if($bPicture):?>
										<img class="item_img" src="<?=$arItem['PREVIEW_IMG']['SRC']?>" width="<?=$arItem['PREVIEW_IMG']['WIDTH']?>" height="<?=$arItem['PREVIEW_IMG']['HEIGHT']?>" alt="<?=$arItem['NAME']?>" />
									<?else:?>
										<img class="item_img" src="<?=SITE_TEMPLATE_PATH?>/images/no-photo.jpg" width="150" height="150" alt="<?=$arItem['NAME']?>" />
									<?endif;?>
									<?=$timeBuy?>									
									<span class="sticker">
										<?=$sticker?>
									</span>
									<?if(!empty($arItem["PROPERTIES"]["MANUFACTURER"]["PREVIEW_IMG"]["SRC"])):?>
										<img class="manufacturer" src="<?=$arItem['PROPERTIES']['MANUFACTURER']['PREVIEW_IMG']['SRC']?>" width="<?=$arItem['PROPERTIES']['MANUFACTURER']['PREVIEW_IMG']['WIDTH']?>" height="<?=$arItem['PROPERTIES']['MANUFACTURER']['PREVIEW_IMG']['HEIGHT']?>" alt="<?=$arItem['PROPERTIES']['MANUFACTURER']['NAME']?>" />
									<?endif;?>
								</a>
							</div>
						</div>
						<div class="item-all-title">
							<a class="item-title" href="<?=$arItem['DETAIL_PAGE_URL']?>" title="<?=$arItem['NAME']?>">
								<?=$arItem["NAME"]?>
							</a>
						</div>
						<?/***ARTICLE_RATING***/
						if(in_array("ARTNUMBER", $arSetting["PRODUCT_TABLE_VIEW"]["VALUE"]) || in_array("RATING", $arSetting["PRODUCT_TABLE_VIEW"]["VALUE"])):?>
							<div class="article_rating">
								<?/***ARTICLE***/
								if(in_array("ARTNUMBER", $arSetting["PRODUCT_TABLE_VIEW"]["VALUE"])):?>
									<div class="article">
										<?=GetMessage("CATALOG_ELEMENT_ARTNUMBER")?><?=!empty($arItem["PROPERTIES"]["ARTNUMBER"]["VALUE"]) ? $arItem["PROPERTIES"]["ARTNUMBER"]["VALUE"] : "-";?>
									</div>
								<?endif;
								/***RATING***/
								if(in_array("RATING", $arSetting["PRODUCT_TABLE_VIEW"]["VALUE"])):?>
									<div class="rating">
										<?$APPLICATION->IncludeComponent("bitrix:iblock.vote", "ajax",
											Array(
												"DISPLAY_AS_RATING" => "vote_avg",
												"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
												"IBLOCK_ID" => $arParams["IBLOCK_ID"],
												"ELEMENT_ID" => $arItem["ID"],
												"ELEMENT_CODE" => "disc",
												"MAX_VOTE" => "5",
												"VOTE_NAMES" => array("1","2","3","4","5"),
												"SET_STATUS_404" => "N",
												"CACHE_TYPE" => $arParams["CACHE_TYPE"],
												"CACHE_TIME" => $arParams["CACHE_TIME"],
												"CACHE_NOTES" => "",
												"READ_ONLY" => "Y"
											),
											false,
											array("HIDE_ICONS" => "Y")
										);?>
									</div>
								<?endif;?>
								<div class="clr"></div>
							</div>
						<?endif;?>
						<?if(in_array("PREVIEW_TEXT", $arSetting["PRODUCT_TABLE_VIEW"]["VALUE"])):?>
							<div class="item-desc">
								<?=strip_tags($arItem["PREVIEW_TEXT"]);?>
							</div>
						<?endif;?>
						<div class="item-price-cont<?=(!in_array('OLD_PRICE', $arSetting['PRODUCT_TABLE_VIEW']['VALUE']) && !in_array('PERCENT_PRICE', $arSetting['PRODUCT_TABLE_VIEW']['VALUE']) ? ' one' : '').((in_array('OLD_PRICE', $arSetting['PRODUCT_TABLE_VIEW']['VALUE']) && !in_array('PERCENT_PRICE', $arSetting['PRODUCT_TABLE_VIEW']['VALUE'])) || (!in_array('OLD_PRICE', $arSetting['PRODUCT_TABLE_VIEW']['VALUE']) && in_array('PERCENT_PRICE', $arSetting['PRODUCT_TABLE_VIEW']['VALUE'])) ? ' two' : '').($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"]) ? ' reference' : '');?>">
							<?/***PRICE***/
							if(isset($arItem["OFFERS"]) && !empty($arItem["OFFERS"])):
								$price = CCurrencyLang::GetCurrencyFormat($arItem["TOTAL_OFFERS"]["MIN_PRICE"]["CURRENCY"], "ru");
								if(empty($price["THOUSANDS_SEP"])):
									$price["THOUSANDS_SEP"] = " ";
								endif;								
								if($price["HIDE_ZERO"] == "Y"):									
									if(round($arItem["TOTAL_OFFERS"]["MIN_PRICE"]["DISCOUNT_VALUE"], $price["DECIMALS"]) == round($arItem["TOTAL_OFFERS"]["MIN_PRICE"]["DISCOUNT_VALUE"], 0)):
										$price["DECIMALS"] = 0;
									endif;
								endif;
								$currency = str_replace("#", " ", $price["FORMAT_STRING"]);
							
								if($arItem["TOTAL_OFFERS"]["MIN_PRICE"]["DISCOUNT_VALUE"] <= 0):?>
									<div class="item-no-price">
										<span class="unit">
											<?=GetMessage("CATALOG_ELEMENT_NO_PRICE")?>
											<span><?=(!empty($arItem["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_MEASURE_NAME"])) ? GetMessage("CATALOG_ELEMENT_UNIT")." ".$arItem["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_MEASURE_NAME"] : "";?></span>
										</span>
									</div>
								<?else:?>
									<div class="item-price">
										<?if($arItem["TOTAL_OFFERS"]["MIN_PRICE"]["DISCOUNT_VALUE"] < $arItem["TOTAL_OFFERS"]["MIN_PRICE"]["VALUE"]):
											if(in_array("OLD_PRICE", $arSetting["PRODUCT_TABLE_VIEW"]["VALUE"])):?>
												<span class="catalog-item-price-old">
													<?=$arItem["TOTAL_OFFERS"]["MIN_PRICE"]["PRINT_VALUE"];?>
												</span>
											<?endif;
											if(in_array("PERCENT_PRICE", $arSetting["PRODUCT_TABLE_VIEW"]["VALUE"])):?>
												<span class="catalog-item-price-percent">
													<?=GetMessage("CATALOG_ELEMENT_SKIDKA")." ".$arItem["TOTAL_OFFERS"]["MIN_PRICE"]["PRINT_DISCOUNT_DIFF"];?>
												</span>
											<?endif;
										endif;?>
										<span class="catalog-item-price">
											<?=($arItem["TOTAL_OFFERS"]["FROM"] == "Y") ? "<span class='from'>".GetMessage("CATALOG_ELEMENT_FROM")."</span>" : "";?>
											<?=number_format($arItem["TOTAL_OFFERS"]["MIN_PRICE"]["DISCOUNT_VALUE"], $price["DECIMALS"], $price["DEC_POINT"], $price["THOUSANDS_SEP"]);?>
											<span class="unit">
												<?=$currency?>
												<span><?=(!empty($arItem["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_MEASURE_NAME"])) ? GetMessage("CATALOG_ELEMENT_UNIT")." ".$arItem["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_MEASURE_NAME"] : "";?></span>
											</span>
										</span>
										<?if($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"])):?>
											<span class="catalog-item-price-reference">
												<?=CCurrencyLang::CurrencyFormat($arItem["TOTAL_OFFERS"]["MIN_PRICE"]["DISCOUNT_VALUE"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $arItem["TOTAL_OFFERS"]["MIN_PRICE"]["CURRENCY"], true);?>
											</span>
										<?endif;?>
									</div>								
								<?endif;
							else:
								foreach($arItem["PRICES"] as $code=>$arPrice):
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
												$arItem["ASK_PRICE"]=1;?>
												<div class="item-no-price">
													<span class="unit">
														<?=GetMessage("CATALOG_ELEMENT_NO_PRICE")?>
														<span><?=(!empty($arItem["CATALOG_MEASURE_NAME"])) ? GetMessage("CATALOG_ELEMENT_UNIT")." ".$arItem["CATALOG_MEASURE_NAME"] : "";?></span>
													</span>
												</div>
											<?else:?>
												<div class="item-price">
													<?if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):
														if(in_array("OLD_PRICE", $arSetting["PRODUCT_TABLE_VIEW"]["VALUE"])):?>
															<span class="catalog-item-price-old">
																<?=$arPrice["PRINT_VALUE"];?>
															</span>
														<?endif;
														if(in_array("PERCENT_PRICE", $arSetting["PRODUCT_TABLE_VIEW"]["VALUE"])):?>
															<span class="catalog-item-price-percent">
																<?=GetMessage("CATALOG_ELEMENT_SKIDKA")." ".$arPrice["PRINT_DISCOUNT_DIFF"];?>
															</span>
														<?endif;
													endif;?>
													<span class="catalog-item-price">
														<?=number_format($arPrice["DISCOUNT_VALUE"], $price["DECIMALS"], $price["DEC_POINT"], $price["THOUSANDS_SEP"]);?>
														<span class="unit">
															<?=$currency?>
															<span><?=(!empty($arItem["CATALOG_MEASURE_NAME"])) ? GetMessage("CATALOG_ELEMENT_UNIT")." ".$arItem["CATALOG_MEASURE_NAME"] : "";?></span>
														</span>
													</span>
													<?if($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"])):?>
														<span class="catalog-item-price-reference">
															<?=CCurrencyLang::CurrencyFormat($arPrice["DISCOUNT_VALUE"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $arPrice["CURRENCY"], true);?>
														</span>
													<?endif;?>
												</div>											
											<?endif;
										endif;
									endif;
								endforeach;
							endif;?>
						</div>

						<?/***TIME_BUY***/
						if(array_key_exists("TIME_BUY", $arItem["PROPERTIES"]) && !$arItem["PROPERTIES"]["TIME_BUY"]["VALUE"] == false):
							if(!empty($arItem["CURRENT_DISCOUNT"]["ACTIVE_TO"])):							
								$showBar = false;
								if($arItem["CATALOG_QUANTITY_TRACE"] == "Y"):														
									if(isset($arItem["OFFERS"]) && !empty($arItem["OFFERS"])):
										$showBar = true;									
										$startQnt = $arItem["PROPERTIES"]["TIME_BUY_FROM"]["VALUE"] ? $arItem["PROPERTIES"]["TIME_BUY_FROM"]["VALUE"] : $arItem["TOTAL_OFFERS"]["QUANTITY"];	
										$currQnt = $arItem["PROPERTIES"]["TIME_BUY_TO"]["VALUE"] ? $arItem["PROPERTIES"]["TIME_BUY_TO"]["VALUE"] : $arItem["TOTAL_OFFERS"]["QUANTITY"];	
										$currQntPercent = round($currQnt * 100 / $startQnt);									
									else:
										if($arItem["CAN_BUY"]):
											$showBar = true;
											$startQnt = $arItem["PROPERTIES"]["TIME_BUY_FROM"]["VALUE"] ? $arItem["PROPERTIES"]["TIME_BUY_FROM"]["VALUE"] : $arItem["CATALOG_QUANTITY"];
											$currQnt = $arItem["PROPERTIES"]["TIME_BUY_TO"]["VALUE"] ? $arItem["PROPERTIES"]["TIME_BUY_TO"]["VALUE"] : $arItem["CATALOG_QUANTITY"];
											$currQntPercent = round($currQnt * 100 / $startQnt);								
										endif;
									endif;								
								else:
									$showBar = true;
									$currQntPercent = 100;
								endif;
								
								if($showBar == true):?>
									<div class="item_time_buy_cont">
										<div class="item_time_buy">
											<div class="progress_bar_block">
												<span class="progress_bar_title"><?=GetMessage("CATALOG_ELEMENT_QUANTITY_PERCENT")?></span>
												<div class="progress_bar_cont">
													<div class="progress_bar_bg">
														<div class="progress_bar_line" style="width:<?=$currQntPercent?>%;"></div>
													</div>
												</div>
												<span class="progress_bar_percent"><?=$currQntPercent?>%</span>
											</div>
											<?$new_date = ParseDateTime($arItem["CURRENT_DISCOUNT"]["ACTIVE_TO"], FORMAT_DATETIME);?>
											<script type="text/javascript">												
												$(function() {														
													$("#time_buy_timer_disc_<?=$arItem['ID']?>").countdown({
														until: new Date(<?=$new_date["YYYY"]?>, <?=$new_date["MM"]?> - 1, <?=$new_date["DD"]?>, <?=$new_date["HH"]?>, <?=$new_date["MI"]?>),
														format: "DHMS",
														expiryText: "<div class='over'><?=GetMessage('CATALOG_ELEMENT_TIME_BUY_EXPIRY')?></div>"
													});
												});												
											</script>
											<div class="time_buy_cont">
												<div class="time_buy_clock">
													<i class="fa fa-clock-o"></i>
												</div>
												<div class="time_buy_timer" id="time_buy_timer_disc_<?=$arItem['ID']?>"></div>
											</div>
										</div>
									</div>
								<?endif;
							endif;
						endif;?>

						<div class="buy_more">
							<?/***BUY***/
							if(isset($arItem["OFFERS"]) && !empty($arItem["OFFERS"])):?>
								<div class="available">
									<?if($arItem["TOTAL_OFFERS"]["QUANTITY"] > 0 || $arItem["CATALOG_QUANTITY_TRACE"] == "N"):?>
										<div class="avl">
											<i class="fa fa-check-circle"></i>
											<span>
												<?=GetMessage("CATALOG_ELEMENT_AVAILABLE");
												if($arItem["CATALOG_QUANTITY_TRACE"] == "Y"):
													if(in_array("PRODUCT_QUANTITY", $arSetting["GENERAL_SETTINGS"]["VALUE"])):
														echo " ".$arItem["TOTAL_OFFERS"]["QUANTITY"];
													endif;
												endif;?>
											</span>
										</div>
									<?else:?>
										<div class="not_avl">
											<i class="fa fa-times-circle"></i>
											<span><?=GetMessage("CATALOG_ELEMENT_NOT_AVAILABLE")?></span>
										</div>
									<?endif;?>
								</div>
								<script type="text/javascript">
									$(function() {
										$("#add2basket_disc_offer_form_<?=$arItem['ID']?>").submit(function() {
											var form = $(this);
											$(window).resize(function () {
												modalHeight = $(window).height()/2 - $("#<?=$arItemIDs['ID']?>").height()/2 + $(window).scrollTop();
												$("#<?=$arItemIDs['ID']?>").css({
													"top": modalHeight + "px"
												});
											});
											$(window).resize();
											$("#<?=$arItemIDs['ID']?>_body").fadeIn(300);
											$("#<?=$arItemIDs['ID']?>").fadeIn(300);
																	
											quantityItem = form.find("#quantity_disc_<?=$arItem['ID']?>").attr("value");
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
								<div class="add2basket_block">
									<form action="<?=$APPLICATION->GetCurPage()?>" id="add2basket_disc_offer_form_<?=$arItem['ID']?>">
										<a href="javascript:void(0)" class="minus" onclick="if (BX('quantity_disc_<?=$arItem["ID"]?>').value > <?=$arItem["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_MEASURE_RATIO"]?>) BX('quantity_disc_<?=$arItem["ID"]?>').value = parseFloat(BX('quantity_disc_<?=$arItem["ID"]?>').value)-<?=$arItem["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_MEASURE_RATIO"]?>;"><span>-</span></a>
										<input type="text" id="quantity_disc_<?=$arItem['ID']?>" name="quantity" class="quantity" value="<?=$arItem['TOTAL_OFFERS']['MIN_PRICE']['CATALOG_MEASURE_RATIO']?>"/>
										<a href="javascript:void(0)" class="plus" onclick="BX('quantity_disc_<?=$arItem["ID"]?>').value = parseFloat(BX('quantity_disc_<?=$arItem["ID"]?>').value)+<?=$arItem["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_MEASURE_RATIO"]?>;"><span>+</span></a>
										<button type="submit" name="add2basket" class="btn_buy" value="<?=GetMessage('CATALOG_ELEMENT_ADD_TO_CART')?>"><i class="fa fa-shopping-cart"></i><span><?=GetMessage("CATALOG_ELEMENT_ADD_TO_CART")?></span></button>
									</form>
								</div>
							<?else:?>
								<div class="available">
									<?if($arItem["CAN_BUY"]):?>
										<div class="avl">
											<i class="fa fa-check-circle"></i>
											<span>
												<?=GetMessage("CATALOG_ELEMENT_AVAILABLE");
												if($arItem["CATALOG_QUANTITY_TRACE"] == "Y"):
													if(in_array("PRODUCT_QUANTITY", $arSetting["GENERAL_SETTINGS"]["VALUE"])):
														echo " ".$arItem["CATALOG_QUANTITY"];
													endif;
												endif;?>
											</span>
										</div>
									<?elseif(!$arItem["CAN_BUY"]):?>
										<div class="not_avl">
											<i class="fa fa-times-circle"></i>
											<span><?=GetMessage("CATALOG_ELEMENT_NOT_AVAILABLE")?></span>
										</div>
									<?endif;?>
								</div>
								<?if(isset($arItem["SELECT_PROPS"]) && !empty($arItem["SELECT_PROPS"])):?>
									<script type="text/javascript">
										$(function() {
											$("#add2basket_disc_select_form_<?=$arItem['ID']?>").submit(function() {
												var form = $(this);
												$(window).resize(function () {
													modalHeight = $(window).height()/2 - $("#<?=$arItemIDs['ID']?>").height()/2 + $(window).scrollTop();
													$("#<?=$arItemIDs['ID']?>").css({
														"top": modalHeight + "px"
													});
												});
												$(window).resize();
												$("#<?=$arItemIDs['ID']?>_body").fadeIn(300);
												$("#<?=$arItemIDs['ID']?>").fadeIn(300);
																	
												quantityItem = form.find("#quantity_disc_<?=$arItem['ID']?>").attr("value");
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
								<?endif;?>
								<div class="add2basket_block">
									<?if($arItem["CAN_BUY"]):
										if($arItem["ASK_PRICE"]):?>
											<a class="btn_buy apuo" id="ask_price_anch_disc_<?=$arItem['ID']?>" href="javascript:void(0)" rel="nofollow"><i class="fa fa-comment-o"></i><span class="full"><?=GetMessage("CATALOG_ELEMENT_ASK_PRICE_FULL")?></span><span class="short"><?=GetMessage("CATALOG_ELEMENT_ASK_PRICE_SHORT")?></span></a>
											<?$APPLICATION->IncludeComponent("altop:ask.price", "",
												Array(
													"ELEMENT_ID" => "disc_".$arItem["ID"],		
													"ELEMENT_NAME" => $arItem["NAME"],
													"EMAIL_TO" => "",				
													"REQUIRED_FIELDS" => array("NAME", "TEL", "TIME"),
												),
												false,
												array("HIDE_ICONS" => "Y")
											);?>
										<?elseif(!$arItem["ASK_PRICE"]):
											if(isset($arItem["SELECT_PROPS"]) && !empty($arItem["SELECT_PROPS"])):?>
												<form action="<?=$APPLICATION->GetCurPage()?>" id="add2basket_disc_select_form_<?=$arItem['ID']?>">
											<?else:?>
												<form action="<?=SITE_DIR?>ajax/add2basket.php" class="add2basket_disc_form">
											<?endif;?>
												<a href="javascript:void(0)" class="minus" onclick="if (BX('quantity_disc_<?=$arItem["ID"]?>').value > <?=$arItem["CATALOG_MEASURE_RATIO"]?>) BX('quantity_disc_<?=$arItem["ID"]?>').value = parseFloat(BX('quantity_disc_<?=$arItem["ID"]?>').value)-<?=$arItem["CATALOG_MEASURE_RATIO"]?>;"><span>-</span></a>
												<input type="text" id="quantity_disc_<?=$arItem['ID']?>" name="quantity" class="quantity" value="<?=$arItem['CATALOG_MEASURE_RATIO']?>"/>
												<a href="javascript:void(0)" class="plus" onclick="BX('quantity_disc_<?=$arItem["ID"]?>').value = parseFloat(BX('quantity_disc_<?=$arItem["ID"]?>').value)+<?=$arItem["CATALOG_MEASURE_RATIO"]?>;"><span>+</span></a>
												<?if(!isset($arItem["SELECT_PROPS"]) || empty($arItem["SELECT_PROPS"])):?>
													<input type="hidden" name="ID" value="<?=$arItem['ID']?>" />
													<?if(!empty($arItem["PROPERTIES"]["ARTNUMBER"]["VALUE"])):
														$props = array();
														$props[] = array(
															"NAME" => $arItem["PROPERTIES"]["ARTNUMBER"]["NAME"],
															"CODE" => $arItem["PROPERTIES"]["ARTNUMBER"]["CODE"],
															"VALUE" => $arItem["PROPERTIES"]["ARTNUMBER"]["VALUE"]
														);												
														$props = strtr(base64_encode(addslashes(gzcompress(serialize($props),9))), '+/=', '-_,');?>
														<input type="hidden" name="PROPS" value="<?=$props?>" />
													<?endif;?>
													<input type="hidden" name="item_image" class="item_image" value="&lt;img class='item_image' src='<?=$arItem["PREVIEW_IMG"]["SRC"]?>' alt='<?=$arItem["NAME"]?>'/&gt;"/>
													<input type="hidden" name="item_title" class="item_title" value="<?=$arItem['NAME']?>"/>													
												<?endif;?>										
												<button type="submit" name="add2basket" class="btn_buy" value="<?=GetMessage('CATALOG_ELEMENT_ADD_TO_CART')?>"><i class="fa fa-shopping-cart"></i><span><?=GetMessage("CATALOG_ELEMENT_ADD_TO_CART")?></span></button>
												<?if(!isset($arItem["SELECT_PROPS"]) || empty($arItem["SELECT_PROPS"])):?>
													<small class="result hidden"><i class="fa fa-check"></i><span><?=GetMessage("CATALOG_ELEMENT_ADDED")?></span></small>
												<?endif;?>
											</form>
										<?endif;
									elseif(!$arItem["CAN_BUY"]):?>
										<a class="btn_buy apuo" id="order_anch_disc_<?=$arItem['ID']?>" href="javascript:void(0)" rel="nofollow"><i class="fa fa-clock-o"></i><span><?=GetMessage("CATALOG_ELEMENT_UNDER_ORDER")?></span></a>
										<?$APPLICATION->IncludeComponent("altop:ask.price", "order",
											Array(
												"ELEMENT_ID" => "disc_".$arItem["ID"],		
												"ELEMENT_NAME" => $arItem["NAME"],
												"EMAIL_TO" => "",				
												"REQUIRED_FIELDS" => array("NAME", "TEL", "TIME"),
											),
											false,
											array("HIDE_ICONS" => "Y")
										);?>
									<?endif;?>									
								</div>
							<?endif;?>
							<div class="clr"></div>
							<?/***COMPARE***/
							if($arParams["DISPLAY_COMPARE"]=="Y"):?>
								<div class="compare">
									<a href="javascript:void(0)" class="catalog-item-compare" id="catalog_add2compare_link_disc_<?=$arItem['ID']?>" onclick="return addToCompare('<?=$arItem["COMPARE_URL"]?>', 'catalog_add2compare_link_disc_<?=$arItem["ID"]?>', '<?=SITE_DIR?>');" title="<?=GetMessage('CATALOG_ELEMENT_ADD_TO_COMPARE')?>" rel="nofollow"><i class="fa fa-bar-chart"></i><i class="fa fa-check"></i></a>
								</div>
							<?endif;
							/***DELAY***/
							if(isset($arItem["OFFERS"]) && !empty($arItem["OFFERS"])):
								if($arItem["TOTAL_OFFERS"]["MIN_PRICE"]["CAN_BUY"]):
									if($arItem["TOTAL_OFFERS"]["MIN_PRICE"]["DISCOUNT_VALUE"] > 0):
										$props = array();
										if(!empty($arItem["TOTAL_OFFERS"]["MIN_PRICE"]["PROPERTIES"]["ARTNUMBER"]["VALUE"])):
											$props[] = array(
												"NAME" => $arItem["TOTAL_OFFERS"]["MIN_PRICE"]["PROPERTIES"]["ARTNUMBER"]["NAME"],
												"CODE" => $arItem["TOTAL_OFFERS"]["MIN_PRICE"]["PROPERTIES"]["ARTNUMBER"]["CODE"],
												"VALUE" => $arItem["TOTAL_OFFERS"]["MIN_PRICE"]["PROPERTIES"]["ARTNUMBER"]["VALUE"]
											);																
										endif;
										foreach($arItem["TOTAL_OFFERS"]["MIN_PRICE"]["DISPLAY_PROPERTIES"] as $propOffer) {
											$props[] = array(
												"NAME" => $propOffer["NAME"],
												"CODE" => $propOffer["CODE"],
												"VALUE" => strip_tags($propOffer["DISPLAY_VALUE"])
											);
										}
										$props = !empty($props) ? strtr(base64_encode(addslashes(gzcompress(serialize($props),9))), '+/=', '-_,') : "";?>
										<div class="delay">
											<a href="javascript:void(0)" id="catalog-item-delay-disc-min-<?=$arItem['TOTAL_OFFERS']['MIN_PRICE']['ID']?>" class="catalog-item-delay" onclick="return addToDelay('<?=$arItem["TOTAL_OFFERS"]["MIN_PRICE"]["ID"]?>', '<?=$arItem["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_MEASURE_RATIO"]?>', '<?=$props?>', '', 'catalog-item-delay-disc-min-<?=$arItem["TOTAL_OFFERS"]["MIN_PRICE"]["ID"]?>', '<?=SITE_DIR?>')" title="<?=GetMessage('CATALOG_ELEMENT_ADD_TO_DELAY')?>" rel="nofollow"><i class="fa fa-heart-o"></i><i class="fa fa-check"></i></a>
										</div>
									<?endif;
								endif;
							else:
								if($arItem["CAN_BUY"]):
									foreach($arItem["PRICES"] as $code=>$arPrice):
										if($arPrice["MIN_PRICE"] == "Y"):
											if($arPrice["DISCOUNT_VALUE"] > 0):
												$props = "";
												if(!empty($arItem["PROPERTIES"]["ARTNUMBER"]["VALUE"])):		
													$props = array();
													$props[] = array(
														"NAME" => $arItem["PROPERTIES"]["ARTNUMBER"]["NAME"],
														"CODE" => $arItem["PROPERTIES"]["ARTNUMBER"]["CODE"],
														"VALUE" => $arItem["PROPERTIES"]["ARTNUMBER"]["VALUE"]
													);
													$props = strtr(base64_encode(addslashes(gzcompress(serialize($props),9))), '+/=', '-_,');
												endif;?>
												<div class="delay">
													<a href="javascript:void(0)" id="catalog-item-delay-disc-<?=$arItem['ID']?>" class="catalog-item-delay" onclick="return addToDelay('<?=$arItem["ID"]?>', '<?=$arItem["CATALOG_MEASURE_RATIO"]?>', '<?=$props?>', '', 'catalog-item-delay-disc-<?=$arItem["ID"]?>', '<?=SITE_DIR?>')" title="<?=GetMessage('CATALOG_ELEMENT_ADD_TO_DELAY')?>" rel="nofollow"><i class="fa fa-heart-o"></i><i class="fa fa-check"></i></a>
												</div>
											<?endif;
										endif;
									endforeach;
								endif;
							endif;?>
						</div>
					</div>
				</div>
			<?endforeach;

			/***OFFERS***/
			foreach($arResult["ITEMS"] as $key => $arElement):
				if((isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])) || (isset($arElement["SELECT_PROPS"]) && !empty($arElement["SELECT_PROPS"]))):
					$strMainID = $this->GetEditAreaId($arElement["ID"]);
					$arItemIDs = array(
						"ID" => $strMainID."_disc",
						"PICT" => $strMainID."_disc_picture",
						"PRICE" => $strMainID."_disc_price",
						"BUY" => $strMainID."_disc_buy",
						"PROP_DIV" => $strMainID."_disc_sku_tree",
						"PROP" => $strMainID."_disc_prop_",
						"SELECT_PROP_DIV" => $strMainID."_disc_propdiv",
						"SELECT_PROP" => $strMainID."_disc_select_prop_"
					);
					$strObName = "ob".preg_replace("/[^a-zA-Z0-9_]/", "x", $strMainID)."_disc";?>
					
					<div class="pop-up-bg more_options_body" id="<?=$arItemIDs['ID']?>_body"></div>
					<div class="pop-up more_options<?=(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])) ? (($arSetting['OFFERS_VIEW']['VALUE'] == 'LIST') ? ' offers-list' : '') : '';?>" id="<?=$arItemIDs['ID']?>">
						<a href="javascript:void(0)" class="pop-up-close more_options_close" id="<?=$arItemIDs['ID']?>_close"><i class="fa fa-times"></i></a>
						<div class="h1"><?=GetMessage("CATALOG_ELEMENT_MORE_OPTIONS")?></div>
						<div class="item_info">
							<div class="item_image" id="<?=$arItemIDs['PICT']?>">
								<?if(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])):
									if($arSetting["OFFERS_VIEW"]["VALUE"] != "LIST"):
										foreach($arElement["OFFERS"] as $key_off => $arOffer):?>
											<div id="img_disc_<?=$arElement['ID']?>_<?=$arOffer['ID']?>" class="img <?=$arElement['ID']?> hidden">
												<?if(isset($arOffer["PREVIEW_IMG"])):?>
													<img src="<?=$arOffer['PREVIEW_IMG']['SRC']?>" alt="<?=$arElement['NAME']?>" width="<?=$arOffer['PREVIEW_IMG']['WIDTH']?>" height="<?=$arOffer['PREVIEW_IMG']['HEIGHT']?>"/>
												<?else:?>
													<img src="<?=$arElement['PREVIEW_IMG']['SRC']?>" width="<?=$arElement['PREVIEW_IMG']['WIDTH']?>" height="<?=$arElement['PREVIEW_IMG']['HEIGHT']?>" alt="<?=$arElement['NAME']?>"/>
												<?endif;?>
											</div>
										<?endforeach;
									endif;
								endif;
								if(!isset($arElement["OFFERS"]) || empty($arElement["OFFERS"]) || $arSetting["OFFERS_VIEW"]["VALUE"] == "LIST"):?>
									<div class="img">
										<?if(isset($arElement["PREVIEW_IMG"])):?>
											<img src="<?=$arElement["PREVIEW_IMG"]["SRC"]?>" width="<?=$arElement["PREVIEW_IMG"]["WIDTH"]?>" height="<?=$arElement["PREVIEW_IMG"]["HEIGHT"]?>" alt="<?=$arElement["NAME"]?>"/>
										<?else:?>
											<img src="<?=SITE_TEMPLATE_PATH?>/images/no-photo.jpg" width="150" height="150" alt="<?=$arElement['NAME']?>" />
										<?endif;?>
									</div>
								<?endif;?>
								<div class="item_name">
									<?=$arElement["NAME"]?>
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
												<div id="price_disc_<?=$arElement['ID']?>_<?=$arOffer['ID']?>" class="price <?=$arElement['ID']?> hidden">
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
											foreach($arElement["OFFERS"] as $key => $arOffer):?>
												<div id="buy_more_disc_<?=$arElement['ID']?>_<?=$arOffer['ID']?>" class="buy_more <?=$arElement['ID']?> hidden">
													<?if($arOffer["CAN_BUY"]):
														if($arOffer["ASK_PRICE"]):?>
															<a class="btn_buy apuo" id="ask_price_anch_disc_<?=$arOffer['ID']?>" href="javascript:void(0)" rel="nofollow"><i class="fa fa-comment-o"></i><span><?=GetMessage("CATALOG_ELEMENT_ASK_PRICE_FULL")?></span></a>
															<?$properties = false;
															foreach($arOffer["DISPLAY_PROPERTIES"] as $propOffer) {
																$properties[] = $propOffer["NAME"].": ".strip_tags($propOffer["DISPLAY_VALUE"]);
															}
															$properties = implode("; ", $properties);
															if(!empty($properties)):
																$offer_name = $arElement["NAME"]." (".$properties.")";
															else:
																$offer_name = $arElement["NAME"];
															endif;?>
															<?$APPLICATION->IncludeComponent("altop:ask.price", "",
																Array(
																	"ELEMENT_ID" => "disc_".$arOffer["ID"],		
																	"ELEMENT_NAME" => $offer_name,
																	"EMAIL_TO" => "",				
																	"REQUIRED_FIELDS" => array("NAME", "TEL", "TIME")
																),
																false,
																array("HIDE_ICONS" => "Y")
															);?>
														<?elseif(!$arOffer["ASK_PRICE"]):?>											
															<div class="add2basket_block">
																<form action="<?=SITE_DIR?>ajax/add2basket.php" class="add2basket_disc_form">
																	<div class="qnt_cont">
																		<a href="javascript:void(0)" class="minus" onclick="if (BX('quantity_disc_<?=$arOffer["ID"]?>').value > <?=$arOffer["CATALOG_MEASURE_RATIO"]?>) BX('quantity_disc_<?=$arOffer["ID"]?>').value = parseFloat(BX('quantity_disc_<?=$arOffer["ID"]?>').value)-<?=$arOffer["CATALOG_MEASURE_RATIO"]?>;"><span>-</span></a>
																		<input type="text" id="quantity_disc_<?=$arOffer['ID']?>" name="quantity" class="quantity" value="<?=$arOffer['CATALOG_MEASURE_RATIO']?>"/>
																		<a href="javascript:void(0)" class="plus" onclick="BX('quantity_disc_<?=$arOffer["ID"]?>').value = parseFloat(BX('quantity_disc_<?=$arOffer["ID"]?>').value)+<?=$arOffer["CATALOG_MEASURE_RATIO"]?>;"><span>+</span></a>
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
																		<input type="hidden" name="SELECT_PROPS" id="select_props_disc_<?=$arOffer['ID']?>" value="" />		
																	<?endif;
																	if(!empty($arOffer["PREVIEW_IMG"]["SRC"])):?>
																		<input type="hidden" name="item_image" class="item_image" value="&lt;img class='item_image' src='<?=$arOffer["PREVIEW_IMG"]["SRC"]?>' alt='<?=$arElement["NAME"]?>'/&gt;"/>
																	<?else:?>
																		<input type="hidden" name="item_image" class="item_image" value="&lt;img class='item_image' src='<?=$arElement["PREVIEW_IMG"]["SRC"]?>' alt='<?=$arElement["NAME"]?>'/&gt;"/>
																	<?endif;?>
																	<input type="hidden" name="item_title" class="item_title" value="<?=$arElement['NAME']?>"/>								
																	<button type="submit" name="add2basket" class="btn_buy" value="<?=GetMessage('CATALOG_ELEMENT_ADD_TO_CART')?>"><i class="fa fa-shopping-cart"></i><span><?=GetMessage("CATALOG_ELEMENT_ADD_TO_CART")?></span></button>
																	<small class="result hidden"><i class="fa fa-check"></i><span><?=GetMessage("CATALOG_ELEMENT_ADDED")?></span></small>
																</form>
															</div>
														<?endif;														
													elseif(!$arOffer["CAN_BUY"]):?>
														<a class="btn_buy apuo" id="order_anch_disc_<?=$arOffer['ID']?>" href="javascript:void(0)" rel="nofollow"><i class="fa fa-clock-o"></i><span><?=GetMessage("CATALOG_ELEMENT_UNDER_ORDER")?></span></a>
														<?$properties = false;
														foreach($arOffer["DISPLAY_PROPERTIES"] as $propOffer) {
															$properties[] = $propOffer["NAME"].": ".strip_tags($propOffer["DISPLAY_VALUE"]);
														}
														$properties = implode("; ", $properties);
														if(!empty($properties)):
															$offer_name = $arElement["NAME"]." (".$properties.")";
														else:
															$offer_name = $arElement["NAME"];
														endif;?>
														<?$APPLICATION->IncludeComponent("altop:ask.price", "order",
															Array(
																"ELEMENT_ID" => "disc_".$arOffer["ID"],		
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
														<form action="<?=SITE_DIR?>ajax/add2basket.php" class="add2basket_disc_form">
															<div class="qnt_cont">
																<a href="javascript:void(0)" class="minus" onclick="if (BX('quantity_disc_select_<?=$arElement["ID"]?>').value > <?=$arElement["CATALOG_MEASURE_RATIO"]?>) BX('quantity_disc_select_<?=$arElement["ID"]?>').value = parseFloat(BX('quantity_disc_select_<?=$arElement["ID"]?>').value)-<?=$arElement["CATALOG_MEASURE_RATIO"]?>;"><span>-</span></a>
																<input type="text" id="quantity_disc_select_<?=$arElement['ID']?>" name="quantity" class="quantity" value="<?=$arElement['CATALOG_MEASURE_RATIO']?>"/>
																<a href="javascript:void(0)" class="plus" onclick="BX('quantity_disc_select_<?=$arElement["ID"]?>').value = parseFloat(BX('quantity_disc_select_<?=$arElement["ID"]?>').value)+<?=$arElement["CATALOG_MEASURE_RATIO"]?>;"><span>+</span></a>
															</div>
															<input type="hidden" name="ID" class="id" value="<?=$arElement['ID']?>" />
															<?if(!empty($arElement["PROPERTIES"]["ARTNUMBER"]["VALUE"])):
																$props = array();
																$props[] = array(
																	"NAME" => $arElement["PROPERTIES"]["ARTNUMBER"]["NAME"],
																	"CODE" => $arElement["PROPERTIES"]["ARTNUMBER"]["CODE"],
																	"VALUE" => $arElement["PROPERTIES"]["ARTNUMBER"]["VALUE"]
																);												
																$props = strtr(base64_encode(addslashes(gzcompress(serialize($props),9))), '+/=', '-_,');?>
																<input type="hidden" name="PROPS" value="<?=$props?>" />
															<?endif;?>
															<input type="hidden" name="SELECT_PROPS" id="select_props_disc_<?=$arElement['ID']?>" value="" />				
															<input type="hidden" name="item_image" class="item_image" value="&lt;img class='item_image' src='<?=$arElement["PREVIEW_IMG"]["SRC"]?>' alt='<?=$arElement["NAME"]?>'/&gt;"/>
															<input type="hidden" name="item_title" class="item_title" value="<?=$arElement['NAME']?>"/>										
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
														<div class="catalog-item" id="catalog-offer-item-disc-<?=$arOffer['ID']?>">
															<div class="catalog-item-info">							
																<div class="catalog-item-image-cont">
																	<div class="catalog-item-image">
																		<?if(!empty($arOffer["PREVIEW_IMG"]["SRC"])):?>
																			<img src="<?=$arOffer['PREVIEW_IMG']['SRC']?>" width="<?=$arOffer['PREVIEW_IMG']['WIDTH']?>" height="<?=$arOffer['PREVIEW_IMG']['HEIGHT']?>" alt="<?=(isset($arOffer['NAME']) && !empty($arOffer['NAME'])) ? $arOffer['NAME'] : $arElement['NAME'];?>" />
																		<?else:?>
																			<img src="<?=$arElement['PREVIEW_IMG']['SRC']?>" width="<?=$arElement['PREVIEW_IMG']['WIDTH']?>" height="<?=$arElement['PREVIEW_IMG']['HEIGHT']?>" alt="<?=(isset($arOffer['NAME']) && !empty($arOffer['NAME'])) ? $arOffer['NAME'] : $arElement['NAME'];?>" />
																		<?endif;?>
																		<div class="sticker">
																			<?=$sticker?>
																		</div>
																	</div>
																</div>
																<div class="catalog-item-title">
																	<span class="name"><?=(isset($arOffer["NAME"]) && !empty($arOffer["NAME"])) ? $arOffer["NAME"] : $arElement["NAME"];?></span>
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
																			<a class="btn_buy apuo" id="ask_price_anch_disc_<?=$arOffer['ID']?>" href="javascript:void(0)" rel="nofollow"><i class="fa fa-comment-o"></i><span class="short"><?=GetMessage("CATALOG_ELEMENT_ASK_PRICE_SHORT")?></span></a>
																			<?$properties = false;
																			foreach($arOffer["DISPLAY_PROPERTIES"] as $propOffer) {
																				$properties[] = $propOffer["NAME"].": ".strip_tags($propOffer["DISPLAY_VALUE"]);
																			}
																			$properties = implode("; ", $properties);
																			if(!empty($properties)):
																				$offer_name = ((isset($arOffer["NAME"]) && !empty($arOffer["NAME"])) ? $arOffer["NAME"] : $arElement["NAME"])." (".$properties.")";
																			else:
																				$offer_name = (isset($arOffer["NAME"]) && !empty($arOffer["NAME"])) ? $arOffer["NAME"] : $arElement["NAME"];
																			endif;?>
																			<?$APPLICATION->IncludeComponent("altop:ask.price", "",
																				Array(
																					"ELEMENT_ID" => "disc_".$arOffer["ID"],		
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
																							<a href="javascript:void(0)" id="catalog-item-delay-disc-<?=$arOffer['ID']?>" class="catalog-item-delay" onclick="return addToDelay('<?=$arOffer["ID"]?>', '<?=$arOffer["CATALOG_MEASURE_RATIO"]?>', '<?=$props?>', '', 'catalog-item-delay-disc-<?=$arOffer["ID"]?>', '<?=SITE_DIR?>')" rel="nofollow"><i class="fa fa-heart-o"></i><i class="fa fa-check"></i></a>
																						</div>
																					<?endif;
																				endforeach;?>
																				<form action="<?=SITE_DIR?>ajax/add2basket.php" id="add2basket_form_disc_<?=$arOffer['ID']?>" class="add2basket_disc_form">
																					<div class="qnt_cont">
																						<a href="javascript:void(0)" class="minus" onclick="if (BX('quantity_disc_<?=$arOffer["ID"]?>').value > <?=$arOffer["CATALOG_MEASURE_RATIO"]?>) BX('quantity_disc_<?=$arOffer["ID"]?>').value = parseFloat(BX('quantity_disc_<?=$arOffer["ID"]?>').value)-<?=$arOffer["CATALOG_MEASURE_RATIO"]?>;"><span>-</span></a>
																						<input type="text" id="quantity_disc_<?=$arOffer['ID']?>" name="quantity" class="quantity" value="<?=$arOffer['CATALOG_MEASURE_RATIO']?>"/>
																						<a href="javascript:void(0)" class="plus" onclick="BX('quantity_disc_<?=$arOffer["ID"]?>').value = parseFloat(BX('quantity_disc_<?=$arOffer["ID"]?>').value)+<?=$arOffer["CATALOG_MEASURE_RATIO"]?>;"><span>+</span></a>
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
																						<input type="hidden" name="SELECT_PROPS" id="select_props_disc_<?=$arOffer['ID']?>" value="" />
																					<?endif;
																					if(!empty($arOffer["PREVIEW_IMG"]["SRC"])):?>
																						<input type="hidden" name="item_image" class="item_image" value="&lt;img class='item_image' src='<?=$arOffer["PREVIEW_IMG"]["SRC"]?>' alt='<?=(isset($arOffer["NAME"]) && !empty($arOffer["NAME"])) ? $arOffer["NAME"] : $arElement["NAME"];?>'/&gt;"/>
																					<?else:?>
																						<input type="hidden" name="item_image" class="item_image" value="&lt;img class='item_image' src='<?=$arElement["PREVIEW_IMG"]["SRC"]?>' alt='<?=(isset($arOffer["NAME"]) && !empty($arOffer["NAME"])) ? $arOffer["NAME"] : $arElement["NAME"];?>'/&gt;"/>
																					<?endif;?>
																					<input type="hidden" name="item_title" class="item_title" value="<?=(isset($arOffer['NAME']) && !empty($arOffer['NAME'])) ? $arOffer['NAME'] : $arElement['NAME'];?>"/>
																					<button type="submit" name="add2basket" class="btn_buy" value="<?=GetMessage('CATALOG_ELEMENT_ADD_TO_CART')?>"><i class="fa fa-shopping-cart"></i></button>
																					<small class="result offer-item hidden"><i class="fa fa-check"></i></small>
																				</form>
																				<button name="boc_anch" id="boc_anch_disc_<?=$arOffer['ID']?>" class="btn_buy boc_anch" value="<?=GetMessage('CATALOG_ELEMENT_BOC')?>"><i class="fa fa-bolt"></i><?=GetMessage('CATALOG_ELEMENT_BOC_SHORT')?></button>
																				<?$APPLICATION->IncludeComponent("altop:buy.one.click", ".default", 
																					array(
																						"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
																						"IBLOCK_ID" => $arParams["IBLOCK_ID"],
																						"ELEMENT_ID" => $arOffer["ID"],
																						"ELEMENT_CODE" => "disc",
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
																		<a class="btn_buy apuo" id="order_anch_disc_<?=$arOffer['ID']?>" href="javascript:void(0)" rel="nofollow"><i class="fa fa-clock-o"></i><span class="short"><?=GetMessage("CATALOG_ELEMENT_UNDER_ORDER")?></span></a>
																		<?$properties = false;
																		foreach($arOffer["DISPLAY_PROPERTIES"] as $propOffer) {
																			$properties[] = $propOffer["NAME"].": ".strip_tags($propOffer["DISPLAY_VALUE"]);
																		}
																		$properties = implode("; ", $properties);
																		if(!empty($properties)):
																			$offer_name = ((isset($arOffer["NAME"]) && !empty($arOffer["NAME"])) ? $arOffer["NAME"] : $arElement["NAME"])." (".$properties.")";
																		else:
																			$offer_name = (isset($arOffer["NAME"]) && !empty($arOffer["NAME"])) ? $arOffer["NAME"] : $arElement["NAME"];
																		endif;?>
																		<?$APPLICATION->IncludeComponent("altop:ask.price", "order",
																			Array(
																				"ELEMENT_ID" => "disc_".$arOffer["ID"],		
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
								"ID" => $arElement["ID"],
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
								"ID" => $arElement["ID"],
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
						var <?=$strObName;?> = new JCCatalogSectionDisc(<?=CUtil::PhpToJSObject($arJSParams, false, true);?>);
					</script>
				<?endif;
			endforeach;?>
		</div>
	<?else:?>
		<div class="discount_empty"></div>
	<?endif;
$frame->end();?>