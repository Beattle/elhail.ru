<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<script type="text/javascript">
	$(function () {
		$("#catalog_item_toogle_filter").click(function() {
			$(".filter").slideToggle();
		});
		$("#catalog_item_toogle_filter_hidden").click(function() {
			$(".filter").slideToggle();
		});
	});
</script>

<a href="javascript:void(0)" id="catalog_item_toogle_filter"><?=GetMessage("FILTER_DISPLAY_HIDDEN")?></a>
<div class="filter">
	<form name="<?=$arResult["FILTER_NAME"]."_form"?>" action="<?=$arResult["FORM_ACTION"]?>" method="get">
		<input type="hidden" name="set_filter" value="Y" />
		<table width="100%" border="0">
			<?foreach($arResult["ITEMS"] as $arItem):
				if(isset($arItem["PRICE"])):
					if(!$arItem["VALUES"]["MIN"]["VALUE"] || !$arItem["VALUES"]["MAX"]["VALUE"] || $arItem["VALUES"]["MIN"]["VALUE"] == $arItem["VALUES"]["MAX"]["VALUE"])
						continue;
						
					foreach($arItem["CURRENCIES"] as $key => $curr):
						$price = CCurrencyLang::GetCurrencyFormat($key, "ru");
						$currency = str_replace("#", " ", $price["FORMAT_STRING"]);
					endforeach;?>
					<tr>
						<?if(empty($arItem["VALUES"]["MIN"]["HTML_VALUE"])) 
							$arItem["VALUES"]["MIN"]["HTML_VALUE"] = $arItem["VALUES"]["MIN"]["VALUE"];
						if(empty($arItem["VALUES"]["MAX"]["HTML_VALUE"])) 
							$arItem["VALUES"]["MAX"]["HTML_VALUE"] = $arItem["VALUES"]["MAX"]["VALUE"];?>
						<td>
							<span class="sect_name"><?=GetMessage("PRODUCT_PRICE")?></span>
						</td>
						<td>
							<div class="price">
								<?=GetMessage("PRICE_FROM")?> 
								<input id="sl1" type="text" name="<?=$arItem["VALUES"]["MIN"]["CONTROL_ID"]?>" class="text" value="<?=$arItem["VALUES"]["MIN"]["HTML_VALUE"]?>" />
							</div> 
							<div id="slider_all">
								<div class="slider"></div>
							</div>
							<div class="price" >
								<?=GetMessage("PRICE_TO")?>
								<input id="sl2" type="text" class="text" name="<?=$arItem["VALUES"]["MAX"]["CONTROL_ID"]?>" value="<?=$arItem["VALUES"]["MAX"]["HTML_VALUE"]?>" />
								<?=$currency;?>
							</div>
						</td>
						
						<script type="text/javascript">
							$(document).ready(function(){
								$('.slider').slider({
									range: true,
									step: 0.01,
									min: <?=$arItem["VALUES"]["MIN"]["VALUE"]?>,
									max: <?=$arItem["VALUES"]["MAX"]["VALUE"]?>,
									values: [ <?=$arItem["VALUES"]["MIN"]["HTML_VALUE"]?>, <?=$arItem["VALUES"]["MAX"]["HTML_VALUE"]?> ],     
									slide: function( event, ui ) {
										$("#sl1").val(ui.values[0]);
										$("#sl2").val(ui.values[1]);
									},
									stop: function(event, ui){
										change();
									}
								});              
							});
						</script>
					</tr>
				<?endif;
			endforeach;

			foreach($arResult["ITEMS"] as $arItem):
				if(!empty($arItem["VALUES"])):
					if($arItem["PROPERTY_TYPE"] == "N"):
						if(!$arItem["VALUES"]["MIN"]["VALUE"] || !$arItem["VALUES"]["MAX"]["VALUE"] || $arItem["VALUES"]["MIN"]["VALUE"] == $arItem["VALUES"]["MAX"]["VALUE"])
							continue;?>
						<tr>
							<?if(empty($arItem["VALUES"]["MIN"]["HTML_VALUE"])) 
								$arItem["VALUES"]["MIN"]["HTML_VALUE"] = $arItem["VALUES"]["MIN"]["VALUE"];
							if(empty($arItem["VALUES"]["MAX"]["HTML_VALUE"])) 
								$arItem["VALUES"]["MAX"]["HTML_VALUE"] = $arItem["VALUES"]["MAX"]["VALUE"];?>
							<td>
								<span class="sect_name"><?=$arItem["NAME"]?>:</span>
							</td>
							<td>
								<div class="price">
									<?=GetMessage("PRICE_FROM")?> 
									<input id="<?=$arItem["VALUES"]["MIN"]["CONTROL_ID"]?>" type="text" name="<?=$arItem["VALUES"]["MIN"]["CONTROL_ID"]?>" class="text" value="<?=$arItem["VALUES"]["MIN"]["HTML_VALUE"]?>" />
								</div> 
								<div id="slider_all">
									<div class="slider <?=$arItem['CODE']?>"></div>
								</div>
								<div class="price" >
									<?=GetMessage("PRICE_TO")?>
									<input id="<?=$arItem["VALUES"]["MAX"]["CONTROL_ID"]?>" type="text" class="text" name="<?=$arItem["VALUES"]["MAX"]["CONTROL_ID"]?>" value="<?=$arItem["VALUES"]["MAX"]["HTML_VALUE"]?>" />
								</div>
							</td>
							<script type="text/javascript">
								$(document).ready(function(){
									$(".slider.<?=$arItem['CODE']?>").slider({
										range: true,
										step: 0.1,
										min: <?=$arItem["VALUES"]["MIN"]["VALUE"]?>,
										max: <?=$arItem["VALUES"]["MAX"]["VALUE"]?>,
										values: [ <?=$arItem["VALUES"]["MIN"]["HTML_VALUE"]?>, <?=$arItem["VALUES"]["MAX"]["HTML_VALUE"]?> ],     
										slide: function( event, ui ) {
											$("#<?=$arItem['VALUES']['MIN']['CONTROL_ID']?>").val(ui.values[0]);
											$("#<?=$arItem['VALUES']['MAX']['CONTROL_ID']?>").val(ui.values[1]);
										},
										stop: function(event, ui){
											change();
										}
									});              
								});
							</script>
						</tr>
					<?endif;
				endif;
			endforeach;

			foreach($arResult["ITEMS"] as $arItem):
				if(!empty($arItem["VALUES"])):
					if($arItem["PROPERTY_TYPE"] == "L"):
						if($arItem["CODE"] == "NEWPRODUCT" || $arItem["CODE"] == "SALELEADER" || $arItem["CODE"] == "DISCOUNT"):
							$nsd = 1;
						endif;
					endif;
				endif;
			endforeach;

			if(!empty($nsd)):?>
				<tr>
					<td>
						<span class="sect_name"><?=GetMessage("PRODUCT_TYPE")?></span>
					</td>
					<td>
						<?foreach($arResult["ITEMS"] as $arItem):
							if(!empty($arItem["VALUES"])):
								if($arItem["PROPERTY_TYPE"] == "L"):
									if($arItem["CODE"] == "NEWPRODUCT" || $arItem["CODE"] == "SALELEADER" || $arItem["CODE"] == "DISCOUNT"):
										foreach($arItem['VALUES'] as $arOption):?>
											<div class="custom-forms">
												<input type="checkbox" id="<?=$arOption['CONTROL_ID']?>" name="<?=$arOption['CONTROL_ID']?>" <?if ($_REQUEST[$arOption['CONTROL_ID']]=="Y") {echo "checked";} ?> value="<?=$arOption['HTML_VALUE']?>">
												<label for="<?=$arOption['CONTROL_ID']?>"><?=$arItem['NAME']?> </label>
											</div>
										<?endforeach;
									endif;
								endif;
							endif;
						endforeach;?>
					</td>
				</tr>
			<?endif;
									
			foreach($arResult["ITEMS"] as $arItem):
				if(!empty($arItem["VALUES"])):
					if(($arItem["PROPERTY_TYPE"] == "E" || $arItem["PROPERTY_TYPE"] == "S") && $arItem["CODE"]!='COLOR'):?>
						<tr>
							<td>
								<span class="sect_name"><?=$arItem["NAME"]?>:</span>
							</td>
							<td>
								<?foreach($arItem["VALUES"] as $arOption):?>
									<div class="custom-forms">
										<input type="checkbox" id="<?=$arOption['CONTROL_ID']?>" name="<?=$arOption['CONTROL_ID']?>" <?if ($_REQUEST[$arOption['CONTROL_ID']]=="Y") {echo "checked";} ?> value="<?=$arOption['HTML_VALUE']?>">
										<label for="<?=$arOption['CONTROL_ID']?>"><?=$arOption["VALUE"];?></label>
									</div>
								<?endforeach;?>
							</td>
						</tr>
					<?endif;
				endif;
			endforeach;
			
			foreach($arResult["ITEMS"] as $arItem):
				if(!empty($arItem["VALUES"])):
					if($arItem["PROPERTY_TYPE"] == "L"):
						if($arItem["CODE"] != "NEWPRODUCT" && $arItem["CODE"] != "SALELEADER" && $arItem["CODE"] != "DISCOUNT"):?>
							<tr>
								<td>
									<span class="sect_name"><?=$arItem["NAME"]?>:</span>
								</td>
								<td>
									<?foreach($arItem["VALUES"] as $arOption):?>
										<div class="custom-forms">
											<input type="checkbox" id="<?=$arOption['CONTROL_ID']?>" name="<?=$arOption['CONTROL_ID']?>" <?if ($_REQUEST[$arOption['CONTROL_ID']]=="Y") {echo "checked";} ?> value="<?=$arOption['HTML_VALUE']?>">
											<label for="<?=$arOption['CONTROL_ID']?>"><?=$arOption["VALUE"];?></label>
										</div>		
									<?endforeach;?>
								</td>
							</tr>
						<?endif;
					endif;
				endif;
			endforeach;

			foreach($arResult["ITEMS"] as $arItem):
				if(!empty($arItem["VALUES"])):
					if($arItem["CODE"]=='COLOR'):?>
						<tr>
							<td>
								<span class="sect_name"><?=$arItem["NAME"]?>:</span>
							</td>
							<td>
								<?foreach($arItem["VALUES"] as $arOption):?>
									<div class="custom-forms colors">
										<input type="checkbox" id="<?=$arOption['CONTROL_ID']?>" name="<?=$arOption['CONTROL_ID']?>" <?if($_REQUEST[$arOption['CONTROL_ID']]=="Y") {echo "checked";}?> value="<?=$arOption['HTML_VALUE']?>" />
										<?if(!empty($arOption["PICT"]["src"])):?>
											<label for="<?=$arOption['CONTROL_ID']?>" title="<?=$arOption["NAME"]?>">
												<img src="<?=$arOption['PICT']['src']?>" width="<?=$arOption['PICT']['width']?>" height="<?=$arOption['PICT']['height']?>" />
											</label>
										<?else:?>
											<label for="<?=$arOption['CONTROL_ID']?>" title="<?=$arOption["NAME"]?>">
												<i style='background:#<?=$arOption["HEX"]?>;'></i>
											</label>
										<?endif;?>
									</div>
								<?endforeach;?>
							</td>
						</tr>
					<?endif;
				endif;	
			endforeach;?>
		</table>
		<div class="clr"></div>
		<div class="submit">
			<a href="javascript:void(0)" id="catalog_item_toogle_filter_hidden"><?=GetMessage("FILTER_HIDDEN")?></a>
			<input type="submit" value="<?=GetMessage("SEARCH")?>" />
		</div>
	</form>
</div>
<div class="clr"></div>

<div class="count_items">
	<label><?=GetMessage("COUNT_ITEMS")?></label>
	<span><?=$arResult["ELEMENT_COUNT"]?></span>
</div>