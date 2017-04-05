<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require(dirname(__FILE__)."/lang/".LANGUAGE_ID."/script.php");

if($_SERVER["HTTP_X_REQUESTED_WITH"] == "XMLHttpRequest" && ($_POST["METHOD"] == "ask_price" || $_POST["METHOD"] == "order")) {
	$error = '';

	$REQUIRED = array();
	$REQUIRED = explode("/", $_POST["REQUIRED"]);

	//Проверка заполненности Имени
	if(empty($REQUIRED) || in_array("NAME", $REQUIRED)) {
		if(!isset($_POST["NAME"]) || !strlen($_POST["NAME"])) {
			$error .= GetMessage("NAME_NOT_FILLED")."<br>";
			$return = true;
		}
	}	

	//Проверка заполненности Телефона
	if(empty($REQUIRED) || in_array("TEL", $REQUIRED)) {
		if(!isset($_POST["TEL"]) || !strlen($_POST["TEL"])) {
			$error .= GetMessage("TEL_NOT_FILLED")."<br>";
			$return = true;
		}
	}

	//Проверка заполненности Времени звонка
	if(empty($REQUIRED) || in_array("TIME", $REQUIRED)) {
		if(!isset($_POST["TIME"]) || !strlen($_POST["TIME"])) {
			$error .= GetMessage("TIME_NOT_FILLED")."<br>";
			$return = true;
		}
	}

	//Проверка заполненности Вопроса
	if(empty($REQUIRED) || in_array("MESSAGE", $REQUIRED)) {
		if(!isset($_POST["MESSAGE"]) || !strlen($_POST["MESSAGE"])) {
			$error .= GetMessage("MESSAGE_NOT_FILLED")."<br>";
			$return = true;
		}
	}

	if(!$USER->IsAuthorized()) {
		//Затираем значение введенной капчи
		if($_POST["METHOD"] == "ask_price") {
			echo "<script>$('#ask_price_captcha_word_".$_POST["ELEMENT_ID"]."').attr('value', '');</script>";
		} elseif($_POST["METHOD"] == "order") {
			echo "<script>$('#order_captcha_word_".$_POST["ELEMENT_ID"]."').attr('value', '');</script>";
		}
		if(!$APPLICATION->CaptchaCheckCode($_POST["captcha_word"], $_POST["captcha_sid"])) {
	        $error .= GetMessage("WRONG_CAPTCHA")."<br>";
	        $return = true;
		}
	}

	//Если есть ошибки, то выдаем текст ошибки
	if($return == true) {
		//обновляем капчу
	    if(!$USER->IsAuthorized()) {
    		$cCode = $APPLICATION->CaptchaGetCode();
			if($_POST["METHOD"] == "ask_price") {
				echo "<script>$('#ask_price_cImg_".$_POST["ELEMENT_ID"]."').attr('src','/bitrix/tools/captcha.php?captcha_sid=".$cCode."');$('#ask_price_captcha_sid_".$_POST["ELEMENT_ID"]."').val('".$cCode."');</script>";
			} elseif($_POST["METHOD"] == "order") {
				echo "<script>$('#order_cImg_".$_POST["ELEMENT_ID"]."').attr('src','/bitrix/tools/captcha.php?captcha_sid=".$cCode."');$('#order_captcha_sid_".$_POST["ELEMENT_ID"]."').val('".$cCode."');</script>";
			}
		}
		echo "<span class='alertMsg bad'><i class='fa fa-times'></i><span class='text'>".$error."</span></span>";
        return;
    }

	// В случае ошибки, заполненные поля сохраняют свои значения
    $_POST["NAME"]		= iconv("UTF-8", SITE_CHARSET, strip_tags(trim($_POST["NAME"])));    
	$_POST["TEL"]		= iconv("UTF-8", SITE_CHARSET, strip_tags(trim($_POST["TEL"])));
	$_POST["TIME"]		= iconv("UTF-8", SITE_CHARSET, strip_tags(trim($_POST["TIME"])));
	$_POST["MESSAGE"]	= iconv("UTF-8", SITE_CHARSET, strip_tags(trim($_POST["MESSAGE"])));
	
	
	//Отправка письма
	$headers = "From: ".$_POST["EMAIL_TO"]."\r\n";
	$headers .= "Content-type: text/plain; charset=KOI8-R\r\n";
	$headers .= "Mime-Version: 1.0\r\n";

	if($_POST["METHOD"] == "ask_price") {
		$title = SITE_SERVER_NAME.": ".GetMessage("MF_ASK_PRICE_MESSAGE_TITLE");
	} elseif($_POST["METHOD"] == "order") {
		$title = SITE_SERVER_NAME.": ".GetMessage("MF_ORDER_MESSAGE_TITLE");
	}

	$message = GetMessage("MF_MESSAGE_INFO")." ".SITE_SERVER_NAME."\r\n";
	$message.= "------------------------------------------\r\n";
	if($_POST["METHOD"] == "ask_price") {
		$message.= GetMessage("MF_ASK_PRICE_MESSAGE_ZAKAZ")."\n";
	} elseif($_POST["METHOD"] == "order") {
		$message.= GetMessage("MF_ORDER_MESSAGE_ZAKAZ")."\n";
	}
	$message.= GetMessage("MF_MESSAGE_NAME")." ".$_POST["NAME"]."\r\n";	
	$message.= GetMessage("MF_MESSAGE_TEL")." ".$_POST["TEL"]."\r\n";
	$message.= GetMessage("MF_MESSAGE_TIME")." ".$_POST["TIME"]."\r\n";
	$message.= GetMessage("MF_MESSAGE_MESSAGE")." ".$_POST["MESSAGE"]."\r\n";
	$message.= GetMessage("MF_MESSAGE_GENERAT")."\r\n";

	if(mail($_POST["EMAIL_TO"], iconv(SITE_CHARSET, "KOI8-R", $title), iconv(SITE_CHARSET, "KOI8-R//TRANSLIT", $message), $headers)) {
		echo "<span class='alertMsg good'><i class='fa fa-check'></i><span class='text'>".GetMessage("MF_OK_MESSAGE")."</span></span>";
		
		if($_POST["METHOD"] == "ask_price") {			
			echo "<script>$('#ask_price_".$_POST["ELEMENT_ID"]." .btn_buy').prop('disabled', true);</script>";
		} elseif($_POST["METHOD"] == "order") {			
			echo "<script>$('#order_".$_POST["ELEMENT_ID"]." .btn_buy').prop('disabled', true);</script>";
		}
	}
}?>