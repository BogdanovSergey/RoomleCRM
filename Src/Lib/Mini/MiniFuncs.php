<?php

    function ChangeDateFormat($DateString, $Type) {
        // ��������� ���� �� "15 August 2016 11:44" � "15 ��� 2016 11:44"
        global $WORDS;
        if($Type == 'EngMonth2RusShort') {

            $DateString    = preg_replace($WORDS['MonthsEngFullRegexp'], $WORDS['MonthsRusShort'], $DateString);

        }
        return $DateString;

    }