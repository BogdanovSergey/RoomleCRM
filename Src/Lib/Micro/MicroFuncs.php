<?php

    function ChangeDateFormat($DateString, $Type) {
        // ��������� ���� �� "15 August 2016 11:44" � "15 ��� 2016 11:44"
        global $WORDS;
        if($Type == 'EngDate2RusSHort') {
            preg_replace($pattern, $repl, $DateString);

        }
        return $DateString;

    }