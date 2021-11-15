<?php

    function FillDefaults_BillAdPrices($TarifsIds) {
        global $CONF;
        $i=0;
        foreach($TarifsIds as $TarifId) {
            $i++;
            $sql = "INSERT INTO BillAdPrices
                        (AddedDate,PriceDate,AddedUserId,Actual,TarifId,PricePerDay)
                    VALUES (
                        NOW(), NOW(), 0, 1, $TarifId, 0)";
            mysql_query($sql);
            $GLOBALS['FirePHP']->info($sql);
        }
        if(!$i) {
            $msg = __FUNCTION__."(): cant fill default prices";
            $GLOBALS['FirePHP']->error($msg);
            CrmCopyErrorLog($msg);
        } else {
            $msg = __FUNCTION__."(): success $i";
            $GLOBALS['FirePHP']->info($msg);
            CrmCopyNoticeLog($msg);
        }
    }

    function asdfasdfasdf() {

    }