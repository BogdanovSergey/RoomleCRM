<?php

    function RemovePricesActuality() {
        $sql = "UPDATE BillAdPrices SET Actual = 0";
        $GLOBALS['FirePHP']->info($sql);
        mysql_query($sql);
    }

    function UpdateAdPrices($Post) {
        global $CURRENT_USER;
        $msg = null;
        $Params['IdAndPrice']   = true;
        $Params['Actual']       = true;
        $IdAndPricesArr         = GetTarifPricesArr($Params);
        $TrfsArr                = array();
        if(!count($IdAndPricesArr)) {
            $Params2['IdAndPrice'] = true;
            $Params2['Last']       = true;
            $IdAndPricesArr = GetTarifPricesArr($Params2);  // если нет актуальных (что не должно быть), берем только последние.
            $msg = " Актуальных цен не было найдено, взяты последние.";
            $GLOBALS['FirePHP']->error($msg);
        }
        RemovePricesActuality();                            // снимаем актуальность

        $i=0;
        foreach($IdAndPricesArr as $key => $value) {        // перебираем  имеющиеся тарифные планы
                                                            // добавляем указанную в форме цену тарифа как актуальную
            $TarifId = $IdAndPricesArr[$key][0];
            if(isset($Post[$key])) {
                if (in_array($TarifId, $TrfsArr)) {
                    $GLOBALS['FirePHP']->warn("TarifId $TarifId exists");
                } else {
                    $i++;
                    $NewTarifPrice = $Post[$key];
                    array_push($TrfsArr, $TarifId);
                    $sql = "INSERT INTO
                                BillAdPrices
                                    (AddedDate,PriceDate,AddedUserId,Actual,TarifId,PricePerDay)
                            VALUES
                                (NOW(),NOW(),{$CURRENT_USER->id},1,
                                $TarifId, $NewTarifPrice )\n";
                    $GLOBALS['FirePHP']->info($sql);
                    $res = mysql_query($sql);
                }
            } else {
                $GLOBALS['FirePHP']->warn("В пришедшей форме нет поля для $key");
            }
        }
        $GLOBALS['FirePHP']->info("Вставлено новых цен: $i");
        return $msg;
    }


    function UpdateBillAdTarifsActivity($Post) {
        $Params['Actual']      = true;
        $Params['OnlyActivity']= true;
        $IdAndPricesArr        = GetTarifPricesArr($Params);
        foreach($IdAndPricesArr as $key => $value) {    // перебираем  имеющиеся тарифные планы
            //echo "$key {$Post[$key]}\n";
            if(@$Post[$key . 'Active'] == '1') {                    // сравниваем "TrfWinnerActive" из базы с пришедшим POST'ом
                $sql = "UPDATE BillAdTarifs SET Active = 1 WHERE TarifShortName = '$key'";
            } else {
                $sql = "UPDATE BillAdTarifs SET Active = 0 WHERE TarifShortName = '$key'";
            }
            $GLOBALS['FirePHP']->info($sql);
            $res = SQLQuery($sql, $GLOBALS['DBConn']['CrmDb']);
        }
    }


    function GetTarifPricesArr($Params = array()) {
        $GLOBALS['FirePHP']->info(__FUNCTION__."(): ".@$Params['Actual'].", ".@$Params['Last']);
        $CachedParams = $Params;
        // взять последние актуальные цены по всем тарифам (тариф выгрузки объявления - цена в сутки)
        $out = array();
        if(@$Params['Actual']) {
            $sql = "
                # берем последние цены с названием тарифом
                SELECT
                        MAX(p.AddedDate) AS flag,
                        t.id AS TarifId,
                        t.ContragentId AS ContragentId,
                        p.id,
                        t.TarifShortName,
                        t.Active AS Active,
                        p.PricePerDay
                FROM
                        BillAdTarifs AS t,
                        BillAdPrices AS p
                WHERE
                        p.TarifId = t.id AND
                        p.Actual = 1
                GROUP BY p.TarifId
                ORDER BY flag";
        }
        if(@$Params['Last']) {
            $sql = "
                # берем последние цены с названием тарифом
                SELECT
                        MAX(p.AddedDate) AS flag,
                        t.id AS TarifId,
                        t.ContragentId AS ContragentId,
                        p.id,
                        p.TarifId,
                        t.TarifShortName,
                        p.PricePerDay
                FROM
                        BillAdTarifs AS t,
                        BillAdPrices AS p
                WHERE
                        p.TarifId = t.id
                GROUP BY p.TarifId
                ORDER BY flag";
        }

	    $out = TarifPricesQuery($sql, $Params);
        if(!CheckPricesExist()) {
            $TarifsIds = GetBillAdTarifsArr();
            FillDefaults_BillAdPrices($TarifsIds); // цен не найдено (это первый запуск?), заполняем значения по-умолчанию

            $msg = "Cant find any prices, filling defaults (first start?)";
            $GLOBALS['FirePHP']->error($msg);
            CrmCopyNoticeLog($msg);

            $out = TarifPricesQuery($sql, $Params);
        }
        $GLOBALS['FirePHP']->info("prices: ".count($out));
        return $out;
    }

    function TarifPricesQuery($sql, $Params) {
        $out = array();
        //$res = mysql_query($sql);
        $res = SQLQuery($sql, $GLOBALS['DBConn']['CrmDb']);
        $GLOBALS['FirePHP']->info($sql);
        $i = 0;
        while($str = mysql_fetch_object($res)) {
            $i++;
            if(@$Params['OnlyActivity']) {
                $out[$str->TarifShortName ] = $str->Active;
            } else {
                if (@$Params['IdAndPrice']) {
                    if (@$Params['ContragentIdKey']) {   // ключ - id контрагента
                        if (isset($out[$str->ContragentId])) { // один ценник уже есть, по этому контрагенту несколько тарифов
                            $out[$str->ContragentId] = array($out[$str->ContragentId]);
                            array_push($out[$str->ContragentId], array($str->TarifId, $str->PricePerDay));
                        } else {
                            $out[$str->ContragentId] = array($str->TarifId, $str->PricePerDay);
                        }
                    } else {
                        $out[$str->TarifShortName] = array($str->TarifId, $str->PricePerDay);
                        $out[$str->TarifShortName . 'Active']   = $str->Active;
                        $out[$str->TarifShortName . 'XmlLinks'] = GetAdXmlUrlsByTarifId($str->TarifId);
                    }
                } else {
                    if (@$Params['ContragentIdKey']) {   // ключ - id контрагента
                        if (isset($out[$str->ContragentId])) { // один ценник уже есть, по этому контрагенту несколько тарифов
                            $out[$str->ContragentId] = array($out[$str->ContragentId]);
                            array_push($out[$str->ContragentId], $str->PricePerDay);
                        } else {
                            $out[$str->ContragentId] = $str->PricePerDay;
                        }
                    } else {
                        $out[$str->TarifShortName] = $str->PricePerDay;
                        $out[$str->TarifShortName . 'Active']   = $str->Active;
                        $out[$str->TarifShortName . 'XmlLinks'] = GetAdXmlUrlsByTarifId($str->TarifId);
                    }
                }
            }
        }
        return $out;
    }

    function GetAdXmlUrlsByTarifId($TarifId) {
        global $CONF;
        $out = array();
        $sql = "SELECT
                  FileName
                FROM
                  AdXmlFiles
                WHERE
                  BillAdTarifId = $TarifId";
        $res = SQLQuery($sql, $GLOBALS['DBConn']['CrmDb']);
        while($str = mysql_fetch_object($res)) {
            array_push($out, $CONF['SysParamsVars']['MainSiteUrl'] . $CONF['SysParamsVars']['XmlFolder'] . $str->FileName);
        }
        return implode(';', $out);
    }

    function CheckPricesExist() {
        $sql = "
            SELECT
                COUNT(id) AS c
            FROM
                BillAdPrices";
        //$res = mysql_query($sql);
        $res = SQLQuery($sql, $GLOBALS['DBConn']['CrmDb']);
        $str = mysql_fetch_object($res);
        if($str->c > 0) {
            $out = true;
        } else {
            $out = false;
        }

        return $out;

    }