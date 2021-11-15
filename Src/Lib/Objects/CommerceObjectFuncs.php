<?php


    function GetCommerceObjectTypeListArr() {
        $out = array();
        $sql = "SELECT
                    *
                FROM
                    ObjectTypes
                WHERE
                    Active = 1 AND
                    RealtyType = 3
                ORDER BY OrderStep,TypeName";
        $res = mysql_query($sql);
        while($str = mysql_fetch_object($res)) {
            array_push($out, $str);
        }
        return $out;
    }
