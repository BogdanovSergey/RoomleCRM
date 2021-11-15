<?php

    function SaveSettingsForm($DataArr) {
        global $CONF;
        $out = '';
        // Пролистать локальный массив $CONF['SysParamsVars'] и обновить таблицу пришедшими POSTом данными
         foreach($CONF['SysParamsVars'] as $key => $value) {
             if(isset($DataArr[$key])) {
                 $sql = "  UPDATE SysParams SET
                                LastUpdateDate  = NOW(),
                                `Value`         = '{$DataArr[$key]}'
                            WHERE
                                `Name`          = '{$key}' ";
                 $GLOBALS['FirePHP']->info($sql);
                 $res = mysql_query($sql);
                 if(!$res) {
                     $out .= mysql_error();
                 }
             }
         }
        return $out;
    }

    function LoadSettingsForm() {
        $OutArr   = array();
        $elements = array();
        $sql = "SELECT
                    *
                FROM
                    SysParams";
        $res = mysql_query($sql);
        while($str = mysql_fetch_object($res)) {
            $elements[$str->Name] = $str->Value;
        }
        $response           = (object) array();
        $response->success  = true;
        $response->data     = $elements;
        return $response;
    }