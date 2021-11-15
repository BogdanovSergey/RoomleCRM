<?php

    function ArrToJson($ArrayValues, $DefaultValue = true) {
        // Готовим массив для вывода через json
        $EmptyKeys      = array_values($ArrayValues);
        $KeysWithVal  = (object)[];
        foreach($EmptyKeys as $k) {
            $KeysWithVal->$k = $DefaultValue; //
        }
        return $KeysWithVal;
    }


    function GetAgentsArr($Params) {
        $SQL                    = array();
        $SQL['active']          = null;
        $SQL['ActiveObjects']   = null;
        $SQL['SummSubQuery']    = '';
        $SQL['OrderBySubQuery'] = '';
        $SQL['OnlyUserId']      = '';
        $SQL['OnlyGroups']      = '';
        if(isset($Params['Active'])) {
            if($Params['Active'] == 1) { // Относится только к пользователям
                $SQL['active'] = ' AND u.Active=1';
            } else {
                $SQL['active'] = ' AND u.Active=0';
            }
        } // TODO без параметра Active берем всёх ??
        if(isset($Params['ActiveObjects'])) {
            if($Params['ActiveObjects'] == 1) { // Относится только к объектам
                $SQL['ActiveObjects'] = ' AND o.Active=1';
            } else {
                $SQL['ActiveObjects'] = ' AND o.Active=0';
            }
        }
        if(isset($Params['OnlyUserId'])) { // разрешено смотреть только свои объекты
            $SQL['OnlyUserId'] = " AND u.id = " . $Params['OnlyUserId'];
        }
        if( isset($Params['RealtyType']) ) {
            $SQL['RealtyType'] = " o.RealtyType = '{$Params['RealtyType']}' AND ";
        } else {
            $GLOBALS['FirePHP']->warn('GetAgentsArr(): не указан RealtyType');
            $SQL['RealtyType'] = '';
        }
        if(isset($Params['NoHidden'])) {
            $SQL['NoHidden'] = ' AND u.Hidden=0 ';
        } else {
            // По-умолчанию скрытые отражаются
            $SQL['NoHidden'] = '';
        }
        if(@$Params['WithSumm']) {
            $SQL['SummSubQuery'] = ", (SELECT
                                            COUNT(o.id)
                                       FROM
                                            Objects AS o
                                       WHERE
                                            {$SQL['RealtyType']}
                                            o.OwnerUserId = UserTblId
                                            {$SQL['ActiveObjects']}
                                       GROUP BY
                                            o.OwnerUserId
                                       ) AS UserObjectsSumm";
        }
        if(isset($Params['OrderByField'])) {
            if ($Params['OrderByField'] == 'Group') {
                // сложные сортировки
                $SQL['OrderBySubQuery'] = ", (
                        SELECT
                        	ug.GroupName
                        FROM
                        	UserGroups AS ug,
                        	UserLinks AS ul
                        WHERE
                        	ul.UserId=u.id AND ul.TargetType='group' AND
                        	ug.id = ul.TargetId
                        ) AS UserGroupName";
                $SQL['OrderBy'] = " ORDER BY UserGroupName {$Params['OrderByTo']}";

            } elseif($Params['OrderByField'] == 'Position') {
                $SQL['OrderBySubQuery'] = ", (
                        SELECT
                        	up.PositionName
                        FROM
                        	UserPositions AS up,
                        	UserLinks AS ul
                        WHERE
                        	ul.UserId=u.id AND ul.TargetType='position' AND
                        	up.id = ul.TargetId
                        ) AS UserPositionName";
                $SQL['OrderBy'] = " ORDER BY UserPositionName {$Params['OrderByTo']}";

            } elseif($Params['OrderByField'] == 'Status') {
                $SQL['OrderBySubQuery'] = ", (
                        SELECT
                        	us.StatusName
                        FROM
                        	UserStatuses AS us,
                        	UserLinks AS ul
                        WHERE
                        	ul.UserId=u.id AND ul.TargetType='status' AND
                        	us.id = ul.TargetId
                        ) AS UserStatusName";
                $SQL['OrderBy'] = " ORDER BY UserStatusName {$Params['OrderByTo']}";
            } else {

                $SQL['OrderBy'] = " ORDER BY u.{$Params['OrderByField']} {$Params['OrderByTo']}";
            }
        } else {
            $SQL['OrderBy'] = " ORDER BY u.LastName";
        }

        if(isset($Params['LimitByGroupIdsArr']) ) {
            // по каждому пользвателю берем его группы
            $GrpsStr = implode(",", $Params['LimitByGroupIdsArr']);
            $SQL['OnlyGroups'] = " AND ($GrpsStr) IN (
                                        SELECT
                                            TargetId
                                         FROM
                                                UserLinks AS ul
                                         WHERE
                                                ul.TargetType = 'group' AND
                                                ul.UserId = u.id AND
                                                ul.PrimaryTarget = 1
                                         )"; // TODO берет только ОДИН id отдела!!! а не массив как должно быть в идеале
        }
        $out = array();
        $sql = "SELECT
                    *,
                    DATE_FORMAT(u.AddedDate,'%d %M %Y %H:%i') AS AddedDate,
                    DATE_FORMAT(u.LastEnter,'%H:%i %d %M %Y') AS LastEnter,
                    DATE_FORMAT(u.Birthday, '%d %M %Y') AS Birthday,
                    u.id AS UserTblId
                    {$SQL['SummSubQuery']}
                    {$SQL['OrderBySubQuery']}
                FROM
                    Users AS u
                WHERE
                    u.id > 0
                    {$SQL['OnlyUserId']}
                    {$SQL['active']}
                    {$SQL['NoHidden']}
                    {$SQL['OnlyGroups']}
                {$SQL['OrderBy']}";
        $res = mysql_query($sql);
        $GLOBALS['FirePHP']->info($sql);
        while($str = mysql_fetch_object($res)) {
            if(@$Params['HideZeroUsers'] && @$str->UserObjectsSumm <= 0) {
                // just ignore'em
            } else {
                array_push($out, $str);
            }
        }
        return $out;
    }
    function GetAgentObjById($id) {
        $sql = "SELECT
                    *,
                    CONCAT_WS('', LastName, ' ', FirstName) AS FIO
                FROM
                    Users
                WHERE
                    id = $id";
        $res = mysql_query($sql);
        $str = mysql_fetch_object($res);
        return $str;
    }

    function GetObjectTypesArr($RealtyTypeId) {
        $out = array();
        $sql = "SELECT
                    *
                FROM
                    ObjectTypes
                WHERE
                    Active=1
                ORDER BY TypeName";
        $res = mysql_query($sql);
        while($str = mysql_fetch_object($res)) {
            array_push($out, $str);
        }
        return $out;
    }

    function GetObjectTypeNameById($id) {
        $sql = "SELECT
                    TypeName
                FROM
                    ObjectTypes
                WHERE
                    id = $id";
        $res = mysql_query($sql);
        $str = mysql_fetch_object($res);
        return $str->TypeName;
    }


    function GetObjectParamByIdAndColumn($id, $Column = 'ParamValue') {
        $out = null;
        if($id > 0) {
            $sql = "SELECT
                        IFNULL($Column, 1) AS ObjectParam
                    FROM
                        ObjectParams
                    WHERE
                        id = $id";
            $res = mysql_query($sql);
            // TODO ---
            $str = @mysql_fetch_object($res);
            if( isset($str->ObjectParam) ) {
                $out = $str->ObjectParam;
            }
        }
        return $out;
    }

    function GetObjectParamIdByColumn($val, $ParamType, $Column) {
        $out = null;
        $sql = "SELECT
                    id
                FROM
                    ObjectParams
                WHERE
                    ParamType = '$ParamType' AND
                    $Column = '$val'";
        $res = mysql_query($sql);
        $str = @mysql_fetch_object($res);
        if(isset($str->id)) {
            $out = $str->id; }
        return $out;
    }

    function GetObjectTypeByIdAndColumn($id, $Column = 'TypeName') {
        $out = null;
        if($id > 0) {
            $sql = "SELECT
                        IFNULL($Column, 1) AS ObjectParam
                    FROM
                        ObjectTypes
                    WHERE
                        id = $id";
            $res = mysql_query($sql);
            // TODO ---
            $str = @mysql_fetch_object($res);
            if( isset($str->ObjectParam) ) {
                $out = $str->ObjectParam;
            }
        }
        return $out;
    }



    function GetImagesObjByObjectId($ObjectId) {
        $out = array();
        $sql = "SELECT
                    *
                FROM
                    ObjectImages
                WHERE
                    ObjectId = $ObjectId
                ORDER BY IsPrimary DESC, AddedDate";
        $res = mysql_query($sql);
        while($str = mysql_fetch_object($res)) {
            array_push($out, $str);
        }
        return $out;
    }

    function GetObjectIdByImageId($ImageId) {
        if($ImageId > 0) {
            $sql = "SELECT
                        ObjectId
                    FROM
                        ObjectImages
                    WHERE
                        id = $ImageId";
            $res = mysql_query($sql);
            $GLOBALS['FirePHP']->info($sql);
            $out = mysql_fetch_object($res);
            return $out->ObjectId;
        }
    }

    function LoadObjectImageByImageId($ImageId) {
        $sql = "SELECT
                    *
                FROM
                    ObjectImages
                WHERE
                    id = $ImageId";
        $res = mysql_query($sql);
        return mysql_fetch_object($res);
    }

    function UpdateObjectImagesCount($ObjectId) {
        $sql = "UPDATE Objects SET ImagesCount = (SELECT COUNT(id) FROM ObjectImages WHERE ObjectId={$ObjectId}) WHERE id = {$ObjectId}";
        $GLOBALS['FirePHP']->info($sql);
        $res = mysql_query($sql);
    }
    function DeleteObjectImageByImageId($ImageId) {
        if($ImageId > 0) {
            $ObjectId = GetObjectIdByImageId($ImageId); // сначала получим id объекта по фотке
            $sql = "DELETE FROM
                        ObjectImages
                    WHERE
                        id = $ImageId";
            $GLOBALS['FirePHP']->info($sql);
            $res = mysql_query($sql);
            UpdateObjectImagesCount($ObjectId); // обновляем кол-во картинок
        } else {
            $GLOBALS['FirePHP']->warn("DeleteObjectImageByImageId($ImageId)");
        }
        return $res;
    }

    function GetMaxImagesId() {
        $sql = "SELECT
                    IFNULL( MAX(id), 0) AS id
                FROM
                    ObjectImages
                ";
        $res = mysql_query($sql);
        $str = mysql_fetch_object($res);
        return $str->id;
    }

    function SaveUploadedImageToDb($Obj, $UpdateCount=true, $w, $h) {
        // $Obj->FilePath
        // $Obj->ObjectId
        $sql = "INSERT INTO
                    ObjectImages (PreviewPath, FilePath, ObjectId, Width, Height)
                VALUES
                    ('{$Obj->PreviewPath}', '{$Obj->FilePath}', '{$Obj->ObjectId}', '$w', '$h')";
        $res = mysql_query($sql);
        UpdateObjectImagesCount($Obj->ObjectId); // обновляем кол-во картинок
    }

    function ArchivateObjectById($ObjectId) {
        $sql = "UPDATE Objects SET
                    Active = 0,
                    ArchivedDate = NOW(),
                    ArchivedUserId = 0
                WHERE
                    id = {$ObjectId}";
        $GLOBALS['FirePHP']->info($sql);
        $res = mysql_query($sql);
        return $res;
    }

    function RestoreObjectById($ObjectId) {
        $sql = "UPDATE Objects SET
                    Active = 1
                WHERE
                    id = {$ObjectId}";
        $GLOBALS['FirePHP']->info($sql);
        $res = mysql_query($sql);
        return $res;
    }


    function ArchivateUserById($UserId) {
        $sql = "UPDATE Users SET
                        Active = 0,
                        ArchivedDate = NOW(),
                        ArchivedUserId = 0
                    WHERE
                        id = {$UserId}";
        $GLOBALS['FirePHP']->info($sql);
        $res = mysql_query($sql);
        return $res;
    }

    function GetUserIdByPhone($Phone) {
        global $CONF;
        if(preg_match('/7\d{10}/', $Phone)){ // 79168038020
            $Phone = preg_replace('/^7(\d+)/', '8$1', $Phone);
        }
        $sql = "SELECT
                    id
                FROM
                    Users
                WHERE
                    MobilePhone LIKE '$Phone'";
        $GLOBALS['FirePHP']->info($sql);
        $res = mysql_query($sql);
        $str = mysql_fetch_object($res);
        (@$str->id > 0) ? $out=$str->id : $out=false;
        return $out;
    }

    function RestoreUserById($UserId) {
        $sql = "UPDATE Users SET
                        Active = 1
                    WHERE
                        id = {$UserId}";
        $GLOBALS['FirePHP']->info($sql);
        $res = mysql_query($sql);
        return $res;
    }



    function GetCurrencyNameById($CurrencyId, $ShortFormat=false) {
        // берем название валюты статично, т.к. очень редко используется
        $out = 'ErrorIn_GetCurrencyNameById()'; //TODO log error
        switch($CurrencyId) {
            case 70 :
                ($ShortFormat) ? $out = '₽' : $out = 'RUB';
                break;
            case 71 :
                ($ShortFormat) ? $out = '$' : $out = 'USD';
                break;
            case 72 :
                ($ShortFormat) ? $out = '€' : $out = 'EUR';
                break;
        }
        return $out;
    }



    function GetSummOfObjects($Params) {
        // $Params['RealtyType'] - общий тип недвижимости, обязательный параметр.
        // $Params['ObjectType'] = 0 : все типы объекта;
        // $Params['ObjectType'] > 0 : конкретный тип объекта: комната/доля....
        // $Params['ActiveType'] = 0 : только не активные;
        // $Params['ActiveType'] = 1 : только активные;
        // $Params['ActiveType'] = 2 : и активные и не активные;
        global $CURRENT_USER;
        $SQL = array();
        $SQL['ActiveType'] = null;
        $SQL['and']        = null;
        $SQL['ObjectType'] = null;
        $SQL['WHERE']      = null;
        if($Params['ActiveType'] == 0) {
            $SQL['ActiveType'] = 'Active=0';
        } elseif($Params['ActiveType'] == 1) {
            $SQL['ActiveType'] = 'Active=1';
        } elseif($Params['ActiveType'] == 2) {
            //?
        } else {
            // TODO выход с ошибкой в параметрах
            echo __FUNCTION__.'() params1 error';exit;
        }
        if(@$Params['ObjectType'] > 0 ) {
            $SQL['ObjectType'] = "ObjectType = {$Params['ObjectType']}";
        } else {

        }
        if(isset($SQL['ObjectType']) && isset($SQL['ActiveType'])) {
              $SQL['and'] = 'AND';
        }
        /*if( @$Params['OnlyUserId'] > 0 ) {
            // установлен фильтр на конкретного пользователя
            $SQL['WHERE'] = "OwnerUserId = {$Params['OnlyUserId']} AND ";
        }
        if( CheckMyRule('Objects-All-ShowOnlyMine') ) {     // разрешено смотреть только свои объекты
            $SQL['WHERE'] = "OwnerUserId = {$CURRENT_USER->id} AND ";

        } elseif( CheckMyRule('Objects-LimitByOwnGroup') ) {  // разрешено смотреть объекты только моего отдела
            $SQL['WHERE'] = "OwnerUserId IN (".implode(",", $CURRENT_USER->MyGroupUserIdsArr).") AND ";
        }*/

        $sql = "SELECT
                    COUNT(id) as AllCount
                FROM
                    Objects
                WHERE
                    {$SQL['WHERE']}
                    RealtyType = '{$Params['RealtyType']}' AND
                    {$SQL['ObjectType']}
                         {$SQL['and']}
                    {$SQL['ActiveType']}
                ";
        $GLOBALS['FirePHP']->info($sql);
        $res = mysql_query($sql);
        $str = mysql_fetch_object($res);
        return $str->AllCount;
    }




    function DecodeUUencodedFile($DecodedFilePath, $NewFilePath, $NewFileName) {
        global $CONF;
        $LogParamsArr['OnlyMsg'] = true; // не добавлять лишнего описания в лог
        $UudeviewCmd = "{$CONF['UudeviewCmd']}{$CONF['UudeviewParams']} /tmp/$NewFileName $DecodedFilePath";

        $SOut = system($UudeviewCmd);  // сохраняем файл во временной папке (т.к. Uudeview дает не указывать имя файла на выходе)
        //MainNoticeLog(__FUNCTION__."( $DecodedFilePath, $NewFilePath, $NewFileName )", $LogParamsArr);
        MainNoticeLog(__FUNCTION__." $UudeviewCmd\n$SOut", $LogParamsArr);
        //file_put_contents('/tmp/rc/new/aha', "$DecodedFilePath, $NewFilePath, $NewFileName\n$UudeviewCmd\n$SOut");

        /*if( !copy("/tmp/$NewFileName", $NewFilePath.$NewFileName) ) {
            MainNoticeLog(__FUNCTION__."(): Cant copy file from: \"/tmp/$NewFileName\" to \"{$NewFilePath}$NewFileName\"", $LogParamsArr);
        }*/
    }

    function GetHumanFilesize($bytes, $decimals = 2) {
        $sz = 'BKMGTP';
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
    }