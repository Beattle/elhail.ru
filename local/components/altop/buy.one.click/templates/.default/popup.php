<?define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$APPLICATION->ShowAjaxHead();
$APPLICATION->AddHeadScript("/bitrix/js/main/dd.js");

use Bitrix\Main\Application,
	Bitrix\Main\Text\Encoding;

$request = Application::getInstance()->getContext()->getRequest();

$params = $request->getPost("arParams");
if(SITE_CHARSET != "utf-8")
	$params = Encoding::convertEncoding($params, "utf-8", SITE_CHARSET);

$formId = $params["POPUP_ID"]."_form";
$formAction = $params["FORM_ACTION"];
$arParams = $params["PARAMS"];
$arResult = $params["RESULT"];
$arMessage = $params["MESS"];
$btnId = $params["POPUP_ID"]."_btn";

if($arResult["ELEMENT"]["ID"] > 0):?>
	<div class="info">
		<div class="image">
			<?if(is_array($arResult["ELEMENT"]["PREVIEW_PICTURE"])):?>
				<img src="<?=$arResult['ELEMENT']['PREVIEW_PICTURE']['SRC']?>" width="<?=$arResult['ELEMENT']['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$arResult['ELEMENT']['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=$arResult['ELEMENT']['NAME']?>" title="<?=$arResult['ELEMENT']['NAME']?>" />
			<?else:?>
				<img src="<?=SITE_TEMPLATE_PATH?>/images/no-photo.jpg" width="150" height="150" alt="<?=$arResult['ELEMENT']['NAME']?>" title="<?=$arResult['ELEMENT']['NAME']?>" />
			<?endif?>
		</div>
		<div class="name"><?=$arResult["ELEMENT"]["NAME"]?></div>
	</div>
<?endif;?>
<form action="<?=$formAction?>" id="<?=$formId?>">
	<span id="echo_boc_form" class="alert"></span>
	<?foreach($arParams["PROPERTIES"] as $arCode):?>
		<div class="row">
			<div class="span1"><?=$arMessage["FORMS_".$arCode].(in_array($arCode, $arParams["REQUIRED"]) ? "<span class='mf-req'>*</span>" : "");?></div>
			<div class="span2">
				<?if($arCode != "MESSAGE"):?>
					<input type="text" name="<?=$arCode?>" value="<?=($arCode == 'NAME' ? $arResult['USER']['NAME'] : ($arCode == 'EMAIL' ? $arResult['USER']['EMAIL'] : ''));?>" />
				<?else:?>
					<textarea name="<?=$arCode?>" rows="3"></textarea>
				<?endif;?>
			</div>
		</div>
	<?endforeach;
	if(!empty($arParams["CAPTCHA_CODE"])):?>
		<div class="row">
			<div class="span1"><?=$arMessage["FORMS_CAPTCHA"]?><span class="mf-req">*</span></div>
			<div class="span2">					
				<input type="text" name="CAPTCHA_WORD" maxlength="5" value="" />			
				<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arParams['CAPTCHA_CODE']?>" width="127" height="30" alt="CAPTCHA" />
				<input type="hidden" name="CAPTCHA_SID" value="<?=$arParams['CAPTCHA_CODE']?>" />					
			</div>
		</div>
	<?endif;?>
	<input type="hidden" name="PARAMS_STRING" value="<?=$arParams['PARAMS_STRING']?>" />
	<?if($arResult["ELEMENT"]["ID"] > 0):?>
		<input type="hidden" name="ID" value="<?=$arResult['ELEMENT']['ID']?>" />
		<input type="hidden" name="PROPS" value="<?=$arParams['ELEMENT_PROPS']?>" />
		<input type="hidden" name="SELECT_PROPS" value="" />
		<input type="hidden" name="QUANTITY" value="" />
	<?endif;?>	
	<input type="hidden" name="BUY_MODE" value="<?=$arParams['BUY_MODE']?>" />
	<div class="submit">
		<button type="button" id="<?=$btnId?>" class="btn_buy popdef"><?=$arMessage["FORMS_SEND"]?></button>
	</div>
</form>

<script type="text/javascript">
	//MASK//
	var input = $("#<?=$formId?>").find("[name='PHONE']");
	if(!!input)
		input.inputmask("<?=$arParams['PHONE_MASK']?>");
	
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
				var selPropsInput = BX.findChild(BX("<?=$formId?>"), {"attribute": {"name": "SELECT_PROPS"}}, true, false);
				if(!!selPropsInput)
					selPropsInput.value = selPropValue;
			}
		<?endif;?>
	
		//QUANTITY//
		var qntInput = BX.findChild(BX("<?=$formId?>"), {"attribute": {"name": "QUANTITY"}}, true, false);
		if(!!qntInput)
			qntInput.value = BX("quantity_<?=$arParams['ELEMENT_AREA_ID']?>").value;
	<?endif;?>
	
	//FORM_SUBMIT//
	BX.bind(BX("<?=$btnId?>"), "click", BX.delegate(BX.BocFormSubmit, BX));
</script>