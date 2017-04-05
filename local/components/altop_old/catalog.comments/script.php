<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require(dirname(__FILE__)."/lang/".LANGUAGE_ID."/script.php");

if(!CModule::IncludeModule("iblock"))
	return;

if($_SERVER["HTTP_X_REQUESTED_WITH"] == "XMLHttpRequest" && ($_POST["METHOD"] == "ask_price" || $_POST["METHOD"] == "comment")) {
	
	$error = "";

	if(!$USER->IsAuthorized()) {		
		if($_POST["CAPTCHA"] == "Y") {
			//Затираем значение введенной капчи
    		echo "<script>$('#comment_captcha_word').attr('value', '');</script>";	
		}		
    	
		//Проверка заполненности Имени
		if(!isset($_POST["NAME"]) || !strlen($_POST["NAME"])) {
			$error .= GetMessage("NAME_NOT_FILLED")."<br>";
		    $return = true;
		}
	}

	//Проверка заполненности текста комментария
    if(!strlen($_POST["TEXT"])) {
        $error .= GetMessage("COMMENT_NOT_FILLED")."<br>";
        $return = true;
    }
	
	//Проверка капчи
    if($_POST["CAPTCHA"] == "Y") {
	    if(!$USER->IsAuthorized() and !$APPLICATION->CaptchaCheckCode($_POST["captcha_word"], $_POST["captcha_sid"])) {
	        $error .= GetMessage("WRONG_CAPTCHA")."<br>";
	        $return = true;
	    }
	}

	//Если есть ошибки, то выдаем текст ошибки
    if($return == true) {
    	//Если в настройках есть параметр использования капчи, обновляем капчу
	    if(!$USER->IsAuthorized() && $_POST["CAPTCHA"] == "Y") {
    		$cCode = $APPLICATION->CaptchaGetCode();
			echo "<script>$('#comment_cImg').attr('src','/bitrix/tools/captcha.php?captcha_sid=".$cCode."');$('#comment_captcha_sid').val('".$cCode."');</script>";
		}
        echo "<span class='alertMsg bad'><i class='fa fa-times'></i><span class='text'>".$error."</span></span>";
        return;
    }

	$_POST["NAME"]	= iconv("UTF-8", SITE_CHARSET, strip_tags(trim($_POST["NAME"])));
	$_POST["TEXT"]	= iconv("UTF-8", SITE_CHARSET, trim($_POST["TEXT"]));

	//Для всех ссылок в тексте комментария добавим rel="nofollow"
	$_POST["TEXT"] = preg_replace("#(https?|ftp)://\S+[^\s.,>)\];'\"!?]#","<a href='\\0' rel='nofollow'>\\0</a>", $_POST["TEXT"]);

	$el = new CIBlockElement;
    $PROPS = array();
	$arProps = explode("/", $_POST["PROPS"]);

	//Если пользователь авторизован, то данные для имени, почты и т.д. берем из его учетной записи, иначе, из полей комментария
    if(!$USER->IsAuthorized()) {
        $PROPS[$arProps[1]] = $_POST["NAME"];
    } else {
		$rsUser = CUser::GetByID($USER->GetID());
		$arUser = $rsUser->Fetch();
        $PROPS[$arProps[1]] = $arUser["LOGIN"];
    }

	//Заполняем остальные свйоства
    $PROPS[$arProps[0]] = $_POST["OID"];
    $PROPS[$arProps[2]] = $_SERVER["REMOTE_ADDR"];
	$PROPS[$arProps[3]] = $_POST["URL"];

	 $arLoadCommentArray = Array(
        "MODIFIED_BY"       => $USER->GetID(),
        "ACTIVE_FROM"       => ConvertTimeStamp(false, "FULL"),
        "IBLOCK_SECTION_ID" => false,
        "IBLOCK_ID"         => $_POST["CID"],
        "PROPERTY_VALUES"   => $PROPS,
        "NAME"              => GetMessage("REVIEW_ON_PRODUCT")." ".iconv("UTF-8", SITE_CHARSET, $_POST["ONAME"]),
        "ACTIVE"            => ($_POST["PRE_MODER"] == "Y" && !$USER->IsAdmin())? "N" : "Y",
        "DETAIL_TEXT"       => $_POST["TEXT"]
    );

	//Добавляем новый коммент
    if($NEW_ID = $el->Add($arLoadCommentArray)) {
		//Коммент успешно дабавлен, очищаем поля формы добавления коммента
        $_POST["NAME"] = "";
		$_POST["TEXT"] = "";

		if($_POST["PRE_MODER"] == "Y" && !$USER->IsAdmin()) {
			echo "<span class='alertMsg good'><i class='fa fa-check'></i><span class='text'>".GetMessage("REVIEW_ADD_AFTER_MODER")."</span></span>";
		} else {
			echo "<span class='alertMsg good'><i class='fa fa-check'></i><span class='text'>".GetMessage("REVIEW_ADD_SUCCESS")."</span></span>";
		}

		echo "<script>$('#new_comment_form .btn_buy').prop('disabled', true).html('".GetMessage("REVIEW_ADDED")."');</script>";
				        
        //Перезагружаем страницу
        echo "<script>window.setTimeout(function(){location.reload()},2000)</script>";
	}
}?>