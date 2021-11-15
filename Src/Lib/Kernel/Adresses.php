<?php


    function GetPlaceTypeSocr($PlaceType) {
        // TODO сравнивать SOCRNAME и $PlaceType маленькими charами
        $out = '';
        $sql = "SELECT
                    ks.SCNAME
                FROM
                    KladrSocr AS ks
                WHERE
                    ks.SOCRNAME = '$PlaceType'
                LIMIT 0,1";
        $res = mysql_query($sql);
        $str = mysql_fetch_object($res);
        // если ввели что-то невнятное, прикрываем
        if(isset($str->SCNAME)) {
            $out = $str->SCNAME;
        }
        return $out;
    }

    function GetSocrArr($Params) {
        $out = array();
        $sql = "SELECT
                    DISTINCT(ks.SCNAME) AS SCNAME
                FROM
                    KladrSocr AS ks
                ";
        $res = mysql_query($sql);
        while($str = mysql_fetch_object($res)) {
            if (isset($str->SCNAME)) {
                if($Params['RegexpCompatible']) {
                    $s = preg_replace("/\//u", '\/', $str->SCNAME);
                    //$s = preg_replace("/-/u", '-', $s);
                    array_push($out, $s);
                } else {
                    array_push($out, $str->SCNAME);
                }

            }
        }
        // kladr hack
        array_push($out, 'пр');
        array_push($out, 'бул');
        array_push($out, 'пр-д');
        return $out;
    }

    function SplitStreetTextOnKladrSocrAndName($Street, $SocrArr) {
        // разделить текст "улицы/проспекта и др." на "название" и "тип".

        //$SocrArr = GetSocrArr();
        $KladrSocr = null;
        foreach($SocrArr as $socr) {
            //if (strpos($Street, $socr)) {
            $StreetRegexpPatterns = array("/\s$socr$/ui", "/\s$socr\./ui"); // "/\s$socr\s/ui"

            foreach($StreetRegexpPatterns as $ptrn) {
//echo "$socr $ptrn\n";
                if(preg_match($ptrn, $Street)) {    // если в тексте улицы найдено сокращение из кладра
                    //echo "MATCH, ptrn: $ptrn, str: $Street, socr: $KladrSocr\n";

                    $Street   = preg_replace($ptrn, '', $Street);
                    $KladrSocr= $socr;

                    //exit;
                    break 2;
                }
            }
        }

        return array($Street, $KladrSocr);
    }