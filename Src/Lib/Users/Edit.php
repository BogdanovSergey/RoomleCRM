<?php

    function User_Create($FormArr) {
        $salt = md5(rand(0, 9999). date('r') . rand(0, 9999));
        $options = [
            'cost' => 12,
            'salt' => $salt
        ];
        //$PassHash = password_hash($FormArr['Password1'], CRYPT_BLOWFISH, $options);
        $PassHash = crypt($FormArr['Password1'], $salt);

        $sql = "   INSERT INTO Users
                        ( AddedDate, FirstName,  LastName, MobilePhone, Login, Password, PwSalt, Email,
                          MobilePhone1, MobilePhone2, HomePhone, Gender, Birthday)
                    VALUES (
                        NOW(), '{$FormArr['FirstName']}', '{$FormArr['LastName']}', '{$FormArr['MobilePhone']}', '{$FormArr['Login']}', '$PassHash', '$salt', '{$FormArr['Email']}',
                        '{$FormArr['MobilePhone1']}','{$FormArr['MobilePhone2']}','{$FormArr['HomePhone']}', '{$FormArr['Gender']}', '{$FormArr['Birthday']}'
                    )
                ";
        $GLOBALS['FirePHP']->info($sql);
        $res            = mysql_query($sql);
        $SavedUserId    = mysql_insert_id();
        $msg            = mysql_error();
        return array($res, $SavedUserId, $msg);
    }

    function User_GetUserObj($UserId) {
        $sql = "SELECT
                    *
                FROM
                    Users
                WHERE
                    id = {$UserId}";
        $GLOBALS['FirePHP']->info($sql);
        $res = mysql_query($sql, $GLOBALS['DBConn']['CrmDb']);
        $str = mysql_fetch_object($res);
        return $str;
    }


    function User_Update($FormArr) {

        $UserObj        = User_GetUserObj($FormArr['LoadedUserId']);
        $salt           = User_CreateSalt();
        $SQL['patch']   = '';
        if(strlen($UserObj->PwSalt) <= 1) { $UserObj->PwSalt = $salt; } // если пользователь был вставлен технически, без пароля, даем ему соль

        //$NewOptions     = [ 'cost' => 12, 'salt' => $salt ];
        //$OldOptions     = [ 'cost' => 12, 'salt' => $UserObj->PwSalt ];
        $NewPassHash            = User_MakePasswordHash($FormArr['Password1'], $salt);//password_hash($FormArr['Password1'], CRYPT_BLOWFISH, $NewOptions);
        $NewPassHashWithOldSalt = User_MakePasswordHash($FormArr['Password1'], $UserObj->PwSalt);//password_hash($FormArr['Password1'], CRYPT_BLOWFISH, $OldOptions);

        //if($NewPassHashWithOldSalt === $UserObj->Password) {
            // введен новый пароль и = сохраненному, не меняем пароль
            //$GLOBALS['FirePHP']->info('new = old');

        if($FormArr['ResetPassword'] == '1') {
            // сохраняем новый пароль
            $SQL['patch'] = " Password      = '$NewPassHash', ".
                            " PwSalt        = '$salt', ";

        } else {
            $GLOBALS['FirePHP']->info('Старый пароль не трогаем');
        }



        $sql = "    UPDATE Users SET
                        LastUpdateDate= NOW(),
                        FirstName     = '{$FormArr['FirstName']}',
                        LastName      = '{$FormArr['LastName']}',
                        MobilePhone   = '{$FormArr['MobilePhone']}',
                        MobilePhone1  = '{$FormArr['MobilePhone1']}',
                        MobilePhone2  = '{$FormArr['MobilePhone2']}',
                        HomePhone     = '{$FormArr['HomePhone']}',
                        Login         = '{$FormArr['Login']}',
                        {$SQL['patch']}
                        Email         = '{$FormArr['Email']}',
                        Birthday      = '{$FormArr['Birthday']}',
                        Gender        = '{$FormArr['Gender']}'
                    WHERE
                        id = {$FormArr['LoadedUserId']}
                ";
        $GLOBALS['FirePHP']->info($sql);
        $res = mysql_query($sql);
        $msg = mysql_error();
        return array($res, $msg);
    }

    function User_CheckMobileNumberExist($NumbersArr, $UserId) {
        global $CONF;
        // проверить существуют ли у кого-то такие номера
        $out              = false;
        list($n1,$n2,$n3) = $NumbersArr;
        $SqlIn = "SUBSTRING('$n1', 1, {$CONF['PhoneNumberLength']})";   // обрезаем входящий номер для правильного сравнения
        if(@$UserId > 0)  { $SQLWherePtch = " AND id != $UserId"; } else { $SQLWherePtch=''; }

        if($n2) {$SqlIn .= ", SUBSTRING('$n2', 1, {$CONF['PhoneNumberLength']})";}
        if($n3) {$SqlIn .= ", SUBSTRING('$n3', 1, {$CONF['PhoneNumberLength']})";}
        $sql = "SELECT
                    id, FirstName, LastName, MobilePhone AS m0, MobilePhone1 AS m1, MobilePhone2 AS m2
                FROM
                    Users
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
            $out = array($str->id, $str->LastName .' ' .$str->FirstName, $ExistantNo);
        }
        return $out;
    }


    function User_CheckLoginExist($Login, $UserId) {
        // проверить существует ли в базе такой логин
        $out              = false;
        if(@$UserId > 0)  { $SQLWherePtch = " AND id != $UserId"; } else { $SQLWherePtch=''; }
        $sql = "SELECT
                    id, FirstName, LastName
                FROM
                    Users
                WHERE
                    Login = '$Login'
                    $SQLWherePtch";
        $res = mysql_query($sql);
        $GLOBALS['FirePHP']->info($sql);
        $str = mysql_fetch_object($res);
        if(@$str->id > 0) {
            $out = array($str->id, $str->LastName .' ' .$str->FirstName);
        }
        return $out;
    }

    function User_CheckEmailExist($Email, $UserId) {
        // проверить существует ли в базе такой email
        $out              = false;
        if(@$UserId > 0)  { $SQLWherePtch = " AND id != $UserId"; } else { $SQLWherePtch=''; }
        $sql = "SELECT
                    id, FirstName, LastName
                FROM
                    Users
                WHERE
                    Email = '$Email'
                    $SQLWherePtch";
        $res = mysql_query($sql);
        $GLOBALS['FirePHP']->info($sql);
        $str = mysql_fetch_object($res);
        if(@$str->id > 0) {
            $out = array($str->id, $str->LastName .' ' .$str->FirstName);
        }
        return $out;
    }

