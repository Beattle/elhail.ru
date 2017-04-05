<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();
$sComponentFolder = $this->__component->__path;?>

<script type="text/javascript">
	//<![CDATA[
	$(function() {
		$("#boc_anch_cart").click(function(e){
			e.preventDefault();
			$(window).resize(function () {
				modalHeight = ($(window).height() - $("#boc_cart").height()) / 2;
				$("#boc_cart").css({
					'top': modalHeight + 'px'
				});
			});
			$(window).resize();
			$("#boc_body_cart").fadeIn(300);
			$("#boc_cart").fadeIn(300);
		});
		$("#boc_close_cart, #boc_body_cart").click(function(e){
			e.preventDefault();
			$("#boc_body_cart").fadeOut(300);
			$("#boc_cart").fadeOut(300);
		});
	});
	//]]>
</script>

<div class="pop-up-bg boc_body_cart" id="boc_body_cart"></div>
<div class="pop-up boc_cart" id="boc_cart">	
	<a href="javascript:void(0)" class="pop-up-close boc_close_cart" id="boc_close_cart"><i class="fa fa-times"></i></a>
	<div class="h1"><?=GetMessage("MFT_BOC_TITLE");?></div>	
	<form action="<?=$APPLICATION->GetCurPage()?>" class="new_boc_form_cart">
		<span id="echo_boc_form_cart"></span>
		<div class="row">
			<div class="span1">
				<?=GetMessage("MFT_NAME")?><?if(empty($arParams["REQUIRED_ORDER_FIELDS"]) || in_array("NAME", $arParams["REQUIRED_ORDER_FIELDS"])):?><span class="mf-req">*</span><?endif?>
			</div>
			<div class="span2">
				<input type="text" class="input-text" id="boc_name_cart" name="boc_name_cart" value="<?=$arResult['NAME']?>" />
			</div>
			<div class="clr"></div>
		</div>
		<div class="row">
			<div class="span1">
				<?=GetMessage("MFT_TEL")?><?if(empty($arParams["REQUIRED_ORDER_FIELDS"]) || in_array("TEL", $arParams["REQUIRED_ORDER_FIELDS"])):?><span class="mf-req">*</span><?endif?>
			</div>
			<div class="span2">
				<input type="text" class="input-text" id="boc_tel_cart" name="boc_tel_cart" value="" />
			</div>
			<div class="clr"></div>
		</div>
		<div class="row">
			<div class="span1">
				<?=GetMessage("MFT_EMAIL")?><?if(empty($arParams["REQUIRED_ORDER_FIELDS"]) || in_array("EMAIL", $arParams["REQUIRED_ORDER_FIELDS"])):?><span class="mf-req">*</span><?endif?>
			</div>
			<div class="span2">
				<input type="text" class="input-text" id="boc_email_cart" name="boc_email_cart" value="<?=$arResult['EMAIL']?>" />
			</div>
			<div class="clr"></div>
		</div>
		<div class="row">
			<div class="span1">
				<?=GetMessage("MFT_MESSAGE")?><?if(empty($arParams["REQUIRED_ORDER_FIELDS"]) || in_array("MESSAGE", $arParams["REQUIRED_ORDER_FIELDS"])):?><span class="mf-req">*</span><?endif?>
			</div>
			<div class="span2">
				<textarea id="boc_message_cart" name="boc_message_cart" rows="3" cols="30"></textarea>
			</div>
			<div class="clr"></div>
		</div>
		<?if(!$USER->IsAuthorized()):?>
			<div class="row">
				<div class="span1">
					<?=GetMessage('MFT_CAPTCHA');?><span class="mf-req">*</span>
				</div>
				<div class="span2">
					<input type="text" id="boc_captcha_word_cart" name="boc_captcha_word" maxlength="50" value=""/>
					<img id="boc_cImg_cart" src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHA_CODE"]?>" width="127" height="30" alt="CAPTCHA" />
					<input type="hidden" id="boc_captcha_sid_cart" name="boc_captcha_sid" value="<?=$arResult["CAPTCHA_CODE"]?>" />
				</div>
				<div class="clr"></div>
			</div>
		<?endif;?>
		<input type="hidden" id="boc_method_cart" name="boc_method_cart" value="boc"/>
		<input type="hidden" id="boc_personTypeId_cart" name="boc_personTypeId_cart" value="<?=$arParams['DEFAULT_PERSON_TYPE']?>" />
		<input type="hidden" id="boc_propNameId_cart" name="boc_propNameId_cart" value="<?=$arParams['DEFAULT_ORDER_PROP_NAME']?>" />
		<input type="hidden" id="boc_propTelId_cart" name="boc_propTelId_cart" value="<?=$arParams['DEFAULT_ORDER_PROP_TEL']?>" />
		<input type="hidden" id="boc_propEmailId_cart" name="boc_propEmailId_cart" value="<?=$arParams['DEFAULT_ORDER_PROP_EMAIL']?>" />
		<input type="hidden" id="boc_deliveryId_cart" name="boc_deliveryId_cart" value="<?=$arParams['DEFAULT_DELIVERY']?>" />
		<input type="hidden" id="boc_paysystemId_cart" name="boc_paysystemId_cart" value="<?=$arParams['DEFAULT_PAYMENT']?>" />
		<input type="hidden" id="boc_buyMode_cart" name="boc_buyMode_cart" value="<?=$arParams['BUY_MODE']?>" />		
		<input type="hidden" id="boc_dubLetter_cart" name="boc_dubLetter_cart" value="<?=$arParams['DUB']?>" />		
		<div class="submit">
			<button onclick="button_boc('<?=$sComponentFolder?>', '<?=$arResult["REQUIRED"]?>', 'cart');" type="button" name="send_button" class="btn_buy popdef"><?=GetMessage("MFT_BOC_BUTTON");?></button>			
		</div>
	</form>
</div>