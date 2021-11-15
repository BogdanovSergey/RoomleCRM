<?php

    function SystemExit($msg = '') {
        global $CONF;
        exit;
    }

    function ExitOnWebAccess() {
        global $CONF;
        if( isset($_SERVER['HTTP_HOST']) ) { // сервисы должны запускаться только в CLI
            MainSecureLog(__FILE__ . ": _SERVER['HTTP_HOST'] is set. Exiting.");
            SystemExit();
        }
    }