

$(function() {

    $("#RegForm_LoginBtn").click(function() {
        $.post( "Super.php", {
            Action              : "UserLogin",
            RegForm_LoginNumber : $('#RegForm_LoginNumber').val(),
            RegForm_LoginPass   : $('#RegForm_LoginPass').val()  }, function(ServerResponse) {

            /* out values:
             0 - captcha text is not correct
             1 - success
             */
            //console.log( ServerResponse );

            if(ServerResponse == '0') {
                $('#RegForm_Captcha').val('');
                $('#RegForm_CaptchaErr').html('Правильно введите 6 символов с картинки');
                Shaker();

                $('#RegForm_LoginErr').html('Логин или пароль введены неправильно');
                $('#RegForm_LoginPass').val('');

            } else if(ServerResponse == 1) {
                //LoadCaptcha();
                document.location.href = MainSiteUrl; // обновляем сайт на crm

            } else {
                // TODO save error to server
                Shaker();
                $('#RegForm_LoginErr').html('Логин или пароль введены неправильно');
                $('#RegForm_LoginPass').val('');
            }

        });
    });

    // реакция на клавишу enter
    $("#RegForm_LoginPass, #RegForm_LoginNumber").keypress(function(e) {
        if(e.keyCode == 13) {
            $("#RegForm_LoginBtn").click();
        }
    });


    function Shaker() {
        var ShakeOptions    =    {
            direction: 'left',
            distance: 10,
            times: 2
        };
        $("#RegForm_LoginBtn").prop('disabled', true); // disable button
        var l = $("#WelcomeBox").position().left;
        //var t = $("#WelcomeBox").position().top;
        $("#WelcomeBox").css({'margin-left': 'auto' });
        $("#WelcomeBox").css({'margin-right': 'auto' });

        if ( GetBrowserType() == 'firefox') {        // make some magic
            $("#WelcomeBox").css({'margin-left': l });
            //$("#WelcomeBox").css({'margin-top': t });
        }
        $("#WelcomeBox").effect( 'shake', {}, 500, function(){
            $("#WelcomeBox").css({'margin-left': 'auto' });
            $("#WelcomeBox").css({'margin-right': 'auto' });
            $("#RegForm_LoginBtn").prop('disabled', false); // enable button
        });

    }

});



function GetBrowserType() {
    var out = 'unknown';
    if (navigator.userAgent.toLowerCase().indexOf("firefox") > -1) {
        out = 'firefox';
    }

    if (navigator.userAgent.toLowerCase().indexOf("chrome") > -1) {
        out = 'chrome';
    }

    if (navigator.userAgent.toLowerCase().indexOf("msie") > -1) {
        out = 'msie';
    }
    return out;
}