<?php

    function SearchUserIdByPhone($Phone) {
        $out = null;
        if (strlen($Phone) > 0) {
            $sql = "SELECT
                        id
                    FROM
                        Users
                    WHERE
                        MobilePhone  LIKE '$Phone' OR
                        MobilePhone1 LIKE '$Phone' OR
                        MobilePhone2 LIKE '$Phone'";
            $res = mysql_query($sql);
            //$GLOBALS['FirePHP']->info($sql);
            $str = mysql_fetch_object($res);
            if (isset($str->id) && $str->id > 0) {
                $out = $str->id;
            }
        }
        return $out;
    }


