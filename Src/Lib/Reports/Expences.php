<?php


// ������� ������ �� �������

// �� ������� AccountId � ExpenceTypeId=1 (�������)


// [AccountId][UserId/Arr][ObjectId][TarifId] = [����������][����������][������]
// [AccountId][UserId][������][TarifId] = [���������� ���� ��������][���������� ���� ��������][������ ���� ��������]


    function FillReportExpences() {
/*
CREATE TABLE IF NOT EXISTS `ReportExpences` (
  `id` int(10) unsigned NOT NULL,
  `AddedDate` datetime NOT NULL,

  `AccountId`       int(10) unsigned NOT NULL,
  `TargetTypeId`    int(1) unsigned NOT NULL COMMENT 'FK � TargetTypes (��� ������������), 1 - ������ ����.',
  `TargetId`        int(11) unsigned NOT NULL COMMENT 'id ������������',
  `MoreTableName`   enum('BillAdTarifs') COLLATE utf8_bin DEFAULT NULL COMMENT '���.������� - �������������� �������� (����: �����)',
  `MoreTableId`     int(11) unsigned DEFAULT NULL,
  `ExpenseTypeId` int(10) unsigned NOT NULL COMMENT 'FK � BillExpenseTypes (�� ��� �����), 1 - ����. ��������',
  `ExpenceSumm` decimal(10,2) NOT NULL DEFAULT '0.00',
  `IncomeSumm` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `BalanceSumm` decimal(10,2) NOT NULL DEFAULT '0.00'
)

$DATA[ $AccountId ][ $UserIdOrArr ][ $ObjectId ][ $TarifId ] = array($ExpenseSumm, $IncomeSumm, 0);
 */

        /* ���������:
            ��������� �� BillOperations, �������������, �������� � ReportExpences

        */

        $DATA = array();

        // ����� ��� �������� �� ������� ���� ����������� ��������
        $sql = "SELECT DISTINCT(AccountId) FROM BillOperations ORDER BY AccountId ASC";
        $res = mysql_query($sql);
        while($str = mysql_fetch_object($res)) {
            $AccountId      = $str->AccountId;
            $UserIdOrArr    = GetUserByAccountId($AccountId);


            // ����� ������ ����� ������ �� ��������, ������ �� �������, ������ �� ������� ��������
            $sql1 = "SELECT
                        SUM(OperationSumm) AS ExpenseSumm,
                        TargetId AS ObjectId,
                        TarifId
                    FROM
                        BillOperations
                    WHERE
                        AccountId     = {$AccountId} AND
                        TargetType    = 'object' AND   # ������ �� �������� ������������
                        ExpenseTypeId = 1 AND       # FK � BillExpenseTypes (�� ��� �����), 1 - ����. ��������
                        OperationType = 'expense'   # ������ �����
                    GROUP BY TargetId, TarifId";
            $res1 = mysql_query($sql1);
            while($str1 = mysql_fetch_object($res1)) {
                $ObjectId = $str1->ObjectId;
                $TarifId = $str1->TarifId;
                $ExpenseSumm = $str1->ExpenseSumm;

                $DATA[ $AccountId ][ $UserIdOrArr ][ $ObjectId ][ $TarifId ] = array($ExpenseSumm, 0, 0);
            }

            // ������ ������
            $sql1 = "SELECT
                        SUM(OperationSumm) AS IncomeSumm,
                        TargetId AS ObjectId,
                        TarifId
                    FROM
                        BillOperations
                    WHERE
                        AccountId     = {$AccountId} AND
                        TargetType    = 'object' AND   # ������ �� �������� ������������
                        ExpenseTypeId = 1 AND       # FK � BillExpenseTypes (�� ��� �����), 1 - ����. ��������
                        OperationType = 'income'   # ������ ������
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








