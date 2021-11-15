<?php

exit;

// Обновлялка размеров фоток

    require('Conf/Config.php');

    DBConnect();

    $sql = "SELECT
                id,
                FilePath
            FROM
                ObjectImages
            ORDER BY `ObjectImages`.`FilePath` DESC";
    $res = mysql_query($sql);

    while($str = mysql_fetch_object($res)) {
        $path = $CONF['SystemPath'] .$str->FilePath;
        list($w_i, $h_i, $type) = getimagesize($path);

        if(file_exists($path)) {
            $sql2 = "
                      UPDATE
                          ObjectImages
                      SET
                          Width = '$w_i', Height = '$h_i'
                      WHERE
                          id = {$str->id}
                ";
            mysql_query($sql2);
        }

    }