<?php


    function GetClientsArr($Params) {
        $SQL                    = array();
        $SQL['active']          = null;
        $SQL['OnlyUserId']      = '';
        if(isset($Params['Active'])) {
            if($Params['Active'] == 1) { // Относится только к пользователям
                $SQL['active'] = ' AND c.Active=1';
            } else {
                $SQL['active'] = ' AND c.Active=0';
            }
        }
        if(isset($Params['OnlyUserId'])) { // разрешено смотреть только свои объекты
            $SQL['OnlyUserId'] = " AND c.OwnerUserId = " . $Params['OnlyUserId'];
        }

        $SQL['OrderBy'] = " ORDER BY c.{$Params['OrderByField']} {$Params['OrderByTo']}";

        $out = array();
        $sql = "SELECT
                    *
                FROM
                    Clients AS c
                WHERE
                    c.id > 0
                    {$SQL['OnlyUserId']}
                    {$SQL['active']}
                {$SQL['OrderBy']}";
        $res = mysql_query($sql);
        $GLOBALS['FirePHP']->info($sql);
        while($str = mysql_fetch_object($res)) {
            //if(@$Params['HideZeroUsers'] && @$str->UserObjectsSumm <= 0) {
                // just ignore'em
            //} else {
                array_push($out, $str);
            //}
        }
        return $out;
    }


    function Client_CheckMobileNumberExist($NumbersArr, $UserId) {
        global $CONF;
        // проверить существуют ли у кого-то такие номера
        $out              = false;
        list($n1,$n2,$n3) = $NumbersArr;

        if(@$UserId > 0)  { $SQLWherePtch = " AND id != $UserId"; } else { $SQLWherePtch=''; }
        $SqlIn           =  "SUBSTRING('$n1', 1, {$CONF['PhoneNumberLength']})";   // обрезаем входящий номер для правильного сравнения
        if($n2) {$SqlIn .= ", SUBSTRING('$n2', 1, {$CONF['PhoneNumberLength']})";}
        if($n3) {$SqlIn .= ", SUBSTRING('$n3', 1, {$CONF['PhoneNumberLength']})";}
        $sql = "SELECT
                    id, OwnerUserId, FirstName, LastName, MobilePhone AS m0, MobilePhone1 AS m1, MobilePhone2 AS m2
                FROM
                    Clients
                WHERE
                    (MobilePhone  IN ($SqlIn) OR
                     MobilePhone1 IN ($SqlIn) OR
                     MobilePhone2 IN ($SqlIn)
                     ) $SQLWherePtch";
        $res = mysql_query($sql);
        $GLOBALS['FirePHP']->info($sql);
        $str = mysql_fetch_object($res);
        if(@$str->id > 0) {
            if(in_array($str->m0,$NumbersArr)) { $ExistantNo = $str->m0; }
            if(in_array($str->m1,$NumbersArr)) { $ExistantNo = $str->m1; }
            if(in_array($str->m2,$NumbersArr)) { $ExistantNo = $str->m2; }
            $out = array($str->id, $str->LastName .' ' .$str->FirstName, $ExistantNo, $str->OwnerUserId);
        }
        return $out;
    }

    function Client_Create($FormArr) {
        global $CURRENT_USER;
        $sql = "INSERT INTO Clients (
                      AddedDate, FirstName,  LastName, SurName, ClientType,
                      Birthday, Email, MobilePhone,
                      MobilePhone1, MobilePhone2, HomePhone, Description, OwnerUserId)
                VALUES (
                    NOW(),
                    NULLIF('".@$FormArr['FirstName']."',    ''),
                    NULLIF('".@$FormArr['LastName']."',     ''),
                    NULLIF('".@$FormArr['SurName']."',     ''),
                    NULLIF('".@$FormArr['ClientType']."',     ''),
                    NULLIF('".@$FormArr['Birthday']."',     ''),
                    NULLIF('".@$FormArr['Email']."',     ''),
                    NULLIF('".@$FormArr['MobilePhone']."',     ''),
                    NULLIF('".@$FormArr['MobilePhone1']."',     ''),
                    NULLIF('".@$FormArr['MobilePhone2']."',     ''),
                    NULLIF('".@$FormArr['HomePhone']."',     ''),
                    NULLIF('".@$FormArr['Description']."',     ''),
                    {$CURRENT_USER->id}
                )";
        $GLOBALS['FirePHP']->info($sql);
        $res            = mysql_query($sql);
        $SavedUserId    = mysql_insert_id();
        $msg            = mysql_error();
        return array($res, $SavedUserId, $msg);
    }

    function Client_Update($FormArr) {
        global $CURRENT_USER;
        $sql = "UPDATE Clients SET
                        UpdatedDate = NOW(),
                        FirstName   = NULLIF('".@$FormArr['FirstName']."',     ''),
                        LastName    = NULLIF('".@$FormArr['LastName']."',     ''),
                        SurName     = NULLIF('".@$FormArr['SurName']."',     ''),
                        ClientType  = NULLIF('".@$FormArr['ClientType']."',     ''),
                        Birthday    = NULLIF('".@$FormArr['Birthday']."',     ''),
                        Email       = NULLIF('".@$FormArr['Email']."',     ''),
                        MobilePhone = NULLIF('".@$FormArr['MobilePhone']."',     ''),
                        MobilePhone1= NULLIF('".@$FormArr['MobilePhone1']."',     ''),
                        MobilePhone2= NULLIF('".@$FormArr['MobilePhone2']."',     ''),
                        HomePhone   = NULLIF('".@$FormArr['HomePhone']."',     ''),
                        Description = NULLIF('".@$FormArr['Description']."',     '')
                    WHERE
                        OwnerUserId = {$CURRENT_USER->id} AND
                        id          ='{$FormArr['LoadedClientId']}'
                    ";
        $GLOBALS['FirePHP']->info($sql);
        $res            = mysql_query($sql);
        $SavedUserId    = mysql_insert_id();
        $msg            = mysql_error();
        return array($res, $SavedUserId, $msg);
    }


    function ArchivateClientById($ClientId) {
        $sql = "UPDATE Clients SET
                            Active = 0,
                            ArchivedDate = NOW()
                        WHERE
                            id = {$ClientId}";
        $GLOBALS['FirePHP']->info($sql);
        $res = mysql_query($sql);
        return $res;
    }


    function RestoreClientById($ClientId) {
        $sql = "UPDATE Clients SET
                            Active = 1
                        WHERE
                            id = {$ClientId}";
        $GLOBALS['FirePHP']->info($sql);
        $res = mysql_query($sql);
        return $res;
    }