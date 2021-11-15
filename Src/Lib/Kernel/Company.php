<?php

    function GetCurrentCompanyInfo($Params = array()) {
        global $WORDS;
        $CurrentDomain = mysql_real_escape_string( $_SERVER['HTTP_HOST'] );
        $out = array();
        $sql = "SELECT
                  *,
                  DATE_FORMAT(PayedTill, '%d %M %Y') AS PayedTill,
                  DATEDIFF(PayedTill, NOW()) AS DaysRemain
                FROM
                  Companies
                WHERE
                  ClientDomain = '$CurrentDomain'";
        $GLOBALS['FirePHP']->info( $sql );
        $res = SQLQuery($sql, $GLOBALS['DBConn']['CrmAdminDb']);
        $str = mysql_fetch_object($res);

        return $str;
    }

