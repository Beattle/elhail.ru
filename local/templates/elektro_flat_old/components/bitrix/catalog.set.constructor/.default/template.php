<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

global $arSetting;

$intElementID = $arResult["ELEMENT_ID"];?>

<div class="set-constructor-items">
	<div class="h3"><?=GetMessage("CATALOG_SET_BUY_SET")?></div>
	<div class="catalog-item-cards">		
		<div class="catalog-item-card set_element" data-price="<?=$arResult['ELEMENT']['PRICE_DISCOUNT_VALUE']?>" data-reference-price="<?=($arSetting['REFERENCE_PRICE']['VALUE'] == 'Y' && !empty($arSetting['REFERENCE_PRICE_COEF']['VALUE']) ? $arResult['ELEMENT']['PRICE_DISCOUNT_VALUE'] * $arSetting['REFERENCE_PRICE_COEF']['VALUE'] : $arResult['ELEMENT']['PRICE_DISCOUNT_VALUE']);?>" data-old-price="<?=$arResult['ELEMENT']['PRICE_VALUE']?>"data-discount-diff-price="<?=$arResult['ELEMENT']['PRICE_DISCOUNT_DIFFERENCE_VALUE']?>">
			<div class="catalog-item-info">
				<div class="item-image-cont">
					<div class="item-image">					
						<span>
							<?if(is_array($arResult["ELEMENT"]["PREVIEW_IMG"])):?>
								<img class="item_img" src="<?=$arResult['ELEMENT']['PREVIEW_IMG']['SRC']?>" width="<?=$arResult['ELEMENT']['PREVIEW_IMG']['WIDTH']?>" height="<?=$arResult['ELEMENT']['PREVIEW_IMG']['HEIGHT']?>" alt="<?=$arResult['ELEMENT']['NAME']?>" />
							<?else:?>
								<img class="item_img" src="<?=SITE_TEMPLATE_PATH?>/images/no-photo.jpg" width="150" height="150" alt="<?=$arResult['ELEMENT']['NAME']?>" />
							<?endif?>
						</span>							
					</div>
				</div>
				<div class="item-all-title">
					<span class="item-title" title="<?=$arResult['ELEMENT']['NAME']?>">
						<?=$arResult["ELEMENT"]["NAME"]?>
					</span>
				</div>
				<div class="item-price-cont<?=($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"]) ? ' reference' : '');?>">					
					<?$price = CCurrencyLang::GetCurrencyFormat($arResult["ELEMENT"]["PRICE_CURRENCY"], "ru");
					if(empty($price["THOUSANDS_SEP"])):
						$price["THOUSANDS_SEP"] = " ";
					endif;					
					if($price["HIDE_ZERO"] == "Y"):						
						if(round($arResult["ELEMENT"]["PRICE_DISCOUNT_VALUE"], $price["DECIMALS"]) == round($arResult["ELEMENT"]["PRICE_DISCOUNT_VALUE"], 0)):
							$price["DECIMALS"] = 0;
						endif;
					endif;
					$currency = str_replace("#", " ", $price["FORMAT_STRING"]);?>

					<div class="item-price">
						<?if($arResult["ELEMENT"]["PRICE_DISCOUNT_VALUE"] < $arResult["ELEMENT"]["PRICE_VALUE"]):?>
							<span class="catalog-item-price-old">
								<?=$arResult["ELEMENT"]["PRICE_PRINT_VALUE"];?>
							</span>
						<?endif;?>
						<span class="catalog-item-price">
							<?=number_format($arResult["ELEMENT"]["PRICE_DISCOUNT_VALUE"], $price["DECIMALS"], $price["DEC_POINT"], $price["THOUSANDS_SEP"]);?>
							<span class="unit"><?=$currency?></span>
						</span>
						<?if($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"])):?>
							<span class="catalog-item-price-reference">
								<?=CCurrencyLang::CurrencyFormat($arResult["ELEMENT"]["PRICE_DISCOUNT_VALUE"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $arResult["ELEMENT"]["PRICE_CURRENCY"], true);?>
							</span>
						<?endif;?>
					</div>
				</div>
			</div>
		</div>		
		<?foreach($arResult["SET_ITEMS"]["DEFAULT"] as $key => $arItem):?>			
			<div class="catalog-item-card set_item" data-price="<?=$arItem['PRICE_DISCOUNT_VALUE']?>" data-reference-price="<?=($arSetting['REFERENCE_PRICE']['VALUE'] == 'Y' && !empty($arSetting['REFERENCE_PRICE_COEF']['VALUE']) ? $arItem['PRICE_DISCOUNT_VALUE'] * $arSetting['REFERENCE_PRICE_COEF']['VALUE'] : $arItem['PRICE_DISCOUNT_VALUE']);?>" data-old-price="<?=$arItem['PRICE_VALUE']?>" data-discount-diff-price="<?=$arItem['PRICE_DISCOUNT_DIFFERENCE_VALUE']?>">				
				<div class="catalog-item-info">
					<div class="item-image-cont">
						<div class="item-image">
							<a href="<?=$arItem['DETAIL_PAGE_URL']?>">
								<?if(is_array($arItem["PREVIEW_IMG"])):?>
									<img class="item_img" src="<?=$arItem['PREVIEW_IMG']['SRC']?>" width="<?=$arItem['PREVIEW_IMG']['WIDTH']?>" height="<?=$arItem['PREVIEW_IMG']['HEIGHT']?>" alt="<?=$arItem['NAME']?>" />
								<?else:?>
									<img class="item_img" src="<?=SITE_TEMPLATE_PATH?>/images/no-photo.jpg" width="150" height="150" alt="<?=$arItem['NAME']?>" />
								<?endif;?>
							</a>							
						</div>
					</div>
					<div class="item-all-title">
						<a class="item-title" href="<?=$arItem['DETAIL_PAGE_URL']?>" title="<?=$arItem['NAME']?>">
							<?=$arItem["NAME"]?>
						</a>
					</div>
					<div class="item-price-cont<?=($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"]) ? ' reference' : '');?>">						
						<?$price = CCurrencyLang::GetCurrencyFormat($arItem["PRICE_CURRENCY"], "ru");
						if(empty($price["THOUSANDS_SEP"])):
							$price["THOUSANDS_SEP"] = " ";
						endif;						
						if($price["HIDE_ZERO"] == "Y"):							
							if(round($arItem["PRICE_DISCOUNT_VALUE"], $price["DECIMALS"]) == round($arItem["PRICE_DISCOUNT_VALUE"], 0)):
								$price["DECIMALS"] = 0;
							endif;
						endif;
						$currency = str_replace("#", " ", $price["FORMAT_STRING"]);?>

						<div class="item-price">
							<?if($arItem["PRICE_DISCOUNT_VALUE"] < $arItem["PRICE_VALUE"]):?>
								<span class="catalog-item-price-old">
									<?=$arItem["PRICE_PRINT_VALUE"];?>									
								</span>
							<?endif;?>
							<span class="catalog-item-price">
								<?=number_format($arItem["PRICE_DISCOUNT_VALUE"], $price["DECIMALS"], $price["DEC_POINT"], $price["THOUSANDS_SEP"]);?>
								<span class="unit"><?=$currency?></span>
							</span>
							<?if($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"])):?>
								<span class="catalog-item-price-reference">
									<?=CCurrencyLang::CurrencyFormat($arItem["PRICE_DISCOUNT_VALUE"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $arItem["PRICE_CURRENCY"], true);?>
								</span>
							<?endif;?>
						</div>
					</div>
				</div>
				<a class="bx_item_set_del pop-up-close" href="javascript:void(0)" onclick="catalogSetDefaultObj_<?=$intElementID;?>.DeleteItem(this.parentNode, '<?=$arItem["ID"]?>')"><i class="fa fa-times"></i></a>
			</div>
		<?endforeach?>		
		<div class="catalog-item-card set_result">
			<div class="catalog-item-info">
				<div class="item-image-cont">
					<div class="item-image">
						<i class="fa fa-check"></i>
					</div>
				</div>
				<div class="item-price-cont<?=($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"]) ? ' reference' : '');?>">
					<?$price = CCurrencyLang::GetCurrencyFormat($arResult["SET_ITEMS"]["PRICE_CURRENCY"], "ru");
					if(empty($price["THOUSANDS_SEP"])):
						$price["THOUSANDS_SEP"] = " ";
					endif;					
					if($price["HIDE_ZERO"] == "Y"):						
						if(round($arResult["SET_ITEMS"]["PRICE_VALUE"], $price["DECIMALS"]) == round($arResult["SET_ITEMS"]["PRICE_VALUE"], 0)):
							$price["DECIMALS"] = 0;
						endif;
					endif;
					$currency = str_replace("#", " ", $price["FORMAT_STRING"]);?>

					<div class="item-price">
						<?if($arResult["SET_ITEMS"]["PRICE_VALUE"] < $arResult["SET_ITEMS"]["OLD_PRICE_VALUE"]):?>
							<span class="catalog-item-price-old set-result-price-old">
								<?=$arResult["SET_ITEMS"]["OLD_PRICE"];?>
							</span>
							<span class="catalog-item-price-percent set-result-price-percent">
								<span class="text"><?=GetMessage("CATALOG_SET_DISCOUNT_DIFF")?></span>
								<span class="set-result-price-discount"><?=$arResult["SET_ITEMS"]["PRICE_DISCOUNT_DIFFERENCE"]?></span>
							</span>
						<?endif;?>
						<span class="catalog-item-price">
							<span class="set-result-price">
								<?=number_format($arResult["SET_ITEMS"]["PRICE_VALUE"], $price["DECIMALS"], $price["DEC_POINT"], $price["THOUSANDS_SEP"]);?>
							</span>
							<span class="unit set-result-price-currency"><?=$currency?></span>
						</span>
						<?if($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"])):?>
							<span class="catalog-item-price-reference set-result-price-reference">
								<?=CCurrencyLang::CurrencyFormat($arResult["SET_ITEMS"]["PRICE_VALUE"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $arResult["SET_ITEMS"]["PRICE_CURRENCY"], true);?>
							</span>
						<?endif;?>
					</div>
				</div>
				<div class="buy_more">
					<div class="add2basket_block">						
						<button name="add2basket" class="btn_buy" onclick="catalogSetDefaultObj_<?=$intElementID;?>.Add2Basket();" value="<?=GetMessage('CATALOG_SET_ADD_TO_CART')?>"><i class="fa fa-shopping-cart"></i><span><?=GetMessage("CATALOG_SET_ADD_TO_CART")?></span></button>
						<small class="result hidden"><i class="fa fa-check"></i><span><?=GetMessage("CATALOG_SET_ADDED")?></span></small>
					</div>				
				</div>
			</div>
		</div>	
	</div>
	<?if(count($arResult["SET_ITEMS"]["OTHER"]) > 0):?>
		<a class="btn_buy apuo collect_set" href="javascript:void(0)" onclick="OpenCatalogSetPopup('<?=$intElementID?>');"><span class="collect_cont"><i class="fa fa-th"></i><span><?=GetMessage("CATALOG_SET_CONSTRUCT")?></span></span></a>
	<?endif;?>	
	<div class="clr"></div>
</div>

<?
$popupParams["REFERENCE_PRICE"] = $arSetting["REFERENCE_PRICE"]["VALUE"];
$popupParams["REFERENCE_PRICE_COEF"] = $arSetting["REFERENCE_PRICE_COEF"]["VALUE"];
$popupParams["AJAX_PATH"] = $this->GetFolder()."/ajax.php";
$popupParams["SITE_ID"] = SITE_ID;
$popupParams["SITE_DIR"] = SITE_DIR;
$popupParams["CURRENT_TEMPLATE_PATH"] = $this->GetFolder();
$popupParams["MESS"] = array(	
	"CATALOG_SET_POPUP_DESC" => GetMessage("CATALOG_SET_POPUP_DESC"),
	"CATALOG_SET_DISCOUNT_DIFF" => GetMessage("CATALOG_SET_DISCOUNT_DIFF"),
	"CATALOG_SET_ADD_TO_CART" => GetMessage("CATALOG_SET_ADD_TO_CART"),
	"CATALOG_SET_ADDED" => GetMessage("CATALOG_SET_ADDED")
);
$popupParams["ELEMENT"] = $arResult["ELEMENT"];
$popupParams["SET_ITEMS"] = $arResult["SET_ITEMS"];
$popupParams["DEFAULT_SET_IDS"] = $arResult["DEFAULT_SET_IDS"];
$popupParams["ITEMS_RATIO"] = $arResult["ITEMS_RATIO"];
?>
<script type="text/javascript">
	BX.message({		
		setItemAdded2Basket: '<?=GetMessageJS("CATALOG_SET_ADDED2BASKET")?>',		
		setOffersCartProps: <?=CUtil::PhpToJSObject($arParams["OFFERS_CART_PROPERTIES"])?>
	});	

	BX.ready(function(){
		catalogSetDefaultObj_<?=$intElementID; ?> = new catalogSetConstructDefault(
			<?=CUtil::PhpToJSObject($arResult["DEFAULT_SET_IDS"])?>,
			'<?=$this->GetFolder();?>/ajax.php',
			'<?=$arResult["ELEMENT"]["PRICE_CURRENCY"]?>',
			'<?=SITE_ID?>',
			'<?=SITE_DIR?>',
			'<?=$intElementID?>',
			'<?=$arResult["ELEMENT"]["PREVIEW_IMG"]["SRC"]?>',
			<?=CUtil::PhpToJSObject($arResult["ITEMS_RATIO"])?>
		);
	});

	if(!window.arSetParams) {
		window.arSetParams = [{'<?=$intElementID?>' : <?echo CUtil::PhpToJSObject($popupParams)?>}];
	} else {
		window.arSetParams.push({'<?=$intElementID?>' : <?echo CUtil::PhpToJSObject($popupParams)?>});
	}

	function OpenCatalogSetPopup(element_id) {
		if(window.arSetParams) {
			for(var obj in window.arSetParams) {
				if(window.arSetParams.hasOwnProperty(obj)) {
					for(var obj2 in window.arSetParams[obj]) {
						if(window.arSetParams[obj].hasOwnProperty(obj2)) {
							if(obj2 == element_id)
								var curSetParams = window.arSetParams[obj][obj2]
						}
					}
				}
			}
		}

		BX.CatalogSetConstructor =
		{
			bInit: false,
			popup: null,
			arParams: {}
		};
		BX.CatalogSetConstructor.popup = BX.PopupWindowManager.create("CatalogSetConstructor_"+element_id, null, {
			autoHide: false,
			offsetLeft: 0,
			offsetTop: 0,
			overlay : true,
			draggable: false,
			closeByEsc: false,
			closeIcon: { right : "-10px", top : "-10px"},
			titleBar: {content: BX.create("span", {html: "<?=GetMessage('CATALOG_SET_POPUP_TITLE_BAR')?>"})},
			content: "<div class='popup-window-wait'><i class='fa fa-spinner fa-pulse'></i></div>",
			events: {
				onAfterPopupShow: function()
				{
					BX.ajax.post(
						'<? echo $this->GetFolder(); ?>/popup.php',
						{
							lang: BX.message('LANGUAGE_ID'),
							site_id: BX.message('SITE_ID') || '',
							arParams:curSetParams
						},
						BX.delegate(function(result)
						{
							var wndScroll = BX.GetWindowScrollPos(),
								wndSize = BX.GetWindowInnerSize(),
								setWindow,
								popupTop;
							this.setContent(result);

							setWindow = BX("CatalogSetConstructor_"+element_id);
							if (!!setWindow)
							{
								popupTop = wndScroll.scrollTop + (wndSize.innerHeight - setWindow.offsetHeight)/2;
								setWindow.style.left = (wndSize.innerWidth - setWindow.offsetWidth)/2 +"px";
								setWindow.style.top = popupTop > 0 ? popupTop+"px" : 0;
							}
						},
						this)
					);
				}
			}
		});

		BX.addClass(BX("popup-window-overlay-CatalogSetConstructor_"+element_id), "pop-up-bg");
		BX.addClass(BX("CatalogSetConstructor_"+element_id), "popup-set");
		close = BX.findChildren(BX("CatalogSetConstructor_"+element_id), {className: "popup-window-close-icon"}, true);
		if(!!close && 0 < close.length) {
			for(i = 0; i < close.length; i++) {					
				close[i].innerHTML = "<i class='fa fa-times'></i>";
			}
		}

		BX.CatalogSetConstructor.popup.show();
	}
</script>