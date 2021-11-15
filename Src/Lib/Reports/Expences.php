<?php


// подсчет затрат на рекламу

// по каждому AccountId с ExpenceTypeId=1 (реклама)


// [AccountId][UserId/Arr][ObjectId][TarifId] = [СуммЗатрат][СуммВыплат][баланс]
// [AccountId][UserId][вцелом][TarifId] = [СуммЗатрат всех объектов][СуммВыплат всех объектов][баланс всех объектов]


    function FillReportExpences() {
/*
CREATE TABLE IF NOT EXISTS `ReportExpences` (
  `id` int(10) unsigned NOT NULL,
  `AddedDate` datetime NOT NULL,

  `AccountId`       int(10) unsigned NOT NULL,
  `TargetTypeId`    int(1) unsigned NOT NULL COMMENT 'FK к TargetTypes (тип вычисляемого), 1 - объект недв.',
  `TargetId`        int(11) unsigned NOT NULL COMMENT 'id вычисляемого',
  `MoreTableName`   enum('BillAdTarifs') COLLATE utf8_bin DEFAULT NULL COMMENT 'доп.таблица - дополнительный параметр (напр: Тариф)',
  `MoreTableId`     int(11) unsigned DEFAULT NULL,
  `ExpenseTypeId` int(10) unsigned NOT NULL COMMENT 'FK к BillExpenseTypes (на что трата), 1 - рекл. выгрузка',
  `ExpenceSumm` decimal(10,2) NOT NULL DEFAULT '0.00',
  `IncomeSumm` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `BalanceSumm` decimal(10,2) NOT NULL DEFAULT '0.00'
)

$DATA[ $AccountId ][ $UserIdOrArr ][ $ObjectId ][ $TarifId ] = array($ExpenseSumm, $IncomeSumm, 0);
 */

        /* наполнить:
            пробежать по BillOperations, сгруппировать, закинуть в ReportExpences

        */

        $DATA = array();

        // Берем все Аккаунты по которым были фактические операции
        $sql = "SELECT DISTINCT(AccountId) FROM BillOperations ORDER BY AccountId ASC";
        $res = mysql_query($sql);
        while($str = mysql_fetch_object($res)) {
            $AccountId      = $str->AccountId;
            $UserIdOrArr    = GetUserByAccountId($AccountId);


            // берем только сумму затрат по объектам, только на рекламу, только по данному аккаунту
            $sql1 = "SELECT
                        SUM(OperationSumm) AS ExpenseSumm,
                        TargetId AS ObjectId,
                        TarifId
                    FROM
                        BillOperations
                    WHERE
                        AccountId     = {$AccountId} AND
                        TargetType    = 'object' AND   # только по объектам недвижимости
                        ExpenseTypeId = 1 AND       # FK к BillExpenseTypes (на что трата), 1 - рекл. выгрузка
                        OperationType = 'expense'   # только траты
                    GROUP BY TargetId, TarifId";
            $res1 = mysql_query($sql1);
            while($str1 = mysql_fetch_object($res1)) {
                $ObjectId = $str1->ObjectId;
                $TarifId = $str1->TarifId;
                $ExpenseSumm = $str1->ExpenseSumm;

                $DATA[ $AccountId ][ $UserIdOrArr ][ $ObjectId ][ $TarifId ] = array($ExpenseSumm, 0, 0);
            }

            // только приход
            $sql1 = "SELECT
                        SUM(OperationSumm) AS IncomeSumm,
                        TargetId AS ObjectId,
                        TarifId
                    FROM
                        BillOperations
                    WHERE
                        AccountId     = {$AccountId} AND
                        TargetType    = 'object' AND   # только по объектам недвижимости
                        ExpenseTypeId = 1 AND       # FK к BillExpenseTypes (на что трата), 1 - рекл. выгрузка
                        OperationType = 'income'   # только приход
                    GROUP BY TargetId, TarifId";
            $res1 = mysql_query($sql1);
            while($str1 = mysql_fetch_object($res1)) {
                $ObjectId = $str1->ObjectId;
                $TarifId = $str1->TarifId;
                $IncomeSumm = $str1->IncomeSumm;

                list($ExpenseSumm, $tmp1, $tmp2) = $DATA[ $AccountId ][ $UserIdOrArr ][ $ObjectId ][ $TarifId ];
                $DATA[ $AccountId ][ $UserIdOrArr ][ $ObjectId ][ $TarifId ] = array($ExpenseSumm, $IncomeSumm, 0);
            }

            
        }




    }








