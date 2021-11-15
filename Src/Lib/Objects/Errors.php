<?php

/*
 * ObjectsErrorFound($ObjectIdsArr, $ErrorText)
 *
 *
 */

    function ObjectsErrorFound($ObjectIdsArr, $Params) {

        foreach($ObjectIdsArr as $ObjectId) {

            MarkObjectError($ObjectId, 1); // ставим маркер ошибки на объект

            WriteObjectError($ObjectId, $Params); // записываем ошибку в историю объекта в тбл.ObjectErrors


        }

    }

    function MarkObjectError($ObjectId, $HasError) {
        // помечаем наличие/отсутствие ошибки в объекте + Цвет
        global $CONF;
        $SqlColor = '';
        if($HasError == 1) {
            $SqlColor = ", Color = '{$CONF['ObjectColors']['Error']}'";
        }
        $sql = "UPDATE
                    Objects
                SET
                    HasErrors = {$HasError}
                    {$SqlColor}
                WHERE
                    id={$ObjectId}";
        $res = mysql_query($sql);
    }

    function MarkObjectClear($ObjectId) {
        $sql = "UPDATE
                    Objects
                SET
                    HasErrors = 0,
                    Color = NULL
                WHERE
                    id={$ObjectId}";
        $GLOBALS['FirePHP']->info($sql);
        $res = mysql_query($sql);
    }


    function WriteObjectError($ObjectId, $Params) {
        //$Params['ErrorText'] = htmlspecialchars($Params['ErrorText']);
        //$Params['ErrorText'] = mysql_real_escape_string($Params['ErrorText']);
        $sql = "INSERT INTO
                ObjectErrors (
                      AddedDate,
                      ContragentId,
                      ObjectId,
                      EmailId,
                      ErrorText
                    )
                VALUES (
                    NOW(),
                    '{$Params['ContragentId']}',
                    '{$ObjectId}',
                    '{$Params['EmailId']}',
                    '{$Params['ErrorText']}'
                )
                ";
        $res = mysql_query($sql);
    }


    function GetObjectErrorsArr($ObjectId, $Params) {
        $out        = array();
        $outText    = '';
        $SQL['WHERE'] = '';
        // подготовка
        if($Params['Period'] == 'today') {
            $SQL['WHERE'] = ' AND DATE(oe.AddedDate) = DATE(NOW()) ';
            $sql = "SELECT
                            oe.*,
                            bc.ContragentName
                        FROM
                            ObjectErrors AS oe,
                            BillContragents AS bc
                        WHERE
                            oe.ContragentId = bc.id AND
                            oe.ObjectId = $ObjectId
                            {$SQL['WHERE']}
                        ORDER BY AddedDate";
        } elseif($Params['Period'] == 'all') {
            $sql = "SELECT
                            oe.*,
                            bc.ContragentName,
                            DATE(oe.AddedDate) AS AddedDateSHort,
                            DATE(NOW()) AS DateNow
                        FROM
                            ObjectErrors AS oe,
                            BillContragents AS bc
                        WHERE
                            oe.ContragentId = bc.id AND
                            oe.ObjectId = $ObjectId
                        ORDER BY oe.AddedDate DESC";
        }

        $GLOBALS['FirePHP']->info($sql);
        $res = mysql_query($sql);

        while($str = mysql_fetch_object($res)) {
            if($Params['Format'] == 'html') {
                $outText .= "{$str->AddedDate} ошибка в базе {$str->ContragentName}: <b>{$str->ErrorText}</b> (основание: письмо №)<br>";
            } else {
                $out[$str->id] = $str->ErrorText;
            }
            //array_push($out, $str);
        }
        // Пост обработка
        if($Params['Period'] == 'today') {
            $Header = "Ошибки обнаруженные сегодня:<br>";

        } else if($Params['Period'] == 'all') {
            $Header = "История действий с объектом за все время:<br>";

        }

        $out = $Header . $outText;
        return $out;
    }