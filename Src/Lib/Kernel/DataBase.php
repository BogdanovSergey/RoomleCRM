<?php

    function DBConnect() {
        // подключиться к базе CRM
        global $CONF;
        $GLOBALS['CurrentConn']     = mysql_connect($CONF['CrmDb']['host'], $CONF['CrmDb']['user'], $CONF['CrmDb']['password'], true);
        if (!$GLOBALS['CurrentConn']) {
            die('Could not connect: ' . mysql_error()); }
        $GLOBALS['DBConn']['CrmDb'] = $GLOBALS['CurrentConn'];
        mysql_select_db($CONF['CrmDb']['name'], $GLOBALS['CurrentConn']) or die(mysql_error());
        mysql_query("SET NAMES ".$CONF['CrmDb']['charset'], $GLOBALS['CurrentConn']);

        LoadSysParams(); // загрузить системные настройки из тбл.SysParams
    }


    function connectToDB($dbname = null) { // Старая ф-я
        // this func can be used inside already printed html, do we cant send any headers such as fire php
        //$GLOBALS['Firephp']->info(__FUNCTION__.'()');
        //global $SYS;
        global $CONF;
        if(isset($dbname)) {
            // connect to specific database
            //$GLOBALS['FirePHP']->info(__FUNCTION__.'() '.$CONF[$dbname]['host'].', '.$CONF[$dbname]['user'].', '.$CONF[$dbname]['password']);
            $GLOBALS['CurrentConn'] = mysql_connect($CONF[$dbname]['host'], $CONF[$dbname]['user'], $CONF[$dbname]['password'], true);
            if (!$GLOBALS['CurrentConn']) { die('Could not connect: ' . mysql_error()); }
            $GLOBALS['DBConn']["{$dbname}"] = $GLOBALS['CurrentConn'];
            mysql_select_db($CONF[$dbname]['name'], $GLOBALS['CurrentConn']) or die(mysql_error());
            mysql_query("SET NAMES ".$CONF[$dbname]['charset'], $GLOBALS['CurrentConn']);

            $GLOBALS['FirePHP']->info(__FUNCTION__.'() choosing:'.$dbname);
            $GLOBALS['SelectedDataBaseName'] = $CONF[$dbname]['name']; // logging
        } else {
            // default data base
            $GLOBALS['CurrentConn'] = mysql_connect($CONF['db']['host'], $CONF['db']['user'], $CONF['db']['password'], true);
            if (!$GLOBALS['CurrentConn']) { die('Could not connect: ' . mysql_error()); }
            //$GLOBALS['DBConnDBObjects'] = $GLOBALS['CurrentConn'];
            $GLOBALS['DBConn']['Objects'] = $GLOBALS['CurrentConn'];
            mysql_select_db($CONF['db']['name'], $GLOBALS['CurrentConn']) or die(mysql_error());
            mysql_query("SET NAMES ".$CONF['db']['charset'], $GLOBALS['CurrentConn']);

            $GLOBALS['FirePHP']->info(__FUNCTION__.'() choosing:'.$CONF['db']['name']);
            $GLOBALS['SelectedDataBaseName'] = $CONF['db']['name']; // logging
        }
    }


    function SQLQuery($Sql, $link=null) {
        $out = null;
        if(!$link) {
            $link = $GLOBALS['DBConn']['Objects'];
        }
        $result = mysql_query($Sql, $link);
        if($result) {
            $out = $result;
        } else {
            // log error
            CoreLog(__FUNCTION__."() Error in: \n\"{$Sql}\"\n".
                mysql_error($link).
                "\nSelectedDataBaseName: {$GLOBALS['SelectedDataBaseName']}\n");
        }
        return $out;
    }



    function LoadSysParams($CanReloadSelf=true) {
        global $CONF;
        $changed = false;
        $sql = "SELECT
                    *
                FROM
                    SysParams";
        $res = mysql_query($sql);
        $GLOBALS['FirePHP']->info($sql);
        while($str = mysql_fetch_object($res)) {
            $CONF['SysParams'][ $str->Name ] = $str->Value;
        }
        if($CONF['CheckSysParams'] && $CanReloadSelf==true) {       // проверяем/добавляем настройки по-умолчанию // TODO перенести в режим "Установка и настройка"
//print_r($CONF['SysParams']);exit;
            foreach($CONF['SysParamsVars'] as $key => $val) {
                //if( isset( $CONF['SysParams'][$key] ) && strlen($CONF['SysParams'][$key]) < 1 && strlen($val) >= 1) { // если в базе нет параметра из конфига, вставляем его
                if( !isset( $CONF['SysParams'][$key] ) ) { // если в базе нет параметра из конфига, вставляем его
                    InsertSysParamValue($key, $val);
                    $changed = true;
                }
            }
            if($CanReloadSelf && $changed) { LoadSysParams(false); }            // если что-то менялось, заново загрузить переменные, без последующего цикла
        }
    }

    function GetPublicSysParams() {
        $out = array();
        $sql = "SELECT
                    *
                FROM
                    SysParams
                WHERE
                    Publish=1";
        $res = mysql_query($sql);
        $GLOBALS['FirePHP']->info($sql);
        while($str = mysql_fetch_object($res)) {
            $out[ $str->Name ] = $str->Value;
        }

        // настройки рекламы
        $sql = "SELECT
                    Active,
                    TarifShortName
                FROM
                    BillAdTarifs
                WHERE
                    Active=1";
        $res = mysql_query($sql);
        $GLOBALS['FirePHP']->info($sql);
        while($str = mysql_fetch_object($res)) {
            $out[ 'TrfColumnEnabled_' . $str->TarifShortName ] = $str->Active;
        }

        return $out;
    }

    function GetSysParamValueId($VarName) {
        $sql = "SELECT
                    sp.id AS id
                FROM
                    SysParams AS sp
                WHERE
                    sp.Name = '$VarName'
                    ";
        $GLOBALS['FirePHP']->info($sql);
        $res = mysql_query($sql);
        $str = mysql_fetch_object($res);
        return $str->id;
    }

    function InsertSysParamValue($Name, $Value) {
        $sql = "INSERT INTO SysParams (AddedDate,`Name`,`Value`) VALUES (NOW(), '{$Name}', '{$Value}')";
        $GLOBALS['FirePHP']->warn($sql);
        $res = mysql_query($sql);
        $msg = __FUNCTION__."():  '{$Name}', '{$Value}'";
        $ParamsArr = array();
        $ParamsArr['OnlyMsg'] = true;
        CrmCopyNoticeLog($msg, $ParamsArr);
    }
