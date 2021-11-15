<?php
/**
 * каждый день в 23:30 по каждому объекту в AdPortalObjects:
 * после подсчета всех объектов:
 *      увеличиваем счет пользователя на эту сумму; (если счета нет, создаем)
 */

require(dirname(__FILE__) . '/../../Conf/Config.php');
require(dirname(__FILE__) . '/../../Lib/Billing/BillingFuncs.php');
$GLOBALS['FirePHP']->setEnabled(false); // в сервисе не нужен FirePhp

ExitOnWebAccess(); // сервисы должны запускаться только в CLI

DBConnect();


$BUFF = array();
$BUFF['Report']             = '';
$BUFF['TotalDaySumm']       = 0;       // Итого за день
$BUFF['TarifPrice']         = array(); // цены к тарифам:                   $BUFF['TarifPrice'][tarifId] = 2.00
$BUFF['ObjectTodaySumm']    = array(); // сумма сегоня? затрат ПО ОБЪЕКТУ:     $BUFF['ObjectTodaySumm'][objectId] = 200.00
$BUFF['ObjectSummsByTarif'] = array(); // сумма затрат объекта ПО ТАРИФУ:   $BUFF['ObjectSummsByTarif'][objectId][tarifId] = 25.00
$BUFF['ObjectTotalSumm']    = array(); // сумма сегоня? затрат ПО ОБЪЕКТУ:     $BUFF['ObjectTotalSumm'][objectId] = 200.00

GetAllAdObjectsObj();                    // пролистать, добавить сегодняшние операции
GetObjectTotalSummsFromBillOperations(); // берем затраты всех объектов за все время - $BUFF['ObjectTotalSumm']
UpdateObjectSumm($BUFF['ObjectTotalSumm']); // обновить сумму затрат в самом объекте (AdCost)
UpdateUserSumms();

// обновляем

function GetAllAdObjectsObj() {
    // каждый день в 23:30 по каждому объекту в AdPortalObjects
    global $BUFF;
    $out = array();
    $sql = "SELECT
                APO.*,
                (SELECT OwnerUserId FROM Objects WHERE id = APO.ObjectId) AS UserId
            FROM
                AdPortalObjects AS APO
            WHERE
                APO.TarifId IS NOT NULL";
    $res = mysql_query($sql);
    if(!$res) { CoreLog(__FUNCTION__."() Error in: \n\"{$sql}\"\n".mysql_error()); }
    $i=0;
    while($str = mysql_fetch_object($res)) {        // по каждому объекту что сегодня выгрузился
        $i++;
        //if($i>=3){break;}
        $ActualPrice        = GetActualPriceByTarifId($str->TarifId); // TODO можно хакэшить
        $str->ActualPrice   = $ActualPrice;
        $AccountId          = CheckOrCreateUserBillAccount($str->UserId);
        $Exist              = CheckAdBillOperationExistToday($AccountId, $str->ObjectId, $str->TarifId);
        if($Exist == false) {                                           // сначала проверяем нет ли сегодня этой же операции
            if($str->UserId && $str->ObjectId && $str->TarifId) {       
                array_push($out, $str);

                $BUFF['TotalDaySumm'] += $ActualPrice;                      // увеличиваем сумму Итого сегодняшней выгрузки

                if(!isset($BUFF['ObjectTodaySumm'][$str->ObjectId])) {
                    $BUFF['ObjectTodaySumm'][$str->ObjectId] = 0; }
                $BUFF['ObjectTodaySumm'][$str->ObjectId] += $ActualPrice;                   // увеличиваем сумму рекламы объекта

                if(!isset($BUFF['ObjectSummsByTarif'][$str->ObjectId][$str->TarifId])) {
                    $BUFF['ObjectSummsByTarif'][$str->ObjectId][$str->TarifId] = 0; }
                $BUFF['ObjectSummsByTarif'][$str->ObjectId][$str->TarifId] += $ActualPrice; // увеличиваем сумму с разбивкой по тарифу

                SaveTodayAdBillOperationForObject($AccountId, $ActualPrice, $str->ObjectId, $str->TarifId); // сохраняем операцию в биллинг

            } else {
                $BUFF['Report'] .= "Cant save BillOperation, UserId:{$str->UserId}, ObjectId:{$str->ObjectId}, TarifId: {$str->TarifId}\n";
            }
        } else if($Exist == true) {
            $BUFF['Report'] .= "BillOperation exist for AccountId:$AccountId, ObjectId:$str->ObjectId, TarifId:$str->TarifId\n";
        } else {
            $BUFF['Report'] .= "Cant make CheckAdBillOperationExistToday for AccountId:$AccountId, ObjectId:$str->ObjectId, TarifId:$str->TarifId\n";


        }
    }

    //print_r( $BUFF );
    return $out;
}

