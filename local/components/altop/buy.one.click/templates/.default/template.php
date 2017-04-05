<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

if(!empty($arParams["CAPTCHA_CODE"])):
	$frame = $this->createFrame("boc_".$arParams["ELEMENT_AREA_ID"])->begin("");
else:
	$this->setFrameMode(true);
endif;

use Bitrix\Main\Localization\Loc;

$popupParams["POPUP_ID"] = "boc_".$arParams["ELEMENT_AREA_ID"];
$popupParams["FORM_ACTION"] = $this->__component->__path."/script.php";
$popupParams["PARAMS"] = $arParams;
$popupParams["RESULT"] = $arResult;
$popupParams["MESS"] = array(	
	"FORMS_NAME" => Loc::getMessage("FORMS_1CB_NAME"),
	"FORMS_PHONE" => Loc::getMessage("FORMS_1CB_PHONE"),
	"FORMS_EMAIL" => Loc::getMessage("FORMS_1CB_EMAIL"),
	"FORMS_MESSAGE" => Loc::getMessage("FORMS_1CB_MESSAGE"),
	"FORMS_CAPTCHA" => Loc::getMessage("FORMS_1CB_CAPTCHA"),
	"FORMS_SEND" => Loc::getMessage("FORMS_1CB_SEND")
);?>

<script type="text/javascript">	
	BX.bind(BX("<?=$arParams['BUTTON_ID']?>"), "click", function() {
		BX.BocForm =
		{			
			popup: null,
			arParams: {}
		};
		BX.BocForm.popup = BX.PopupWindowManager.create("<?=$popupParams['POPUP_ID']?>", null, {
			autoHide: true,
			offsetLeft: 0,
			offsetTop: 0,
			overlay: {
				opacity: 100
			},
			draggable: false,
			closeByEsc: false,
			closeIcon: { right : "-10px", top : "-10px"},
			titleBar: {content: BX.create("span", {html: "<?=Loc::getMessage('FORMS_1CB_TITLE')?>"})},
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
					} else {
						<?if($arResult["ELEMENT"]["ID"] > 0):
							//SELECT_PROPS//
							if(!empty($arParams["SELECT_PROP_DIV"])):?>
								var selPropValueArr = [];
								ActiveItems = BX.findChildren(BX("<?=$arParams['SELECT_PROP_DIV']?>"), {tagName: "li", className: "active"}, true);
								if(!!ActiveItems && 0 < ActiveItems.length) {
									for(i = 0; i < ActiveItems.length; i++) {
										selPropValueArr[i] = ActiveItems[i].getAttribute("data-select-onevalue");			
									}
								}
								if(0 < selPropValueArr.length) {
									selPropValue = selPropValueArr.join("||");
									var selPropsInput = BX.findChild(BX("<?=$popupParams['POPUP_ID']?>_form"), {"attribute": {"name": "SELECT_PROPS"}}, true, false);
									if(!!selPropsInput)
										selPropsInput.value = selPropValue;
								}
							<?endif;?>
							
							//QUANTITY//
							var qntInput = BX.findChild(BX("<?=$popupParams['POPUP_ID']?>_form"), {"attribute": {"name": "QUANTITY"}}, true, false);
							if(!!qntInput)
								qntInput.value = BX("quantity_<?=$arParams['ELEMENT_AREA_ID']?>").value;
						<?endif;?>
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

		BX.BocForm.popup.show();	
	});
</script>

<?if(!empty($arParams["CAPTCHA_CODE"])):
	$frame->end();
endif;?>