<?php

    $Params = array();
    ($_REQUEST['Active'] == 1) ? $Params['Active']   = 1 : $Params['Active']   = 0; // активные или архивные?
    $Params['NoHidden']     = true; // не показывать скрытых
    if( isset($_REQUEST['sort']) ) { // входящий sort выглядит так: [{"property":"Metro","direction":"ASC"}]
        $SortObj = json_decode(@$_REQUEST['sort']); // подготавливаем направление сортрировки //#COLUMNSORTING
        $SortObj = $SortObj[0];
        $Params['OrderByField'] = $SortObj->property;
        $Params['OrderByTo']    = $SortObj->direction;
    } else {
        $Params['OrderByField'] = 'LastName';
        $Params['OrderByTo']    = 'ASC';
    }
    if( CheckMyRule('Users-LimitByOwnGroup') ) {     // разрешено смотреть сотрудников только своего отдела
        $Params['LimitByGroupIdsArr']    = $CURRENT_USER->GroupIdsArr; #USERGROUPARR
    }

    $UsersArr = GetAgentsArr($Params);
    $GroupsArr= array(); // TODO для облегчения цикла можно как-то вставить назв-я групп сюда?
    $GrpParams['WithCount'] = true;
    $GrpParams['WithId']    = true;
    $PosParams['WithCount'] = true;
    $PosParams['WithId']    = true;
    $StsParams['WithCount'] = false;
    foreach($UsersArr as $UserObj) {
        $element                = array();
        $element['id']          = $UserObj->id;
        $element['AddedDate']    = ChangeDateFormat($UserObj->AddedDate, 'EngMonth2RusShort');
        $element['ArchivedDate']= $UserObj->ArchivedDate;
        $element['FirstName']   = $UserObj->FirstName;
        $element['LastName']    = $UserObj->LastName;
        $element['MobilePhone'] = $UserObj->MobilePhone;

        list($element['Position'], $element['PositionId']) = GetMainUserPosition($UserObj->id , $PosParams);
        list($element['Group'],    $element['GroupId'])    = GetMainUserGroup($UserObj->id , $GrpParams);
        $element['Status']   = GetMainUserStatus($UserObj->id , $StsParams);
        //$element['PositionId'] = GetMainUserPosition($UserObj->id , $PosParams);
        //$element['GroupId']    = GetMainUserGroup($UserObj->id , $GrpParams);

        $element['Email']        = $UserObj->Email;
        $element['Birthday']     = ChangeDateFormat($UserObj->Birthday, 'EngMonth2RusShort');
        $element['MobilePhone1'] = $UserObj->MobilePhone1;
        $element['MobilePhone2'] = $UserObj->MobilePhone2;
        $element['CurrentSumm']  = $UserObj->CurrentSumm;
        $element['LastEnter']    = ChangeDateFormat($UserObj->LastEnter, 'EngMonth2RusShort');

        array_push($out, $element);
    }
    $response               = (object) array();
    $response->success      = true;
    $response->data         = $out;
    $response->total        = count($UsersArr);

    // вывод данных
    header("Content-Type: application/json;charset=UTF-8");
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
