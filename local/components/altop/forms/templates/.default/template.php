<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

if(!empty($arParams["CAPTCHA_CODE"])):
	$frame = $this->createFrame($arResult["IBLOCK"]["CODE"].(!empty($arParams["ELEMENT_AREA_ID"]) ? "_".$arParams["ELEMENT_AREA_ID"] : ""))->begin("");
else:
	$this->setFrameMode(true);
endif;

use Bitrix\Main\Localization\Loc;

$popupParams["POPUP_ID"] = $arResult["IBLOCK"]["CODE"].(!empty($arParams["ELEMENT_AREA_ID"]) ? "_".$arParams["ELEMENT_AREA_ID"] : "");
$popupParams["FORM_ACTION"] = $this->__component->__path."/script.php";
$popupParams["PARAMS"] = $arParams;
$popupParams["RESULT"] = $arResult;
$popupParams["MESS"] = array(
	"FORMS_PRICE" => Loc::getMessage("FORMS_PRICE"),
	"FORMS_CAPTCHA" => Loc::getMessage("FORMS_CAPTCHA"),
	"FORMS_SEND" => Loc::getMessage("FORMS_SEND")
);?>

<script type="text/javascript">	
	BX.bind(BX("<?=$arParams['BUTTON_ID']?>"), "click", function() {		
		BX.PopupForm =
		{			
			popup: null,
			arParams: {}
		};
		BX.PopupForm.popup = BX.PopupWindowManager.create("<?=$popupParams['POPUP_ID']?>", null, {
			autoHide: true,
			offsetLeft: 0,
			offsetTop: 0,			
			overlay: {
				opacity: 100
			},
			draggable: false,
			closeByEsc: false,
			closeIcon: { right : "-10px", top : "-10px"},
			titleBar: {content: BX.create("span", {html: "<?=$arResult['IBLOCK']['NAME']?>"})},
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
		
		BX.addClass(BX("<?=$popupParams['POPUP_ID']?>"), "pop-up forms <?=($arResult['ELEMENT']['ID'] > 0 ? 'full' : 'short');?>");
		close = BX.findChildren(BX("<?=$popupParams['POPUP_ID']?>"), {className: "popup-window-close-icon"}, true);
		if(!!close && 0 < close.length) {
			for(i = 0; i < close.length; i++) {					
				close[i].innerHTML = "<i class='fa fa-times'></i>";
			}
		}

		BX.PopupForm.popup.show();		
	});
</script>

<?if(!empty($arParams["CAPTCHA_CODE"])):
	$frame->end();
endif;?>