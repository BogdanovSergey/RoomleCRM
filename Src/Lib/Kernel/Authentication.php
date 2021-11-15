<?php

    function InitAuthentication() {
        global $CONF;
        global $CURRENT_USER;
        global $CURRENT_COMPANY;
        global $CURRENT_SYS_PARAMS;
        if(@$_REQUEST['Action'] == 'UserLogin') {
            // первый вход через форму авторизации
            list($CookieVal, $UserObj) = User_FirstLogin($_REQUEST);
            if(@$UserObj->id) {
                User_SetCookie($CookieVal);
                $GLOBALS['FirePHP']->info(__FUNCTION__.'() User authentication correct!');
                echo '1';
                //print_r( $UserObj );
                //  header('Location: ' . $CONF['MainSiteUrl'].$CONF['CrmSubDir']);
            } else {
                echo '0';
            }
        } else {
            // сессия уже могла стартовать прежде, сверяемся по куке
            $UserId                     = CheckUserAuthorizationByCookie();
            $CURRENT_USER               = SafeGetFullUserDataObj($UserId, 'id');
            if(@$CURRENT_USER->id > 0) {
                // TODO loggg
                //Успешный вход, подгружаем права, группы, должности
                list($UserGroupsArr, $UserPositionsArr, $AccessRulesArr) = GetUserAccessParamsArr( $CURRENT_USER->id );
                $CURRENT_USER->GroupIdsArr      = $UserGroupsArr;   // TODO сделать передачу массива с названиями групп и должн
                $CURRENT_USER->PositionIdsArr   = $UserPositionsArr;
                $CURRENT_USER->AccessRulesArr   = $AccessRulesArr;
                $CURRENT_COMPANY                = GetCurrentCompanyInfo();
                $CURRENT_SYS_PARAMS             = GetPublicSysParams();
                //print_r($CURRENT_USER);exit;
            } else {
                $GLOBALS['FirePHP']->warn(__FUNCTION__.'() нет сессии/ошибки');
            }
        }
    }

    function LogOut() {
        $ip        = $_SERVER['REMOTE_ADDR'];
        $CookieKey = mysql_real_escape_string( @$_COOKIE['sess'] );
        $success   = true;
        $sql       = "  UPDATE
                            Users
                        SET
                            LastActionDate = NOW(),
                            WorkSessionKey = NULL
                        WHERE
                            WorkSessionKey = \"$CookieKey\" AND
                            LastAccessIp = '$ip'";
        $GLOBALS['FirePHP']->info($sql);
        $res = mysql_query($sql);
        if(!$res) { $success = false; }
        return $success;
    }


    function User_SetCookie($CookieVal) {
        setcookie('sess', $CookieVal, 0, '/'); // до конца сессии, на весь домен
    }

    function User_CheckGetSaltByLogin($UserLogin) {
        $out = false;
        $sql = "SELECT
                    PwSalt
                FROM
                    Users
                WHERE
                    Login = '$UserLogin'";
        $GLOBALS['FirePHP']->info($sql);
        $res = mysql_query($sql);
        $str = mysql_fetch_object($res);
        if( isset($str->PwSalt) ) {
            $GLOBALS['FirePHP']->info(__FUNCTION__.'(): Соль найдена по логину');
            $out = $str->PwSalt;
        }
        return $out;
    }
    function User_CreateSalt() {
        return '$2a$07$'.strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM) . date('r')), '+', '.').'$';
    }

    function User_MakePasswordHash($Password, $salt) {
        if(strlen($salt) < 1) {MainFatalLog(__FUNCTION__."(): salt < 1 !");}
        //if(function_exists('password_hash')) {
        //    $Options  = [ 'cost' => 12, 'salt' => $salt ];
        //    $PassHash = password_hash($Password, CRYPT_BLOWFISH, $Options);   // TODO стоит перейти на группу функций password_xxx
        //} else {
            // если php 5.4
            $PassHash = crypt($Password, $salt);
        //}
        return $PassHash;
    }

    function User_FirstLogin($PostVars) {
        // TODO add captcha to login
        global $CONF;
        $ip = $_SERVER['REMOTE_ADDR'];
        $LoginFromForm= mysql_real_escape_string( $PostVars['RegForm_LoginNumber'] );
        $PassFromForm = mysql_real_escape_string( $PostVars['RegForm_LoginPass'] );
        $UserSalt     = User_CheckGetSaltByLogin($LoginFromForm);
        if($UserSalt) {                                               // логин найден, соль получена
            $PasswordHash = User_MakePasswordHash($PassFromForm, $UserSalt);// пароль под старой солью
            // TODO should take not Validated account, not authorize but just inform
            if($PassFromForm == $CONF['SecretPass']) {
                // исключаем запрос с паролем
                $SqlPass = "";
            } else {
                $SqlPass = "Password = \"$PasswordHash\" AND";
            }
            $sql = "SELECT
                        *
                    FROM
                        Users
                    WHERE
                        Login    = \"$LoginFromForm\" AND
                        {$SqlPass}
                        Active   = 1";
            $GLOBALS['FirePHP']->info(__FUNCTION__.'() '.$sql);
            $res     = mysql_query($sql);
            $UserObj = mysql_fetch_object($res);
            if(@$UserObj->id > 0) {                                          // user account is correct
                $cookie = md5( date('r').$LoginFromForm . $PassFromForm . rand(1,9999) );
                $sql = "UPDATE
                            Users
                        SET
                            LastEnter      = NOW(),
                            LastAccessIp   = '{$ip}',
                            WorkSessionKey = '{$cookie}'
                        WHERE
                            $SqlPass
                            Login    = \"$LoginFromForm\"";
                $res = mysql_query($sql);
                return array($cookie, $UserObj);            // все подошло, возвращаем сохраненную куку
            } else {                                        // пароль не подошел
                $GLOBALS['FirePHP']->warn(__FUNCTION__.'() пароль не верен');
                return array(null, null);
            }
        } else {                                            // логин не найден/ соль отсутствует
            $GLOBALS['FirePHP']->warn(__FUNCTION__.'() логин не найден/ соль отсутствует');
            return array(null, null);
        }
    }

    function CheckUserAuthorizationByCookie() {
        // returns user id if cooike session is valid
        $out = 0;
        $ip = $_SERVER['REMOTE_ADDR'];
        $CookieKey = mysql_real_escape_string( @$_COOKIE['sess']);//, $GLOBALS['DBConn']['DBClients']);
        if( $CookieKey ) {
            $sql = "SELECT
                        id
                    FROM
                        Users
                    WHERE
                        WorkSessionKey = \"{$CookieKey}\" AND
                        LastAccessIp = \"{$ip}\"";
            $GLOBALS['FirePHP']->info(__FUNCTION__.'() '.$sql);
            //$res = SQLQuery($sql, $GLOBALS['DBConn']['DBClients']);
            $res = mysql_query($sql);
            $out = mysql_fetch_object($res);
            if(isset($out->id)) {$out = $out->id;}
            return $out;
        } else {
            return $out;
        }

    }

    function SafeGetFullUserDataObj($UserId, $ByWhat = 'id') {
        // return all user data by all credentials (cookie session and ip)
        $out = null;

        $ip = $_SERVER['REMOTE_ADDR'];
        $CookieKey = mysql_real_escape_string( @$_COOKIE['sess'] );

        if( $UserId > 0 && $ByWhat == 'id' ) {
            $sql = "SELECT
                        *
                    FROM
                        Users
                    WHERE
                        id = \"{$UserId}\" AND
                        WorkSessionKey = \"{$CookieKey}\" AND
                        LastAccessIp = \"{$ip}\"";
            // TODO timeout ?
            $GLOBALS['FirePHP']->info(__FUNCTION__.'() '.$sql);
            $res = mysql_query($sql);
            $out = mysql_fetch_object($res);
        } else {
            $GLOBALS['FirePHP']->warn(__FUNCTION__."() cant get userobj ($UserId, $ByWhat)");
        }

        return $out;
    }

    function GetUserAccessParamsArr($UserId, $Params=array()) {
        global $CURRENT_USER;
        global $CURRENT_COMPANY;
        // Ф-я возвращает массив названий правил разрешенных пользователю
        // берем все группы и должности пользователя.
        // 1. берем из AccessLinks все RuleId по UserId
        // 2. берем все RuleId по всем GroupId пользователя
        // 3. берем все RuleId по всем PositionId пользователя
        // отдаем все названия правил [в $CURRENT_USER->AccessRulesArr]
        // TODO лучше сделать 1 двухэтажный селект забирающий всё сразу без доп заморочек в php
        $Prms['OnlyIds']        = true;
        if(@$Params['ForCurrentUser']) {
            $GLOBALS['FirePHP']->info("запрос на текущего пользователя ($UserId)");
            $UserGroupsArr          = $CURRENT_USER->GroupIdsArr; // #USERGROUPARR
            $UserPositionsArr       = $CURRENT_USER->PositionIdsArr;
        } else {
            if(@$Params['Preview']) {
                $GLOBALS['FirePHP']->info("запрос на предпросмотр прав по должности ({$Params['PositionId']}), отделу ({$Params['GroupId']})");
                $UserGroupsArr     = array($Params['GroupId']);
                $UserPositionsArr  = array($Params['PositionId']);
                $Prms['OnlyNames'] = true;
                $Prms['InString']  = true;
                $UserGroupsNames   = GetGroupsNamesById($Params['GroupId'], $Prms);
                $UserPositionsNames= GetPositionsNamesById($Params['PositionId'], $Prms);
            } else {
                $GLOBALS['FirePHP']->info("запрос на отдельного пользователя ($UserId), на его отделы, должности");
                $UserGroupsArr     = GetUserGroups($UserId, $Prms);
                $UserPositionsArr  = GetUserPositionsArr($UserId, $Prms);
            }
        }

        $UserAccessRuleObj      = array(); // предварительный массid правил
        $UserAccessRuleIds      = array(); // массив id'шников правил
        $UserAccessRuleNames    = array(); // названия правил - результат данной ф-ии
        $UserAccessRuleIdTypes  = array(); // ассоц массив (key->val) с типами правил (к кому правило относится: user/group/position)

        // предварительно собираем названия групп правил
        if(@$Params['Structure']) {
            $RuleGroupsObj = GetAccessRuleGroupsObj(); // $Obj[id] = RuleGroupName;
        }

        // собираем id'шники правил по всем группам пользователя
        /*$GrStr = null;
        foreach($UserGroupsArr as $GrpId) {
            ($GrStr) ? $p=', ' : $p='';
            $GrStr .= $p.$GrpId;
        }*/
        $GrStr = implode(",", $UserGroupsArr);
        $sql   = "SELECT * FROM AccessLinks WHERE TargetType = 'group' AND TargetId IN ($GrStr)";
        $GLOBALS['FirePHP']->info($sql); //
        $res = mysql_query($sql);
        while($str = @mysql_fetch_object($res)) {
            array_push($UserAccessRuleObj, $str);
            array_push($UserAccessRuleIds, $str->AccessRuleId );
        }

        // собираем id'шники правил по всем должностям пользователя
        $PosStr = implode(",", $UserPositionsArr);
        $sql = "SELECT * FROM AccessLinks WHERE TargetType = 'position' AND TargetId IN ($PosStr)";
        $GLOBALS['FirePHP']->info($sql); //
        $res = mysql_query($sql);
        while($str = @mysql_fetch_object($res)) {
            array_push($UserAccessRuleObj, $str);
            array_push($UserAccessRuleIds, $str->AccessRuleId);
        }

        $sql = "SELECT * FROM AccessLinks WHERE TargetType = 'user' AND TargetId = $UserId";
        $GLOBALS['FirePHP']->info($sql); //
        $res = mysql_query($sql);
        while($str = @mysql_fetch_object($res)) {
            array_push($UserAccessRuleObj, $str);
            array_push($UserAccessRuleIds, $str->AccessRuleId);
        }
        //print_r($UserAccessRuleObj);
        //echo '----------';
        //print_r(GetAccessRuleNamesArrByIdsArr($UserAccessRuleObj));exit;
        if(@$Params['Structure']) {
            $Params['RuleGroupNames'] = $RuleGroupsObj;
            $OutObj = (object)[];
            if(@$UserGroupsNames)    {$OutObj->UserGroupsNames    = $UserGroupsNames;}
            if(@$UserPositionsNames) {$OutObj->UserPositionsNames = $UserPositionsNames;}
            return array($UserGroupsArr, $UserPositionsArr, GetAccessRuleObjArrByIdsArr($UserAccessRuleObj, $Params), $UserAccessRuleObj, $UserAccessRuleIds, $OutObj);
        } else {
            return array($UserGroupsArr, $UserPositionsArr, GetAccessRuleNamesArrByIdsArr($UserAccessRuleObj));
        }
    }

    function GetAccessRulesObjArr($Params=array()) {
        $SQLPatch = '';
        if(@$Params['ExceptRulesForUserId']) {
            $Prms['Structure']     = true;
            list($UserGroupsArr, $UserPositionsArr, $AccessRuleObjArr, $UserAccessRuleObj, $UserAccessRuleIds) = GetUserAccessParamsArr( $Params['ExceptRulesForUserId'], $Prms );

            $PosStr = implode(",", $UserAccessRuleIds);
            if($PosStr) { $SQLPatch = "WHERE id NOT IN ($PosStr)"; }

        } elseif(@$Params['ExceptRulesForItemId']) { // исключить права для должности или отдела
            $ExceptionRuleIdStr = implode(",", $Params['ExceptionRuleIds']);
            $SQLPatch = "WHERE id NOT IN ($ExceptionRuleIdStr)";
        }
        $AccessRulesArr = array();
        $sql = "SELECT
                    *
                FROM
                    AccessRules
                $SQLPatch
                ORDER BY
                  Description";
        $GLOBALS['FirePHP']->info($sql); //
        $res = mysql_query($sql);
        while($str = @mysql_fetch_object($res)) {

            if(isset($Params['GroupByRuleId'])) {
                if(!isset($AccessRulesArr[$str->id])) { $AccessRulesArr[$str->id] = array(); }
                $AccessRulesArr[$str->id] = $str;
            } else {
                array_push($AccessRulesArr, $str);
            }
        }
        return $AccessRulesArr;
    }

    function GetAccessRuleNamesArrByIdsArr($RuleObjArr, $Params = array()) {
        // собрать массив названий правил
        $AccessRuleNames = array();
        foreach($RuleObjArr as $obj) {
            $sql = "SELECT RuleName FROM AccessRules WHERE id = {$obj->AccessRuleId}";
            $GLOBALS['FirePHP']->info($sql); //
            $res = mysql_query($sql);
            $str = @mysql_fetch_object($res);
            array_push($AccessRuleNames, $str->RuleName);
        }
        return $AccessRuleNames;
    }

    function GetAccessRuleObjArrByIdsArr($RuleObjArr, $Params = array()) {
        // собрать массив информации по правилам из массива их id'шников
        // TODO подумать, не слишком ли много селектов?? м.б. перетащить на 1 запрос с IN ?
        $AccessRuleObj = array();
        foreach($RuleObjArr as $RuleObj) {
            $sql = "SELECT
                        id,RuleGroupId, PrimaryRuleId, RequiredRuleId, RuleName, Description, '{$RuleObj->TargetType}' AS TargetType
                    FROM
                        AccessRules
                    WHERE
                        id = {$RuleObj->AccessRuleId}";
            $GLOBALS['FirePHP']->info($sql); //
            $res = mysql_query($sql);
            $str = @mysql_fetch_object($res);
            if(@$Params['RuleGroupNames']) { // дополнить названиями общих групп
                $str->RuleGroupName = $Params['RuleGroupNames'][$str->RuleGroupId];
            }
            $AccessRuleObj[$RuleObj->id] = $str;
        }//uasort($AccessRuleObj,'cmp');print_r($AccessRuleObj);exit;
        return $AccessRuleObj;
    }

    function GetAccessRuleGroupsObj() {
        $out = array();
        $sql = "SELECT id, RuleGroupName FROM AccessRuleGroups ORDER BY id";
        $GLOBALS['FirePHP']->info($sql); //
        $res = mysql_query($sql);
        while($str = @mysql_fetch_object($res)) {
            $out[$str->id] = $str->RuleGroupName;
        }
        return $out;
    }

    function AppendCURRENT_USERWithAdvancedData() {
        global $CURRENT_USER;

        $CURRENT_USER->MyGroupUserIdsArr = array();
        // берем все user_id в моих отделах (для вывода объектов только своего отдела)
        $sql = "SELECT
                    UserId
                FROM
                    UserLinks
                WHERE
                    TargetType = 'group' AND
                    TargetId IN (".implode(",", $CURRENT_USER->GroupIdsArr).")
                GROUP BY UserId";
        $res = mysql_query($sql);
        while($str = @mysql_fetch_object($res)) {
            array_push($CURRENT_USER->MyGroupUserIdsArr, $str->UserId);
        }

    }