function GetObjectTotalSummsFromBillOperations() {
    // считаем сумму всех операций по объектам
    // может быть затратно!
    global $BUFF;
    $sql = "SELECT
                TargetId AS ObjectId,
                SUM(OperationSumm)AS ObjectSumm
            FROM 
                `BillOperations` 
            WHERE 
                OperationType = 'expense' AND
                TargetType = 'object'
            GROUP BY TargetId";
    $res = mysql_query($sql);
    while($str = mysql_fetch_object($res)) {
        $BUFF['ObjectTotalSumm'][$str->ObjectId] = $str->ObjectSumm;
    }
}

function GetActualPriceByTarifId($TarifId) {
    global $BUFF;
    $out = null;
    if(isset($BUFF['TarifPrice'][$TarifId])) {
        $out = $BUFF['TarifPrice'][$TarifId]; // типа "кэширование"
    } else {
        $sql = "SELECT
                    PricePerDay
                FROM
                    BillAdPrices
                WHERE
                    TarifId = $TarifId AND
                    Actual = 1";
        $res = mysql_query($sql);
        if(!$res) { CoreLog(__FUNCTION__."() Error in: \n\"{$sql}\"\n".mysql_error()); }
        $str = mysql_fetch_object($res);
        $out = $str->PricePerDay; // todo проверять на правильность
        $BUFF['TarifPrice'][$TarifId] = $str->PricePerDay;
    }
    return $out;
}

function UpdateObjectSumm($ObjectTotalSumm) {
    global $BUFF;
    foreach($ObjectTotalSumm as $key=>$val) {
        $sql = "UPDATE Objects SET AdCosts = {$val} WHERE id = {$key}";
        //echo "$sql\n";
        $BUFF['Report'] .= "ObjectId : $key, AdCost; ++$val\n";
        $res = mysql_query($sql);
        if(!$res) { CoreLog(__FUNCTION__."() Error in: \n\"{$sql}\"\n".mysql_error()); }
    }
}

function CheckAdBillOperationExistToday($AccountId, $ObjectId, $TarifId) {
    // сначала проверяем нет ли сегодня этой же операции
    $out = null;
    if($AccountId && $ObjectId && $TarifId) {
        $sql = "SELECT id FROM BillOperations WHERE
                        DATE(OperationDate) = CURRENT_DATE() AND
                        OperationType       = 'expense' AND
                        AccountId           = $AccountId AND
                        ExpenseTypeId       = 1 AND
                        TargetType          = 'object' AND
                        TargetId            = $ObjectId AND
                        TarifId             = $TarifId AND
                        OperationAuthorId   = 0";
        $res = mysql_query($sql);
        if(!$res) { CoreLog(__FUNCTION__."() Error in: \n\"{$sql}\"\n".mysql_error()); }
        $str = mysql_fetch_object($res);
        if(@$str->id > 0) {
            $out = true;
        } else {
            $out = false;
        }
    }
    return $out;
}


function SaveTodayAdBillOperationForObject($AccountId, $Summ, $ObjectId, $TarifId) {
    global $BUFF;
    $sql = "INSERT INTO BillOperations
                   (OperationDate, OperationType,OperationSumm,AccountId,ExpenseTypeId,TargetType,TargetId,TarifId,OperationAuthorId)
            VALUES (NOW(), 'expense', $Summ, $AccountId, 1, 'object', $ObjectId, $TarifId, 0)";
    //echo "$sql\n";
    $res = mysql_query($sql);
    if(!$res) { CoreLog(__FUNCTION__."() Error in: \n\"{$sql}\"\n".mysql_error()); }
}



function CreateUserBillAccount($UserId) {
    global $BUFF;
    $BUFF['Report'] .= "Creating BillAccount for $UserId\n";
    $sql = "INSERT INTO BillAccounts
                   (CreatedDate, LastUpdateDate,AccountType,CurrentSumm,ContragentId)
            VALUES (NOW(),NOW(), 'user', 0, $UserId )";
    $res = mysql_query($sql);
    if(!$res) { CoreLog(__FUNCTION__."() Error in: \n\"{$sql}\"\n".mysql_error()); }
    $AccountId  = mysql_insert_id();
    return $AccountId;
}

