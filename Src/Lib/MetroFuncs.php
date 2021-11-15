<?php


    function GetMetroStationsArr() {
        $out = array();
        $sql = "SELECT
                        *
                    FROM
                        MetroStations
                    WHERE
                        Active = 1
                    ORDER BY StationName";
        $res = mysql_query($sql);
        while($str = mysql_fetch_object($res)) {
            array_push($out, $str);
        }
        return $out;
    }
    function GetMetroStationNameById($id) {
        $sql = "SELECT
                    StationName
                FROM
                    MetroStations
                WHERE
                    id = $id";
        $res = mysql_query($sql);
        $str = mysql_fetch_object($res);
        return $str->StationName;
    }

    function GetMetroStationIdByName($StationName) {
        $out = null;
        $sql = "SELECT
                    id
                FROM
                    MetroStations
                WHERE
                    StationName LIKE '{$StationName}'";
        $res = mysql_query($sql);
        $str = mysql_fetch_object($res);
        if(isset($str->id)) {
            $out = $str->id;
        }
        return $out;
    }

