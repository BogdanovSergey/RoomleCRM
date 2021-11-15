<?php

    function GetNews($Params) {
        $out = array();
        $Count = 3;
        if(isset($Params['Count'])) {
            $Count = $Params['Count'];
        }


        $sql = "SELECT
                  *
                FROM
                  News
                ORDER BY AddedDate DESC
                LIMIT 0,$Count";
        $res = SQLQuery($sql, $GLOBALS['DBConn']['CrmAdminDb']);
        while($str = mysql_fetch_object($res)) {

            $element               = array();
            $element['id']         = $str->id;
            $element['NewsTitle']  = $str->NewsTitle;
            $element['NewsText']   = $str->NewsText;

            array_push($out, $element);
        }

            return $out;
    }