<?php

$Params = array();
($_REQUEST['Active'] == 1) ? $Params['Active']   = 1 : $Params['Active']   = 0; // активные или архивные?
if( isset($_REQUEST['sort']) ) { // входящий sort выглядит так: [{"property":"Metro","direction":"ASC"}]
    $SortObj = json_decode(@$_REQUEST['sort']); // подготавливаем направление сортрировки //#COLUMNSORTING
    $SortObj = $SortObj[0];
    $Params['OrderByField'] = $SortObj->property; // св-ва Json запроса
    $Params['OrderByTo']    = $SortObj->direction;
} else {
    $Params['OrderByField'] = 'LastName';
    $Params['OrderByTo']    = 'ASC';
}
$Params['OnlyUserId'] = $CURRENT_USER->id; // Смотрим только своих...
//if( CheckMyRule('Users-=-=-==--LimitByOwnGroup') ) {     // разрешено смотреть сотрудников только своего отдела
//    $Params['LimitByGroupIdsArr']    = $CURRENT_USER->GroupIdsArr; #USERGROUPARR
//}

$ClientsArr = GetClientsArr($Params);
$GroupsArr= array(); // TODO для облегчения цикла можно как-то вставить назв-я групп сюда?

$p['ClientType']['person']  = 'физ. лицо';
$p['ClientType']['company'] = 'Организация';
foreach($ClientsArr as $ClientObj) {
    $element                = array();
    $element['id']          = $ClientObj->id;
    $element['AddedDate']   = $ClientObj->AddedDate;
    $element['ArchivedDate']= $ClientObj->ArchivedDate;
    $element['FirstName']   = $ClientObj->FirstName;
    $element['LastName']    = $ClientObj->LastName;
    $element['MobilePhone'] = $ClientObj->MobilePhone;
    $element['ObjectLocation'] = $ClientObj->ObjectLocation;
    $element['ClientType'] = $p['ClientType'][$ClientObj->ClientType];
    //echo $p['ClientType'][$ClientObj->ClientType]."\n";


    //list($element['Position'], $element['PositionId']) = GetMainUserPosition($ClientObj->id , $PosParams);
    //list($element['Group'],    $element['GroupId'])    = GetMainUserGroup($ClientObj->id , $GrpParams);
//$element['Status']   = GetMainUserStatus($ClientObj->id , $StsParams);
    //$element['PositionId'] = GetMainUserPosition($ClientObj->id , $PosParams);
    //$element['GroupId']    = GetMainUserGroup($ClientObj->id , $GrpParams);

    //$element['Email']        = $ClientObj->Email;
    //$element['MobilePhone1'] = $ClientObj->MobilePhone1;
    //$element['MobilePhone2'] = $ClientObj->MobilePhone2;


    array_push($out, $element);
}
$response               = (object) array();
$response->success      = true;
$response->data         = $out;
$response->total        = count($ClientsArr);

// вывод данных
header("Content-Type: application/json;charset=UTF-8");
echo json_encode($response, JSON_UNESCAPED_UNICODE);
