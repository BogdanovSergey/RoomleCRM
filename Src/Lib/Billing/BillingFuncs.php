<?php


    function UpdateUserSumms() {
        // TODO: ЗАТРАТНО? проверить
        $sql = "SELECT
                    BA.id AS AccountId,
                    BA.ContragentId AS UserId,
					(SELECT
                     	SUM(BO.OperationSumm)
                     FROM
                     	BillOperations AS BO
                     WHERE
                     	BO.OperationType = 'expense' AND
                     	BO.AccountId = BA.id) AS UserSumm
                FROM
                    BillAccounts AS BA
                WHERE
                    BA.AccountType ='user' AND
                    BA.ContragentId > 0
                ORDER BY UserId";
        $res = mysql_query($sql);
        while($str = mysql_fetch_object($res)) {
            if($str->AccountId > 0 && $str->UserSumm > 0 ) {
                UpdateUserBillAccount($str->AccountId, $str->UserSumm);
            }
            if($str->UserId > 0 && $str->UserSumm > 0 ) {
                UpdateUserCurrentSumm($str->UserId, $str->UserSumm);
            }
        }

    }

    function UpdateUserBillAccount($AccountId, $Summ) {
        if($AccountId > 0 && $Summ>0 ) {
            $sql = "UPDATE BillAccounts SET CurrentSumm = -$Summ WHERE id=$AccountId";
            mysql_query($sql);
        } else {
            CoreLog( __FUNCTION__."() Error: AccountId:$AccountId, Summ:$Summ\n" );
        }
    }

    function UpdateUserCurrentSumm($UserId, $Summ) {
        if($UserId > 0 && $Summ>0 ) {
            $sql = "UPDATE Users SET CurrentSumm = -$Summ WHERE id=$UserId";
            mysql_query($sql);
        } else {
            CoreLog( __FUNCTION__."() Error: UserId:$UserId, Summ:$Summ\n" );
        }
    }

    function GetPriceByTarifId($TarifId) {
        $out = null;
        $sql = "SELECT
                    PricePerDay
                FROM
                    BillAdPrices
                WHERE
                    TarifId = $TarifId AND
                    Actual  = 1";
        $res = mysql_query($sql);
        $GLOBALS['FirePHP']->info($sql);
        $str = @mysql_fetch_object($res);
        if($str->PricePerDay >= 0) {
            $out = $str->PricePerDay;
        } else {
            MainFatalLog("Cant get actual price by tarifId: $TarifId");
        }
        return $out;
    }


    function CheckOrCreateUserBillAccount($UserId) {
        // TODO у пользователя когда-то возможно будет счетов больше одного
        $AccountId = null;
        if($UserId > 0) {
            $sql = "SELECT
                        id
                    FROM
                        BillAccounts
                    WHERE
                        AccountType  = 'user' AND
                        ContragentId = $UserId";
            $res = mysql_query($sql);
            if(!$res) { CoreLog(__FUNCTION__."() Error in: \n\"{$sql}\"\n".mysql_error()); }
            $str = mysql_fetch_object($res);
            $AccountId = @$str->id;
            if($AccountId < 1) {
                $AccountId = CreateUserBillAccount($UserId);
            }
        }
        return $AccountId;
    }

    function GetUserByAccountId($AccountId) {
        // вернуть один или несколько id по id аккаунта
        $User = null;
        $sql = "SELECT
                    ContragentId
                FROM
                    BillAccounts
                WHERE
                    AccountType = 'user' AND
                    id          = $AccountId";
        $res = mysql_query($sql);
        while($str = mysql_fetch_object($res)) {
            if(!isset($User)) {
                $User = $str->ContragentId;
            } else {
                // на аккаунте несколько пользователей, делаем массив
                if(is_int($User)) {
                    $User = array($User, $str->ContragentId);
                } else {        // уже массив
                    array_push($User, $str->ContragentId);
                }
            }
        }
        return $User;
    }


