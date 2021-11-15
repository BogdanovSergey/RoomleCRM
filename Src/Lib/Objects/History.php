<?php

    function GetObjectHistory($ObjectId, $Params) {
        //return GetObjectErrorsArr($ObjectId, $Params);
        return GetFullObjectHistory($ObjectId, $Params);
    }



    function GetFullObjectHistory($ObjectId, $Params) {//
        $outText      = '';
        $SQL['WHERE1'] = '';
        $SQL['WHERE2'] = '';
        if( $Params['Period'] == 'today' && $Params['EventType'] == 'errors') {
            $SQL['WHERE1'] = 'DATE(oer.AddedDate) = CURRENT_DATE() AND ';
            $SQL['WHERE2'] = 'DATE(oev.AddedDate) = CURRENT_DATE() AND ';
        }
        $sql = "
            (    SELECT
                        oer.AddedDate,
                        DATE_FORMAT(oer.AddedDate, '%d %M %Y %H:%i') AS AddedDate2,
                        oer.ContragentId, oer.EmailId, oer.ObjectId, '' AS UserId,
                        oer.ErrorText AS Message,
                        '' AS fio
                 FROM
                        ObjectErrors AS oer
                 WHERE
                        {$SQL['WHERE1']} oer.ObjectId = $ObjectId
            )
            UNION
            (    SELECT
                        oev.AddedDate,
                        DATE_FORMAT(oev.AddedDate, '%d %M %Y %H:%i') AS AddedDate2,
                        '', '', oev.ObjectId, oev.UserId, oev.Message,
                        CONCAT(u.LastName, ' ', u.FirstName) AS fio
                 FROM
                        ObjectEvents AS oev,
                        Users AS u
                 WHERE
                        {$SQL['WHERE2']}
                        oev.ObjectId = $ObjectId AND
                        u.id = oev.UserId
            )
            ORDER BY AddedDate DESC";
        $GLOBALS['FirePHP']->info($sql);
        $res = mysql_query($sql);

        while($str = mysql_fetch_object($res)) {
            $AddedDate    = ChangeDateFormat($str->AddedDate2, 'EngMonth2RusShort');
            $outText .= "{$AddedDate}: {$str->fio} {$str->Message}<br>";
        }
        return $outText;
    }


    function AddObjectEvent($ObjectId, $Params) {
        $Params['UserId']; // Кто осуществил действие
        $Params['Message'] = mysql_real_escape_string($Params['Message']); // Текст действия

        $sql = "INSERT INTO ObjectEvents (
                        AddedDate,
                        ObjectId,
                        UserId,
                        Message
                    )
                VALUES (
                        NOW(),
                        '$ObjectId',
                        '{$Params['UserId']}',
                        '{$Params['Message']}'
                )";
        mysql_query($sql);
        $GLOBALS['FirePHP']->info($sql);
    }
