<?php

/*
 *
 *

SELECT
	u.FirstName, u.LastName, u.MobilePhone, u.Login, u.Email, up.PositionName
FROM
	Users as u,
    UserLinks as ul,
    UserPositions as up

WHERE
 	u.Active = 1 AND
    ul.UserId = u.id AND
    ul.TargetType = 'position' and
    ul.TargetId =  up.id


 *
 */


    function GetGroupsArr($Params) {
        $SQL = array();
        $out = array();
        if(isset($Params['Active'])) {
            if($Params['Active'] == 1) { // Относится только к пользователям
                $SQL['active'] = ' AND Active=1';
            } else {
                $SQL['active'] = ' AND Active=0';
            }
        }
        if(isset($Params['OrderByField'])) {
            $SQL['OrderBy'] = " ORDER BY {$Params['OrderByField']} {$Params['OrderByTo']}";
        } else {
            $SQL['OrderBy'] = " ORDER BY Ordering";
        }

        $sql = "SELECT
                    *
                FROM
                    UserGroups
                WHERE
                    id > 0
                    {$SQL['active']}
                    {$SQL['OrderBy']}";
        $res = mysql_query($sql);
        $GLOBALS['FirePHP']->info($sql);
        while($str = mysql_fetch_object($res)) {
            array_push($out, $str);
        }
        return $out;
    }

    function GetMainUserGroup($UserId, $Params=array()) {
        $GroupName  = '';
        $Count      = 0;
        $GroupId = null;
        $HasPrimary = false;
        $sql = "SELECT
                    UL.id,
                    UL.TargetId AS GroupId,
                    UL.PrimaryTarget,
                    (SELECT UG.GroupName FROM UserGroups AS UG WHERE UG.id = UL.TargetId) AS GroupName
                FROM
                    UserLinks AS UL
                WHERE
                    UserId     = $UserId AND
                    TargetType = 'group'
                ORDER BY
                    UL.PrimaryTarget DESC";
        $res = mysql_query($sql);
        //$GLOBALS['FirePHP']->info($sql); // много вывода
        while($str = mysql_fetch_object($res)) {
            $Count++;
            if($str->PrimaryTarget) {
                $HasPrimary =true;
                $GroupName .= $str->GroupName;
                ($GroupId) ? $p=',' : $p='';
                $GroupId .= $p.$str->GroupId; // TODO не задействовано при нескольких группах, выводим
            }
        }
        //if(!$HasPrimary) {
        //  $GroupName = 'отсутствует';
        //}
        if(@$Params['WithCount'] && $Count > 1) { $GroupName .= " ($Count)"; }

        if(@$Params['WithId'] ) {
            return array($GroupName, $GroupId);
        } else {
            return $GroupName;
        }

    }

    function GetUserGroups($UserId, $Params = array()) {
        $out = array();
        $sql = "SELECT
                    UL.id,
                    UL.TargetId AS GroupId,
                    (SELECT UG.GroupName FROM UserGroups AS UG WHERE UG.id = UL.TargetId) AS GroupName
                FROM
                    UserLinks AS UL
                WHERE
                    UserId     = $UserId AND
                    TargetType = 'group'
                ORDER BY
                    UL.PrimaryTarget DESC";
        $res = mysql_query($sql);
        //$GLOBALS['FirePHP']->info($sql); // много вывода
        while($str = mysql_fetch_object($res)) {
            if(@$Params['OnlyIds']) {
                array_push($out, $str->GroupId);
            } elseif(@$Params['OnlyNames']) {
                if(@$Params['InString']) {
                    ($out) ? $p=', ' : $p='';
                    $out = (string)$out . $p.$str->GroupName;
                }
            } else {
                array_push($out, $str);
            }
        }
        return $out;
    }

    function GetGroupsNamesById($GroupArr, $Params = array()) { // TODO возможна передача массива
        $out = array();
        if($GroupArr) {
            $sql = "SELECT
                        GroupName
                    FROM
                        UserGroups
                    WHERE
                        id = $GroupArr
                    ORDER BY
                        GroupName";
            $res = mysql_query($sql);
            $GLOBALS['FirePHP']->info($sql);
            while($str = mysql_fetch_object($res)) {
                if(@$Params['OnlyNames']) {
                    if(@$Params['InString']) {
                        if($out) { $p=', '; } else { $out = ''; $p=''; }
                        $out = $out . $p.$str->GroupName;
                    }
                }
            }
        }
        return $out;
    }

    function GetPositionsNamesById($PositionsArr, $Params = array()) { // TODO возможна передача массива
        $out = array();
        if($PositionsArr) {
            $sql = "SELECT
                        PositionName
                    FROM
                        UserPositions
                    WHERE
                        id = $PositionsArr
                    ORDER BY
                        PositionName";
            $res = mysql_query($sql);
            $GLOBALS['FirePHP']->info($sql);
            while($str = mysql_fetch_object($res)) {
                if(@$Params['OnlyNames']) {
                    if(@$Params['InString']) {
                        if($out) { $p=', '; } else { $out = ''; $p=''; }
                        $out = $out . $p.$str->PositionName;
                    }
                }
            }
        }
        return $out;
    }

    function GetPositionsOrGroupsObjArr($Type, $Params = array()) {
        $SQL['active']  = '';
        $SQL['OrderBy'] = '';
        $out            = array();
        if($Type == 'position') {
            $Field = 'PositionName';
            $Table = 'UserPositions';
        } elseif($Type == 'group') {
            $Field = 'GroupName';
            $Table = 'UserGroups';
        } else {
            MainFatalLog(__FUNCTION__ . '(): $Type unknown');
            SystemExit();
        }
        if(isset($Params['Active'])) {
            if($Params['Active'] == 1) { // Относится только к пользователям
                $SQL['active'] = ' AND Active=1';
            } else {
                $SQL['active'] = ' AND Active=0';
            }
        }
        if(isset($Params['OrderByField'])) {
            $SQL['OrderBy'] = " ORDER BY {$Params['OrderByField']} {$Params['OrderByTo']}";
        } else {
            $SQL['OrderBy'] = " ORDER BY " . $Field;
        }
        $sql = "SELECT
                    *,
                    $Field AS ItemName
                FROM
                    $Table
                WHERE
                    id > 0
                    {$SQL['active']}
                    {$SQL['OrderBy']}";
        $res = mysql_query($sql);
        $GLOBALS['FirePHP']->info($sql);
        while($str = mysql_fetch_object($res)) {
            array_push($out, $str);
        }
        return $out;
    }

    function GetItemsAndRightsStructure($ItemType, $Params = array()) {
        // Универсальная ф-я для вывода структуры должностей и отделов
        $out = array();

        if($ItemType == 'position') {
            $iconCls         = "PositionCls";
            $PrimaryItemType = "Position";
        } elseif($ItemType == 'group') {
            $iconCls         = "GroupCls";
            $PrimaryItemType = "Group";
        } else {
            MainFatalLog(__FUNCTION__ . '(): $ItemType unknown');
            SystemExit();
        }

        $Params['Active']   = 1;
        $Prms['GroupByTargetId']  = true;
        $Prms2['GroupByRuleId']   = true;
        $ItemsObjArr        = GetPositionsOrGroupsObjArr($ItemType, $Params); // берем список объектов должн/отделов
        $AccessLinksObjArr  = GetAccessLinksObjForAllItems($ItemType, $Prms);
        $RulesObjArr        = GetAccessRulesObjArr($Prms2);

        foreach($ItemsObjArr as $ItemObj) {
            $element               = array();
            $element['iconCls']    = $iconCls;
            $element['id']         = $ItemObj->id;
            $element['ItemName']   = $ItemObj->ItemName;
            $element['ItemType']   = $PrimaryItemType;
            (@$Params['ExpandItemId'] == $ItemObj->id) ? $element['expanded'] = true : $element['expanded'] = false; // после добавления права, раскрываем ветку

            $ChidrenCount = 0;
            if(isset($AccessLinksObjArr[$ItemObj->id])) {
                $ChidrenArr = array();
                foreach($AccessLinksObjArr[$ItemObj->id] as $ALObj) {
                    if(isset($RulesObjArr[ $ALObj->AccessRuleId ])) {
                        $ChidrenCount++;
                        $Child                  = array();
                        // добавляем префикс на случай смешения с id должности/отдела или с тем же праивилом в другой должности/отдела !!!!!!!!!!!!!!!
                        // иначе браузер выдаст:
                        // FF:      too much recursion
                        // Chrome:  Maximum call stack size exceeded
                        $Child['id']            = 'r' . $RulesObjArr[ $ALObj->AccessRuleId ]->id . 'p'.$ItemObj->id; // !!!
                        $Child['iconCls']       = 'RightCls';
                        $Child['BindToItemId']  = $ItemObj->id;          // todo образуется слишком много дублированной инфы в таблице
                        $Child['BindToItemName']= $ItemObj->ItemName;
                        $Child['ItemName']      = $RulesObjArr[ $ALObj->AccessRuleId ]->Description;
                        $Child['ItemType']      = 'Right';
                        $Child['RightDescr']    = $RulesObjArr[ $ALObj->AccessRuleId ]->RuleName;
                        $Child['leaf']          = true;
                        array_push($ChidrenArr, $Child);
                    }
                }
                $element['children'] = $ChidrenArr;
            }
            if($ChidrenCount == 0) {
                $element['leaf']  = true; // сделать должность "неоткрывающейся"
            }
            array_push($out, $element);
        }
        return $out;
    }


    function GetStatusesStructure($ItemType, $Params = array()) {
        // Универсальная ф-я для вывода структуры должностей и отделов
        $out = array();

        if($ItemType == 'position') {
            $iconCls         = "PositionCls";
            $PrimaryItemType = "Position";
        } elseif($ItemType == 'group') {
            $iconCls         = "GroupCls";
            $PrimaryItemType = "Group";
        } else {
            MainFatalLog(__FUNCTION__ . '(): $ItemType unknown');
            SystemExit();
        }

        $Params['Active']   = 1;
        $Prms['GroupByTargetId']  = true;
        $Prms2['GroupByRuleId']   = true;
        $ItemsObjArr        = GetPositionsOrGroupsObjArr($ItemType, $Params); // берем список объектов должн/отделов
        $AccessLinksObjArr  = GetAccessLinksObjForAllItems($ItemType, $Prms);
        $RulesObjArr        = GetAccessRulesObjArr($Prms2);

        foreach($ItemsObjArr as $ItemObj) {
            $element               = array();
            $element['iconCls']    = $iconCls;
            $element['id']         = $ItemObj->id;
            $element['ItemName']   = $ItemObj->ItemName;
            $element['ItemType']   = $PrimaryItemType;
            (@$Params['ExpandItemId'] == $ItemObj->id) ? $element['expanded'] = true : $element['expanded'] = false; // после добавления права, раскрываем ветку

            $ChidrenCount = 0;
            if(isset($AccessLinksObjArr[$ItemObj->id])) {
                $ChidrenArr = array();
                foreach($AccessLinksObjArr[$ItemObj->id] as $ALObj) {
                    if(isset($RulesObjArr[ $ALObj->AccessRuleId ])) {
                        $ChidrenCount++;
                        $Child                  = array();
                        // добавляем префикс на случай смешения с id должности/отдела или с тем же праивилом в другой должности/отдела !!!!!!!!!!!!!!!
                        // иначе браузер выдаст:
                        // FF:      too much recursion
                        // Chrome:  Maximum call stack size exceeded
                        $Child['id']            = 'r' . $RulesObjArr[ $ALObj->AccessRuleId ]->id . 'p'.$ItemObj->id; // !!!
                        $Child['iconCls']       = 'RightCls';
                        $Child['BindToItemId']  = $ItemObj->id;          // todo образуется слишком много дублированной инфы в таблице
                        $Child['BindToItemName']= $ItemObj->ItemName;
                        $Child['ItemName']      = $RulesObjArr[ $ALObj->AccessRuleId ]->Description;
                        $Child['ItemType']      = 'Right';
                        $Child['RightDescr']    = $RulesObjArr[ $ALObj->AccessRuleId ]->RuleName;
                        $Child['leaf']          = true;
                        array_push($ChidrenArr, $Child);
                    }
                }
                $element['children'] = $ChidrenArr;
            }
            if($ChidrenCount == 0) {
                $element['leaf']  = true; // сделать должность "неоткрывающейся"
            }
            array_push($out, $element);
        }
        return $out;
    }


    function GetAccessLinksObjForAllItems($Type = 'position', $Params = array()) {
        $out = array();

        $sql = "SELECT
                    *
                FROM
                    AccessLinks
                WHERE
                    TargetType = '$Type'";
        $res = mysql_query($sql);
        $GLOBALS['FirePHP']->info($sql);
        while($str = mysql_fetch_object($res)) {
            if( isset($Params['GroupByTargetId']) ) {
                if(!isset($out[$str->TargetId])) { $out[$str->TargetId] = array(); }
                array_push($out[$str->TargetId], $str);
            } else {
                array_push($out, $str);
            }
        }
        return $out;
    }

    function AddNewPosition($Name) { // TODO доделать управление PrimaryTarget
        $out = true;
        $msg = '';
        $sql = "INSERT INTO
                    UserPositions (AddedDate, Active, PositionName, Ordering)
                VALUES
                    (NOW(), 1, '$Name', 65500)";
        $GLOBALS['FirePHP']->info($sql);
        $res = mysql_query($sql);
        if(!$res) { $out = false; $msg = mysql_error();}
        return array($out, $msg);
    }
    function AddNewGroup($Name) { // TODO доделать управление PrimaryTarget
        $out = true;
        $msg = '';
        $sql = "INSERT INTO
                        UserGroups (AddedDate, Active, GroupName, Ordering)
                    VALUES
                        (NOW(), 1, '$Name', 65500)";
        $GLOBALS['FirePHP']->info($sql);
        $res = mysql_query($sql);
        if(!$res) { $out = false; $msg = mysql_error();}
        return array($out, $msg);
    }

    function AddNewStatus($Name) {
        $out = true;
        $msg = '';
        $sql = "INSERT INTO
                        UserStatuses (AddedDate, Active, StatusName, Ordering)
                    VALUES
                        (NOW(), 1, '$Name', 65500)";
        $GLOBALS['FirePHP']->info($sql);
        $res = mysql_query($sql);
        if(!$res) { $out = false; $msg = mysql_error();}
        return array($out, $msg);
    }

    function DeletePosition($PositionId) {
        $sql = "DELETE FROM
                    UserPositions
                WHERE
                    id=$PositionId";
        $GLOBALS['FirePHP']->info($sql);
        $res = mysql_query($sql);
        return $res;
    }
    function DeleteGroup($GroupId) {
        $sql = "DELETE FROM
                    UserGroups
                WHERE
                    id=$GroupId";
        $GLOBALS['FirePHP']->info($sql);
        $res = mysql_query($sql);
        return $res;
    }

    function DeleteStatus($StatusId) {
        $sql = "DELETE FROM
                    UserStatuses
                WHERE
                    id=$StatusId";
        $GLOBALS['FirePHP']->info($sql);
        $res = mysql_query($sql);
        return $res;
    }

    function ClearAccessLinks($DeleteType, $TargetType, $TargetId, $BindToItemId=null) {
        if($DeleteType == 'ByTarget') {
            $SqlPatch = "TargetType = LOWER('$TargetType') AND
                         TargetId   = $TargetId";

        } elseif($DeleteType == 'ByRule') {
            // удаляем право доступа только у определенной должности
            $SqlPatch = "AccessRuleId = $TargetId AND
                         TargetType   = LOWER('$TargetType') AND
                         TargetId     = $BindToItemId";
        } else {
            MainFatalLog(__FUNCTION__ . '(): $DeleteType unknown');
            SystemExit();
        }
        $sql = "DELETE FROM
                    AccessLinks
                WHERE
                   $SqlPatch ";
        $GLOBALS['FirePHP']->info($sql);
        $res = mysql_query($sql);
        return $res;
    }

    function GetMainUserPosition($UserId, $Params = array()) {
        $PositionName   = '';
        $PositionId     = null;
        $Count          = 0;
        $HasPrimary     = false;
        $sql = "SELECT
                    UL.id,
                    UL.TargetId AS PositionId,
                    UL.PrimaryTarget,
                    (SELECT UP.PositionName FROM UserPositions AS UP WHERE UP.id = UL.TargetId) AS PositionName
                FROM
                    UserLinks AS UL
                WHERE
                    UserId     = $UserId AND
                    TargetType = 'position'
                ORDER BY
                    UL.PrimaryTarget DESC";
        $res = mysql_query($sql);
        //$GLOBALS['FirePHP']->info($sql); // много вывода
        while($str = mysql_fetch_object($res)) {
            $Count++;
            if($str->PrimaryTarget) {
                $HasPrimary =true;
                $PositionName .= $str->PositionName;
                ($PositionId) ? $p=',' : $p='';
                $PositionId .= $p.$str->PositionId; // TODO не задействовано при нескольких группах, выводим
            }
        }

        if(@$Params['WithCount'] && $Count > 1) { $PositionName .= " ($Count)"; }
        if(@$Params['WithId'] ) {
            return array($PositionName, $PositionId);
        } else {
            return $PositionName;
        }
    }

    function GetUserPositionsArr($UserId, $Params = array()) {
        $out = array();
        $sql = "SELECT
                    UL.id,
                    UL.TargetId AS PositionId,
                    (SELECT UP.PositionName FROM UserPositions AS UP WHERE UP.id = UL.TargetId) AS PositionName
                FROM
                    UserLinks AS UL
                WHERE
                    UserId     = $UserId AND
                    TargetType = 'position'
                ORDER BY
                    UL.PrimaryTarget DESC";
        $res = mysql_query($sql);
        //$GLOBALS['FirePHP']->info($sql); // много вывода
        while($str = mysql_fetch_object($res)) {
            if(@$Params['OnlyIds']) {
                array_push($out, $str->PositionId);
            } else {
                array_push($out, $str);
            }
        }
        return $out;
    }

    function CheckUserInStatus($UserId, $StatusId) {
        $out = false;
        $sql = "SELECT
                    id
                FROM
                    UserStatuses
                WHERE
                    UserId     = '$UserId' AND
                    TargetType = 'status' AND
                    TargetId   = $StatusId";
        $res = mysql_query($sql);
        $GLOBALS['FirePHP']->info($sql);
        $str = @mysql_fetch_object($res);
        if(@$str->id > 0) {
            $out = true;
        }
        return $out;
    }

    function CheckUserInGroup($UserId, $GroupId) {
        $out = false;
        $sql = "SELECT
                    id
                FROM
                    UserLinks
                WHERE
                    UserId     = '$UserId' AND
                    TargetType = 'group' AND
                    TargetId   = $GroupId";
        $res = mysql_query($sql);
        $GLOBALS['FirePHP']->info($sql);
        $str = @mysql_fetch_object($res);
        if(@$str->id > 0) {
            $out = true;
        }
        return $out;
    }
    function CheckUserInPosition($UserId, $PositionId) {
        $out = false;
        $sql = "SELECT
                    id
                FROM
                    UserLinks
                WHERE
                    UserId     = '$UserId' AND
                    TargetType = 'position' AND
                    TargetId   = $PositionId";
        $res = mysql_query($sql);
        $GLOBALS['FirePHP']->info($sql);
        $str = @mysql_fetch_object($res);
        if(@$str->id > 0) {
            $out = true;
        }
        return $out;
    }

    function LinkUserIdToStatusId($UserId, $StatusId, $AddedUserId) { // TODO доделать управление PrimaryTarget
        $out = true;
        $sql = "INSERT INTO
                    UserLinks (AddedDate, AddedUserId, UserId, TargetType, TargetId, PrimaryTarget)
                VALUES
                    (NOW(), '$AddedUserId', $UserId, 'status', $StatusId, 1)";
        $GLOBALS['FirePHP']->info($sql);
        $res = mysql_query($sql);
        if(!$res) { $out = false; }
        return $out;
    }
    function LinkUserIdToGroupId($UserId, $GroupId, $AddedUserId) { // TODO доделать управление PrimaryTarget
        $out = true;
        $sql = "INSERT INTO
                    UserLinks (AddedDate, AddedUserId, UserId, TargetType, TargetId, PrimaryTarget)
                VALUES
                    (NOW(), '$AddedUserId', $UserId, 'group', $GroupId, 1)";
        $GLOBALS['FirePHP']->info($sql);
        $res = mysql_query($sql);
        if(!$res) { $out = false; }
        return $out;
    }
    function LinkUserIdToPositionId($UserId, $PositionId, $AddedUserId) { // TODO доделать управление PrimaryTarget
        $out = true;
        $sql = "INSERT INTO
                        UserLinks (AddedDate, AddedUserId, UserId, TargetType, TargetId, PrimaryTarget)
                    VALUES
                        (NOW(), '$AddedUserId', $UserId, 'position', $PositionId, 1)";
        $GLOBALS['FirePHP']->info($sql);
        $res = mysql_query($sql);
        if(!$res) { $out = false; }
        return $out;
    }

    function UnlinkUserIdFromAllStatuses($UserId) {
        $sql = "DELETE FROM UserLinks WHERE UserId = $UserId AND TargetType = 'status'";
        $GLOBALS['FirePHP']->info($sql);
        $res = mysql_query($sql);
    }
    function UnlinkUserIdFromAllGroups($UserId) {
        $sql = "DELETE FROM UserLinks WHERE UserId = $UserId AND TargetType = 'group'";
        $GLOBALS['FirePHP']->info($sql);
        $res = mysql_query($sql);
    }
    function UnlinkUserIdFromAllPositions($UserId) {
        $sql = "DELETE FROM UserLinks WHERE UserId = $UserId AND TargetType = 'position'";
        $GLOBALS['FirePHP']->info($sql);
        $res = mysql_query($sql);
    }

    function AddOrUpdateUserGroup($UserId, $GroupId) {
        $out = true;
        $UserAlreadyInGroup = CheckUserInGroup($UserId, $GroupId);
        if(!$UserAlreadyInGroup) {
            UnlinkUserIdFromAllGroups($UserId);// Предварительно удаляем все прежние группы // TODO Оптимизировать до дополнительных групп.
            $res = LinkUserIdToGroupId($UserId, $GroupId, 0); // TODO доделать сохранение AddedUserId
            if(!$res) { $out = false; }
        }
        return $out;
    }

    function AddOrUpdateUserStatus($UserId, $StatusId) {
        $out = true;
        if($StatusId > 0) {
            $UserAlreadyInStatus = CheckUserInStatus($UserId, $StatusId);
            if(!$UserAlreadyInStatus) {
                UnlinkUserIdFromAllStatuses($UserId);// Предварительно удаляем все прежние группы // TODO Оптимизировать до дополнительных статусов.
                $res = LinkUserIdToStatusId($UserId, $StatusId, 0); // TODO доделать сохранение AddedUserId
                if(!$res) { $out = false; }
            }
        }
        return $out;
    }

    function AddOrUpdateUserPosition($UserId, $PositionId) {
        $out = true;
        $UserAlreadyInPosition = CheckUserInPosition($UserId, $PositionId);
        if(!$UserAlreadyInPosition) {
            UnlinkUserIdFromAllPositions($UserId);// ---//--- должности
            $res = LinkUserIdToPositionId($UserId, $PositionId, 0);
            if(!$res) { $out = false; }
        }
        return $out;
    }



    function GetUserStatusesArr($UserId, $Params = array()) {
        $SQL['active']  = '';
        $SQL['OrderBy'] = '';
        $SQL['id']      = '';
        $out = array();
        if($UserId > 0) {
            //$SQL['id']= 'id = '.$UserId;
            $query = "SELECT
                        UL.id,
                        UL.TargetId AS StatusId,
                        (SELECT US.StatusName FROM UserStatuses AS US WHERE US.id = UL.TargetId) AS StatusName
                    FROM
                        UserLinks AS UL
                    WHERE
                        UserId     = $UserId AND
                        TargetType = 'status'
                    ORDER BY
                        UL.PrimaryTarget DESC";
        } else {
            $SQL['id']= 'id > 0';
            if(@$Params['Active'] == 1) {
                $SQL['active'] = ' AND Active=1';
            } else {
                $SQL['active'] = ' AND Active=0';
            }

            if(isset($Params['OrderByField'])) {
                $SQL['OrderBy'] = " ORDER BY {$Params['OrderByField']} {$Params['OrderByTo']}";
            } else {
                $SQL['OrderBy'] = " ORDER BY Ordering";
            }
            $query = "SELECT
                        *,
                        id AS StatusId
                    FROM
                        UserStatuses
                    WHERE
                        {$SQL['id']}
                        {$SQL['active']}
                        {$SQL['OrderBy']}";
        }

        $res = mysql_query($query);
        $GLOBALS['FirePHP']->info($query);
        while($str = mysql_fetch_object($res)) {
            array_push($out, $str);
        }
        return $out;
    }


    function GetMainUserStatus($UserId, $Params=array()) {
        $StatusName = '';
        $Count      = 0;
        $HasPrimary = false;
        $sql = "SELECT
                    UL.id,
                    UL.TargetId AS StatusId,
                    UL.PrimaryTarget,
                    (SELECT US.StatusName FROM UserStatuses AS US WHERE US.id = UL.TargetId) AS StatusName
                FROM
                    UserLinks AS UL
                WHERE
                    UserId     = $UserId AND
                    TargetType = 'status'
                ORDER BY
                    UL.PrimaryTarget DESC";
        $res = mysql_query($sql);
        //$GLOBALS['FirePHP']->info($sql);  // много вывода
        while($str = mysql_fetch_object($res)) {
            $Count++;
            if($str->PrimaryTarget) {
                $HasPrimary =true;
                $StatusName .= $str->StatusName;
            }
        }
        //if(!$HasPrimary) {
        //  $GroupName = 'отсутствует';
        //}
        if(@$Params['WithCount'] && $Count > 1) { $StatusName .= " ($Count)"; }
        return $StatusName;
    }
