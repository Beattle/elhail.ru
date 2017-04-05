<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

if(!empty($arParams["CAPTCHA_CODE"])):
	$frame = $this->createFrame("catalog_review_".$arParams["ELEMENT_AREA_ID"])->begin("");
else:
	$this->setFrameMode(true);
endif;

use Bitrix\Main\Localization\Loc;

$popupParams["POPUP_ID"] = "catalog_review_".$arParams["ELEMENT_AREA_ID"];
$popupParams["FORM_ACTION"] = $this->__component->__path."/script.php";
$popupParams["PARAMS"] = $arParams;
$popupParams["RESULT"] = $arResult;
$popupParams["MESS"] = array(	
	"REVIEWS_NAME" => Loc::getMessage("CATALOG_REVIEWS_NAME"),
	"REVIEWS_MESSAGE" => Loc::getMessage("CATALOG_REVIEWS_MESSAGE"),	
	"REVIEWS_CAPTCHA" => Loc::getMessage("CATALOG_REVIEWS_CAPTCHA"),
	"REVIEWS_SEND" => Loc::getMessage("CATALOG_REVIEWS_SEND")
);?>

<div class="reviews-collapse reviews-minimized">
	<a class="btn_buy apuo reviews-collapse-link" id="catalog_review_anch" href="javascript:void(0)" rel="nofollow"><i class="fa fa-pencil"></i><span class="full"><?=Loc::getMessage("CATALOG_REVIEWS_TITLE_FULL")?></span><span class="short"><?=Loc::getMessage("CATALOG_REVIEWS_TITLE_SHORT")?></span></a>
</div>

<?if(count($arResult["ITEMS"]) > 0):?>
	<div class="catalog-reviews-list" data-count="<?=count($arResult['ITEMS'])?>">
		<?foreach($arResult["ITEMS"] as $key => $arElement):?>
			<div class="catalog-review">
				<div class="catalog-review__col catalog-review__userpic-wrap">
					<div class="catalog-review__userpic">
						<?if(is_array($arElement["CREATED_USER_PERSONAL_PHOTO"])):?>
							<img src="<?=$arElement['CREATED_USER_PERSONAL_PHOTO']['SRC']?>" width="<?=$arElement['CREATED_USER_PERSONAL_PHOTO']['WIDTH']?>" height="<?=$arElement['CREATED_USER_PERSONAL_PHOTO']['HEIGHT']?>" alt="userpic" title="userpic" />
						<?else:?>
							<img src="<?=SITE_TEMPLATE_PATH?>/images/userpic.jpg" width="57" height="57" alt="userpic" title="userpic" />
						<?endif;?>
					</div>
				</div>
				<div class="catalog-review__col">
					<span class="catalog-review__name"><?=$arElement["PROPERTIES"]["USER_ID"]["VALUE"]?></span>
					<span class="catalog-review__date"><?=($arElement["DATE_ACTIVE_FROM"] ? $arElement["DATE_ACTIVE_FROM"] : $arElement["DATE_CREATE"])?></span>
					<span class="catalog-review__text"><?=$arElement["DETAIL_TEXT"]?></span>
				</div>
			</div>
		<?endforeach;?>
	</div>
<?endif;?>

<script type="text/javascript">
	BX.bind(BX("catalog_review_anch"), "click", function() {	
		BX.ReviewsSet =
		{			
			popup: null,
			arParams: {}
		};
		BX.ReviewsSet.popup = BX.PopupWindowManager.create("<?=$popupParams['POPUP_ID']?>", null, {
			autoHide: true,
			offsetLeft: 0,
			offsetTop: 0,			
			overlay: {
				opacity: 100
			},
			draggable: false,
			closeByEsc: false,
			closeIcon: { right : "-10px", top : "-10px"},
			titleBar: {content: BX.create("span", {html: "<?=Loc::getMessage('CATALOG_REVIEWS_TITLE_FULL')?>"})},
			content: "<div class='popup-window-wait'><i class='fa fa-spinner fa-pulse'></i></div>",			
			events: {
				onAfterPopupShow: function()
				{
					if(!BX("<?=$popupParams['POPUP_ID']?>_form")) {
						BX.ajax.post(
							"<?=$this->GetFolder();?>/popup.php",
							{							
								arParams: <?=CUtil::PhpToJSObject($popupParams)?>
							},
							BX.delegate(function(result)
							{
								this.setContent(result);
								var windowSize =  BX.GetWindowInnerSize(),
								windowScroll = BX.GetWindowScrollPos(),
								popupHeight = BX("<?=$popupParams['POPUP_ID']?>").offsetHeight;
								BX("<?=$popupParams['POPUP_ID']?>").style.top = windowSize.innerHeight/2 - popupHeight/2 + windowScroll.scrollTop + "px";
							},
							this)
						);
					}
				}
			}			
		});
		
		BX.addClass(BX("<?=$popupParams['POPUP_ID']?>"), "pop-up forms review");
		close = BX.findChildren(BX("<?=$popupParams['POPUP_ID']?>"), {className: "popup-window-close-icon"}, true);
		if(!!close && 0 < close.length) {
			for(i = 0; i < close.length; i++) {					
				close[i].innerHTML = "<i class='fa fa-times'></i>";
			}
		}

		BX.ReviewsSet.popup.show();		
	});
</script>

<?if(!empty($arParams["CAPTCHA_CODE"])):
	$frame->end();
endif;?>