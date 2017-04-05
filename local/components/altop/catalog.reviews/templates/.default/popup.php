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
$btnId = $params["POPUP_ID"]."_btn";?>

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
<form action="<?=$formAction?>" id="<?=$formId?>">
	<span id="echo_reviews_form" class="alert"></span>
	<?foreach($arParams["PROPERTIES"] as $arCode):?>
		<div class="row">
			<div class="span1"><?=$arMessage["REVIEWS_".$arCode]?><span class="mf-req">*</span></div>
			<div class="span2">
				<?if($arCode != "MESSAGE"):?>
					<input type="text" name="<?=$arCode?>" value="<?=($arCode == 'NAME' ? $arResult['USER']['NAME'] : '');?>" />
				<?else:?>
					<textarea name="<?=$arCode?>" rows="3"></textarea>
				<?endif;?>
			</div>
		</div>
	<?endforeach;
	if(!empty($arParams["CAPTCHA_CODE"])):?>
		<div class="row">
			<div class="span1"><?=$arMessage["REVIEWS_CAPTCHA"]?><span class="mf-req">*</span></div>
			<div class="span2">					
				<input type="text" name="CAPTCHA_WORD" maxlength="5" value="" />			
				<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arParams['CAPTCHA_CODE']?>" width="127" height="30" alt="CAPTCHA" />
				<input type="hidden" name="CAPTCHA_SID" value="<?=$arParams['CAPTCHA_CODE']?>" />					
			</div>
		</div>
	<?endif;?>
	<input type="hidden" name="PARAMS_STRING" value="<?=$arParams['PARAMS_STRING']?>" />
	<input type="hidden" name="IBLOCK_STRING" value="<?=$arResult['IBLOCK']['STRING']?>" />
	<input type="hidden" name="ELEMENT_STRING" value="<?=$arResult['ELEMENT']['STRING']?>" />	
	<div class="submit">
		<button type="button" id="<?=$btnId?>" class="btn_buy popdef"><?=$arMessage["REVIEWS_SEND"]?></button>
	</div>
</form>

<script type="text/javascript">
	//FORM_SUBMIT//
	BX.bind(BX("<?=$btnId?>"), "click", BX.delegate(BX.ReviewFormSubmit, BX));
</script>