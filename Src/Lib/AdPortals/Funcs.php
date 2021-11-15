<?php

    /*
        function GetAdPortalIdByShortName($ShortName) {
            $sql = "SELECT
                        id
                    FROM
                        AdPortals
                    WHERE
                        PortalShortname = '$ShortName'
                    ";
            $GLOBALS['FirePHP']->info($sql);
            $res = mysql_query($sql);
            $str = mysql_fetch_object($res);
            if( !$str->id ) {
                MainNoticeLog(__FUNCTION__."($ShortName): portal id not found!");
            }
            return $str->id;
        }*/

    function GetBillAdTarifByShortName($ShortName) {
        $sql = "SELECT
                    id, TarifName
                FROM
                    BillAdTarifs
                WHERE
                    TarifShortname = '$ShortName'
                    ";
        $GLOBALS['FirePHP']->info($sql);
        $res = mysql_query($sql);
        $str = mysql_fetch_object($res);
        if( !$str->id ) {
            MainNoticeLog(__FUNCTION__."($ShortName): tarif id not found!");
        }
        return array($str->id, $str->TarifName);
    }

    /*function GetObjectAdPortalState($ObjectId, $PortalId) {
        // Узнаем, есть ли объект среди выгружаемых?
        $out = false;
        $sql = "SELECT
                    id
                FROM
                    AdPortalObjects
                WHERE
                    PortalId = '$PortalId' AND
                    ObjectId = '$ObjectId'
                ";
        $GLOBALS['FirePHP']->info($sql);
        $res = mysql_query($sql);
        $str = mysql_fetch_object($res);
        if( isset($str->id) && $str->id > 0 ) {
            $out = $str->id;
        }
        return $out;
    }*/

    function GetObjectAdTarifState($ObjectId, $TarifId) {
        // Узнаем, назначен ли объект к выгрузке?
        $out = false;
        $sql = "SELECT
                    id
                FROM
                    AdPortalObjects
                WHERE
                    TarifId = '$TarifId' AND
                    ObjectId = '$ObjectId'
                ";
        $GLOBALS['FirePHP']->info($sql);
        $res = mysql_query($sql);
        $str = mysql_fetch_object($res);
        if( isset($str->id) && $str->id > 0 ) {
            $out = $str->id;
        }
        return $out;
    }


    /*function GetObjectAdPortalArr($ObjectId) {
        // берем список порталов куда выгружается объект
        $out = array();
        $sql = "SELECT
                    PortalId
                FROM
                    AdPortalObjects
                WHERE
                    ObjectId = '$ObjectId'
                ";
        //$GLOBALS['FirePHP']->info($sql); // слишком много вывода
        $res = mysql_query($sql);
        while($str = mysql_fetch_object($res)) {
            $out[$str->PortalId] = 1;
        }
        return $out;
    }*/

    function GetObjectAdTarifArr($ObjectId) {
        // берем список порталов куда выгружается объект
        $out = array();
        $sql = "SELECT
                    TarifId
                FROM
                    AdPortalObjects
                WHERE
                    ObjectId = '$ObjectId'
                ";
        //$GLOBALS['FirePHP']->info($sql); // слишком много вывода
        $res = mysql_query($sql);
        while($str = mysql_fetch_object($res)) {
            $out[$str->TarifId] = 1;
        }
        return $out;
    }

    /*function SetObjectAdPortalLoadState($ObjectId, $PortalId) {
        $res = true;
        if( !GetObjectAdTarifState($ObjectId, $PortalId) ) {
            // объекта нет, помечаем его к выгрузке
            $sql = "INSERT INTO
                        AdPortalObjects (ObjectId, PortalId)
                    VALUES ('$ObjectId', '$PortalId')
                    ";
            $GLOBALS['FirePHP']->info($sql);
            $res = mysql_query($sql);
        } else {
            // объект уже есть
        }
        return $res;
    }*/

    function SetObjectAdTarifId($ObjectId, $TarifId) {
        $res = true;
        if( !GetObjectAdTarifState($ObjectId, $TarifId) ) {
            // объекта нет, помечаем его к выгрузке
            $sql = "INSERT INTO
                        AdPortalObjects (ObjectId, TarifId)
                    VALUES ('$ObjectId', '$TarifId')
                    ";
            $GLOBALS['FirePHP']->info($sql);
            $res = mysql_query($sql);
        } else {
            // объект уже есть
        }
        return $res;
    }

    /*function RemoveObjectFromAdPortal($ObjectId, $PortalId) {
        $sql = "DELETE FROM
                    AdPortalObjects
                WHERE
                    PortalId = '$PortalId' AND
                    ObjectId = '$ObjectId'
                ";
        $GLOBALS['FirePHP']->info($sql);
        $res = mysql_query($sql);
        return $res;
    }*/
    function RemoveObjectFromAdTarif($ObjectId, $TarifId) {
        $sql = "DELETE FROM
                    AdPortalObjects
                WHERE
                    TarifId  = '$TarifId' AND
                    ObjectId = '$ObjectId'
                ";
        $GLOBALS['FirePHP']->info($sql);
        $res = mysql_query($sql);
        return $res;
    }

    function TrigerCianCheckboxes($ObjectId, $IncomeTarifShortName) {
        global $CURRENT_USER;
        // при переключении чекбокса циан/цианпремиум автоматически убрать цианпремиум/циан

        if($IncomeTarifShortName == 'TrfCian') {
            list($TarifId, $TarifName) = GetBillAdTarifByShortName('TrfCianPremium');
        } elseif($IncomeTarifShortName == 'TrfCianPremium') {
            list($TarifId, $TarifName) = GetBillAdTarifByShortName('TrfCian');
        }

        if( GetObjectAdTarifState($ObjectId, $TarifId) ) {
            $out = RemoveObjectFromAdTarif($ObjectId, $TarifId);    // убираем противоположный тариф если есть
            $EventParams['Message'] = "остановил выгрузку по тарифу \"$TarifName\"";
            $EventParams['UserId']  = $CURRENT_USER->id;
            $GLOBALS['FirePHP']->info( $EventParams['Message'] );
            AddObjectEvent($ObjectId, $EventParams);
        }
    }


    function UpdateAdTarifObjectState($ObjectId, $TarifShortName, $Value) {
        global $CURRENT_USER;
        $out = false;
        if($ObjectId > 0 && strlen($TarifShortName) > 0 && $Value) {

            list($TarifId, $TarifName) = GetBillAdTarifByShortName($TarifShortName);

            if($TarifShortName == 'TrfCian' || $TarifShortName == 'TrfCianPremium') {
                // триггер на исключение противоположного тарифа циана
                TrigerCianCheckboxes($ObjectId, $TarifShortName);
            }

            if($Value == 'true') {
                $out = SetObjectAdTarifId($ObjectId, $TarifId);
                $EventParams['Message'] = "включил выгрузку по тарифу \"$TarifName\"";
            } else {
                // $Value == 'false'
                $out = RemoveObjectFromAdTarif($ObjectId, $TarifId);
                $EventParams['Message'] = "остановил выгрузку по тарифу \"$TarifName\"";
            }

            $EventParams['UserId']  = $CURRENT_USER->id;
            AddObjectEvent($ObjectId, $EventParams);

        } else {
            // TODO должен быть централизованный лог фатальных ошибок!
            $msg =  __FUNCTION__.'() params error';
            MainFatalLog($msg);
        }
        return $out;
    }

    function ClearAdPortalObjectsForObjectId($ObjectId) {
        $sql = "DELETE FROM AdPortalObjects WHERE ObjectId = {$ObjectId}";
        $GLOBALS['FirePHP']->info($sql);
        mysql_query($sql);
    }

    function GetBillAdTarifsArr($Params = array()) {
        $out = array();
        $sql = "SELECT
                    id
                FROM
                    BillAdTarifs
                ";
        $res = mysql_query($sql);
        while($str = mysql_fetch_object($res)) {
            array_push($out, $str->id);
        }
        return $out;
    }