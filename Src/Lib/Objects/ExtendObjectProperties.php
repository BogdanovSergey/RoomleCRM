<?php

function ExtendObjectProperties($Params) {
    // ф-я дополняет нужные поля объекта
    // TODO сквозная???
    global $TMP;
    $IncomeObj = Array();
    $IncomeObj = $Params['Data'];
    if($Params['CopyData']) {
        $out     = $Params['Data'];     // копируем предидущие значения
    } else {
        $out     = (object) array();    // возвращаем новый объект без прошлых значений, только новые
    }

    // даты
    $out->AddedDate    = ChangeDateFormat($IncomeObj->AddedDate, 'EngMonth2RusShort');
//echo $IncomeObj->AddedDate . $out->AddedDate; exit;
    // сокращение типа города в колонке "Город"
    if(strlen($IncomeObj->PlaceTypeSocr) > 0) {
        $PlaceTypeSocr = ' (' . $IncomeObj->PlaceTypeSocr . ')';
    } else {
        $PlaceTypeSocr = '';
    }

    // тип жилья
    if( isset($TMP[ $IncomeObj->ObjectAgeType ]) ) {
        $out->ObjectAgeType = $TMP[ $IncomeObj->ObjectAgeType ] ; // а-ля кэш
    } else {
        $TMP[ $IncomeObj->ObjectAgeType ] = GetObjectParamByIdAndColumn($IncomeObj->ObjectAgeType, 'ParamValue');
        $out->ObjectAgeType = $TMP[ $IncomeObj->ObjectAgeType ] ;
    }


    // город
    ( isset($IncomeObj->AltCityName) ) ? $AltCityName = '/'.$IncomeObj->AltCityName : $AltCityName = null;    // показываем альтернативный нас. пункт
    if(strlen($IncomeObj->PlaceTypeSocr) > 0) {
        if( isset($AltCityName) ) {
            $out->City = $IncomeObj->City . $AltCityName;
        } else {
            $out->City = $IncomeObj->City . $PlaceTypeSocr;
        }
    } else {
        $out->City = $IncomeObj->City . $AltCityName;
    }

    // неск станций метро в скобках
    $MetroStatCount = 0;
    if($IncomeObj->MetroStation1Id > 0) { $MetroStatCount++; }
    if($IncomeObj->Metro2StationId > 0) { $MetroStatCount++; }
    if($IncomeObj->Metro3StationId > 0) { $MetroStatCount++; }
    if($IncomeObj->Metro4StationId > 0) { $MetroStatCount++; }
    if($MetroStatCount>1) { $out->Metro = @$IncomeObj->Metro . " ($MetroStatCount)";} // ->Metro - может быть пустым при изменении типа объекта!

    // цена, валюта
    $out->Currency  = GetCurrencyNameById($IncomeObj->Currency, true);
    $out->Price     = $out->Currency.' '.$IncomeObj->Price;

    // рекламные затраты
    $out->AdCosts   = '₽ '.$IncomeObj->AdCosts;

    // к комнатам добавляем комнатность
    if($IncomeObj->ObjectType == 3) { $out->RoomsCount = $IncomeObj->RoomsSell .'/' . $IncomeObj->RoomsCount; }

    // галки к порталам
    $AdTarifArr             = GetObjectAdTarifArr($IncomeObj->id); // список тарифов куда выгружается объект
    $out->TrfWinner         = @$AdTarifArr[1];   //winner - 1 - эти индексы прописаны в тбл. BillAdTarifs
    $out->TrfCian           = @$AdTarifArr[2];   //cian   - 2
    $out->TrfCianPremium    = @$AdTarifArr[3];   //cian premium   - 5
    $out->TrfAvito          = @$AdTarifArr[5];   //avito  - 3
    $out->TrfNavigatorFree  = @$AdTarifArr[7];
    $out->TrfRbcFree        = @$AdTarifArr[9];    // rbc
    $out->TrfAfy            = @$AdTarifArr[6];    // rbc
    $out->TrfAnSiteFree     = @$AdTarifArr[8];    // CorpSite
    $out->TrfYandex         = @$AdTarifArr[10];    // 10 = yandex
    //$out->SobPhone        = $IncomeObj->SobPhone;
    //$out->SobName         = $str->SobName;
    $out->Agent             = $IncomeObj->FIO;//$FIO;




    if(@$Params['InArray']) {
        return (array) $out;
    } else {
        return $out;
    }


}