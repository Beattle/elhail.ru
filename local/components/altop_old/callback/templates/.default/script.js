function button_callback(path, email_to, required) {
    $.ajax({
        type: "POST",
        url : path+'/script.php',
        data: ({
            NAME			: $("#callback_name").val(),
            TEL				: $("#callback_tel").val(),
			TIME			: $("#callback_time").val(),
			MESSAGE			: $("#callback_message").val(),
			captcha_word	: $("#callback_captcha_word").val(),
            captcha_sid		: $("#callback_captcha_sid").val(),
			METHOD			: $("#callback_method").val(),			
			EMAIL_TO		: email_to,
			REQUIRED		: required
        }),
        success: function (html) {
            $('#echo_callback_form').html(html);
        }
    });
}