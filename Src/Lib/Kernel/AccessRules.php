<?php

    function AddAccessRuleIdForUserId($RuleId, $UserId) {
        $out = null;
        // проверяем нет ли уже такого правила у пользователя?
        $Prms['Structure'] = true;
        list($UserGroupsArr, $UserPositionsArr, $AccessRuleObjArr, $UserAccessRuleObj, $UserAccessRuleIds) = GetUserAccessParamsArr( $UserId, $Prms );
        if(!in_array($RuleId,$UserAccessRuleIds)) {
            // добавляем правило
            $sql = "INSERT INTO AccessLinks (AddedDate, AccessRuleId, TargetType, TargetId, AddedUserId)
                                     VALUES (NOW(), $RuleId, 'user', $UserId, 0)";
            $GLOBALS['FirePHP']->info($sql);
            $res = mysql_query($sql);
            $out = true;
        } else {
            // TODO странно - протоколируем
            $out = false;
        }
        return $out;

    }

    function CheckAccessLinkExist($AccessRuleId, $TargetType, $TargetId) {
        $out = false;
        $sql = "SELECT
                    id
                FROM
                    AccessLink
                WHERE
                    AccessRuleId = $AccessRuleId AND
                    TargetType   = '$TargetType' AND
                    TargetId     = $TargetId";
        $res = mysql_query($sql);
        $GLOBALS['FirePHP']->info($sql);
        $str = @mysql_fetch_object($res);
        if(@$str->id > 0) {
            $out = true;
        }
        return $out;
    }

    function AttachRuleToItem($ItemType, $ItemId, $AccessRuleId) {
        $out = false;
        if(!CheckAccessLinkExist($AccessRuleId, $ItemType, $ItemId)) {
            $sql = "INSERT INTO AccessLinks (AddedDate, AccessRuleId, TargetType, TargetId, AddedUserId)
                                         VALUES (NOW(), $AccessRuleId, '$ItemType', $ItemId, 0)";
            $GLOBALS['FirePHP']->info($sql);
            $res = mysql_query($sql);
        }


        return true;
    }

    function RenameStrucItem($Type, $ItemId, $NewName) {
        if($Type == 'position') {
            $sql = "UPDATE UserPositions SET PositionName = '$NewName' WHERE id = $ItemId";
        } elseif($Type == 'group') {
            $sql = "UPDATE UserGroups SET GroupName = '$NewName' WHERE id = $ItemId";
        } elseif($Type == 'status') {
            $sql = "UPDATE UserStatuses SET StatusName = '$NewName' WHERE id = $ItemId";
        } else {
            MainFatalLog(__FUNCTION__ . '(): $Type unknown');
            SystemExit();
        }
        $GLOBALS['FirePHP']->info($sql);
        $res = mysql_query($sql);
        return $res;
    }

    function DeleteAccessRuleIdForUserId($RuleId, $UserId) {
        $out = null;

        $sql = "DELETE FROM AccessLinks WHERE AccessRuleId = $RuleId AND TargetType = 'user' AND TargetId = $UserId";
        $GLOBALS['FirePHP']->info($sql);
        $res = mysql_query($sql);
        if($res) {
            $out = true;
        } else {
            $out = false;
        }
        return $out;

    }

    function GetAccessRulesByTarget($Params, $TargetType, $TargetId) {
        $out = array();
        $sql = "SELECT
                    *
                FROM
                    AccessLinks
                WHERE
                    TargetType = '$TargetType' AND
                    TargetId   = $TargetId";
        $GLOBALS['FirePHP']->info($sql); //
        $res = mysql_query($sql);
        while($str = @mysql_fetch_object($res)) {
            if(isset($Params['OnlyIds'])) {
                array_push($out, $str->AccessRuleId);
            }
        }
        if(count($out) == 0) { array_push($out, 0); }
        return $out;
    }

    function CheckMyRule($RuleName) {
        global $CURRENT_USER;
        if(in_array($RuleName, $CURRENT_USER->AccessRulesArr) ) {
            $permit = true;
        } else {
            $permit = false;
        }
        return $permit;

    }

    function DenyRuleAlert($RulesArr) {
        // TODO доделать распечатку ожидаемых прав .$RulesArr. // У вас отсутствует одно или несколько следующих прав:
        echo '{"success":false,"message":"У вас нет прав на выполнение этой операции"}';
    }
