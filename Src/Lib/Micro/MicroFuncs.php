<?php

    function ChangeDateFormat($DateString, $Type) {
        // переводим дату из "15 August 2016 11:44" в "15 авг 2016 11:44"
        global $WORDS;
        if($Type == 'EngDate2RusSHort') {
            preg_replace($pattern, $repl, $DateString);

        }
        return $DateString;

    }