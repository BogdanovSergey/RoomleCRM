<?php
    // Список объектов в главной таблице ///////////
    // TODO Как выбирать записи с пустыми (null) ст Метро? - IFNULL!
    // REPLACE(FORMAT - изменить запятую на точку - sql strreplace
    // REPLACE( REPLACE(FORMAT(o.Price, 3),'.000',''), ',', '.') AS Price

    $Params['RealtyType'] = 'commerce';
    $Params['OnlyUserId'] = @$_REQUEST['OnlyUserId'];
    $SQLParams  = GetSqlParamsForGetObjectsList($Params); // NB! GetSummOfObjects() - Ниже
    $SqlQuery   = MakeGetObjectsListSql($SQLParams);
    $res        = mysql_query($SqlQuery);
    $CycleCount = 0; // контрольный счетчик для проверки соответствия между "select query" объектов и "select count query"
    while($str  = mysql_fetch_object($res)) {
        $CycleCount++;
        //fields: ['AddedDate', 'Rooms', 'City', 'Metro', 'Street', 'Floors', 'Squares', 'Price']
        //$tmp = GetAgentObjById($str->OwnerUserId); // TODO убрать в главный запрос
        //if(isset($tmp->FIO)) {$FIO = $tmp->FIO;}else{$FIO='';}
        $AdTarifArr        = GetObjectAdTarifArr($str->id); // список порталов куда выгружается объект
        $Prms             = array();
        $Prms['Data']     = $str;
        $Prms['CopyData'] = true;
        $Prms['InArray']  = true;
        $ObjData            = ExtendObjectProperties($Prms);

        $element          = $ObjData;
        $element['checkbox']     = 0;//$str->id;//0;
        //$element['id']           = $ObjData->id;
        //$element['ObjectAgeType']= $ObjData->ObjectAgeType;
        //$element['Color']        = $ObjData->Color;
        /*$element['ImagesCount']  = $str->ImagesCount;
        $element['DealTypeName'] = $str->DealTypeName;
        $element['RoomTypeName'] = $str->RoomTypeName;
        $element['PricePeriodName'] = $str->PricePeriodName;
        $element['PriceTypeName']   = $str->PriceTypeName;
        $element['ObjectBrandName']=$str->ObjectBrandName;
        $element['AddedDate']    = $str->AddedDate;
        $element['ArchivedDate'] = $str->ArchivedDate;
        $element['CommerceObjectTypeName']=$str->ObjectTypeName;
        $element['RoomsCount']   = $str->RoomsCount;*/
        //if($str->ObjectType == 3) {$element['RoomsCount'] = $str->RoomsSell .'/' . $element['RoomsCount']; } // к комнатам добавляем комнатность
        //(strlen($str->PlaceTypeSocr)>0) ? $element['City'] = $str->City . ' (' . $str->PlaceTypeSocr . ')' : $element['City'] = $str->City;
        //$element['Metro']        = $str->MetroStation;
        /*$element['Street']       = $str->StreetHouse;
        $element['Floors']       = $str->Floors;
        $element['Squares']      = $str->SquareAll;
        $element['Currency']     = GetCurrencyNameById($str->Currency, true);   // пока не добавляю это в sql, чтоб не нагружать
        $element['Price']        = $element['Currency'].' '.$str->Price;            // знак валюты
        $element['AdPortWinner'] = @$AdTarifArr[1];   //winner - 1 - эти индексы прописаны в тбл. AdPortals
        $element['AdPortCian']   = @$AdTarifArr[2];   //cian   - 2
        $element['AdPortCianPrem']=@$AdTarifArr[5];   //cian premium   - 5
        $element['AdPortAvito']  = @$AdTarifArr[3];   //avito  - 3
        $element['AdPortNavigator']=@$AdTarifArr[4];
        $element['AdPortRbc']    = @$AdTarifArr[6];    // rbc
        $element['TrfAnSiteFree']=@$AdTarifArr[7];    // CorpSite
        //$element['SobPhone']     = $str->SobPhone;
        //$element['SobName']      = $str->SobName;
        $element['Agent']        = $str->FIO;//$FIO;
*/
        array_push($out, $element);
    }

    $response               = (object) array();
    $response->success      = true;
    //$response->message      = "Loaded data";
    $response->data         = $out;
    $Prms['ActiveType']     = $_REQUEST['Active'];
    $Prms['RealtyType']     = $Params['RealtyType']; // тип взят из статической тбл RealtyTypes
    $Prms['OnlyUserId']     = $Params['OnlyUserId'];
    $TotalCount             = GetSummOfObjects($Prms); // все виды объектов, [не]активные
    $response->total        = $TotalCount;

    // проверка правильности вывода селектов (при больших объемах $CycleCount разделяется на порции и != Total)
    if( ( $CycleCount > $TotalCount ) || ($CycleCount < 10 && $TotalCount > 10) ) {
        $msg = __FILE__." Несоответствие в выборке объектов между 'SELECT *' и 'SELECT COUNT(): \$CycleCount=$CycleCount, \$TotalCount = $TotalCount";
        $GLOBALS['FirePHP']->error($msg);
        MainFatalLog($msg);
    }
    // вывод данных
    header("Content-Type: application/json;charset=UTF-8");
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
