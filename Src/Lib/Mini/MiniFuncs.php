<?php

    function ChangeDateFormat($DateString, $Type) {
        // переводим дату из "15 August 2016 11:44" в "15 авг 2016 11:44"
        global $WORDS;
        if($Type == 'EngMonth2RusShort') {

            $DateString    = preg_replace($WORDS['MonthsEngFullRegexp'], $WORDS['MonthsRusShort'], $DateString);

        }
        return $DateString;

    }