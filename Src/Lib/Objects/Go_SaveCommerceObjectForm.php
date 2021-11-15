<?php

// берем сокращение типа нас.пункта
$PlaceTypeSocr = GetPlaceTypeSocr( @$_REQUEST['PlaceType'] );

if(@$_REQUEST['CityType'] == 'Moscow') {
    // объект в москве. По ключу (CityType) автоматически подготавливаем поля
    $_REQUEST['KladrRegion']     =   'Москва';
    //$_REQUEST['KladrCity']       =   'Москва';
    $_REQUEST['KladrRaion']      =   '';
    //$_REQUEST['PlaceType']       =   'город';
    //$PlaceTypeSocr               =   'г';
} else {
    // за москвой
}
if(@$_REQUEST['CommerceRoomsSquares'] == '20+15+40+50') {$_REQUEST['CommerceRoomsSquares']='';}// todo bad
if(@$_REQUEST['Utka'] == 1) { $_REQUEST['Color'] = 'LightBrown'; } else { $_REQUEST['Color'] = null; }
if(!isset($_REQUEST['Currency'])) { $_REQUEST['Currency'] = 70;}// TODO статика - оч плохо! подумать,переделать

if($_REQUEST['LoadedObjectId'] > 0) {
    // обновление существующего объекта
    // TODO эскейпнуть и все другое
    $_REQUEST['Description'] = mysql_real_escape_string( @$_REQUEST['Description'] );
    $_REQUEST['KladrCity']   = mysql_real_escape_string( @$_REQUEST['KladrCity'] );
    $_REQUEST['Street']      = mysql_real_escape_string( @$_REQUEST['Street'] );

    if(@$_REQUEST['Street'] && @$_REQUEST['KladrCity']) {
        $GeoCoords = GetGeoCoordsByYandex($_REQUEST); // TODO делает задержку при сохранении, вывести в отдельный сервис?
    } else {
        $GeoCoords = (object)[];
        $GeoCoords->Latitude = '';
    }
    $MoreParams                  = array();
    $MoreParams['PlaceTypeSocr'] = $PlaceTypeSocr;
    $MoreParams['GeoCoords']     = @$GeoCoords;
    $MoreParams['EditSpecial']   = $_REQUEST['EditSpecial'];

    $sql                         = MakeSqlQuery('commerce', 'update', $_REQUEST, $MoreParams);   // делаем строку sql запроса
    $GLOBALS['FirePHP']->info($sql);
    $res = mysql_query($sql);
    $msg = mysql_error();

    if(!$_REQUEST['EditSpecial']) {
        $sql2                    = MakeSqlQuery('commerceMore', 'update', $_REQUEST, $MoreParams);   // делаем строку sql запроса
        $GLOBALS['FirePHP']->info($sql2);
        $res2 = mysql_query($sql2);
        $msg2 = mysql_error();
    } else {
        $res2 = true;
    }


    if($res && $res2) {
        $EventParams['UserId']  = $CURRENT_USER->id;
        $EventParams['Message'] = 'внес изменения в объект';
        AddObjectEvent($_REQUEST['LoadedObjectId'], $EventParams);

        echo '{"success":true,"message":"Объект № '.$_REQUEST['LoadedObjectId'].' успешно обновлен","Latitude":"'.$GeoCoords->Latitude.'"}';
    } else {
        echo '{"success":false,"message":"'.$msg.' '.$msg2.'"}';
    }

} else {
    // новый объект

    // TODO ескапироватб все оставшиеся переменные
    $_REQUEST['Description'] = mysql_real_escape_string( $_REQUEST['Description'] );
    $_REQUEST['KladrCity']   = mysql_real_escape_string( $_REQUEST['KladrCity'] );
    $_REQUEST['Street']      = mysql_real_escape_string( $_REQUEST['Street'] );
    //if($_REQUEST['CommerceCeilingHeight'] == '	<1,90') {}

    $GeoCoords = GetGeoCoordsByYandex($_REQUEST); // TODO делает задержку при сохранении, вывести в отдельный сервис?
    $sql = "        INSERT INTO Objects
                        (
                        AddedDate,    RealtyType,
                        Price,        Currency,   OwnerUserId,
                        PriceTypeId,  DealType,
                        ObjectType,
                        Region,       Raion,
                        City,         AltCityName, PlaceType, PlaceTypeSocr,
                        Street,       HouseNumber,
                        SquareAll,    RoomsCount, Floor, Floors,
                        MetroStation1Id, MetroWayMinutes, MetroWayType,
                        Description,
                        CommercePricePeriodId,
                        CommercePriceTypeId,
                        ObjectBrandName,
                        CommerceObjectTypeId,
                        CommerceRoomTypeId,
                        Latitude, Longitude,
                        YandexAddress,
                        OwnerPhoneId
                        )
                    VALUES (
                        NOW(),  'commerce',
                        '{$_REQUEST['Price']}',           '{$_REQUEST['Currency']}', '{$_REQUEST['OwnerUserId']}',
                        '{$_REQUEST['CommercePriceTypeId']}', '{$_REQUEST['DealType']}',
                        '{$_REQUEST['CommerceObjectTypeId']}',
                        '{$_REQUEST['KladrRegion']}',     '{$_REQUEST['KladrRaion']}',
                        '{$_REQUEST['KladrCity']}',       NULLIF('{$_REQUEST['AltCityName']}',''), '{$_REQUEST['PlaceType']}', '{$PlaceTypeSocr}',
                        '{$_REQUEST['Street']}',          '{$_REQUEST['HouseNumber']}',
                        '{$_REQUEST['SquareAll']}',       '{$_REQUEST['RoomsCount']}',      '{$_REQUEST['Floor']}', '{$_REQUEST['Floors']}',
                        '{$_REQUEST['MetroStation1Id']}', '{$_REQUEST['MetroWayMinutes']}',
                        '{$_REQUEST['MetroWayType']}',
                        '{$_REQUEST['Description']}',
                        '{$_REQUEST['CommercePricePeriodId']}',
                        '{$_REQUEST['CommercePriceTypeId']}',
                        '{$_REQUEST['ObjectBrandName']}',
                        '{$_REQUEST['CommerceObjectTypeId']}',
                        '{$_REQUEST['CommerceRoomTypeId']}',
                        NULLIF('{$GeoCoords->Latitude}', ''),
                        NULLIF('{$GeoCoords->Longitude}',''),
                        NULLIF('{$GeoCoords->YandexAddress}',''),
                        '{$_REQUEST['OwnerPhoneId']}'
                    )
                ";
    $GLOBALS['FirePHP']->info($sql);
    $res = mysql_query($sql);
    $msg = mysql_error();
    $SavedObjectId = mysql_insert_id();
    // вставить дополнительные данные
    $sql2 = "INSERT INTO ObjectsData (
                        AddedDate, ObjectId,
                        CommerceSquareMin,
                        CommerceBuildingTypeId,
                        CommerceEnterTypeId,
                        CommercePhoneLinesCount,
                        CommercePhoneLinesAddId,
                        CommerceFurnitureId,
                        CommerceBuildingsCount,
                        CommerceCeilingHeight,
                        CommerceBuildingYear,
                        CommerceBuildingClass,
                        CommerceCommunPayId,
                        CommerceExplutPayId,
                        RoomMapId,
                        CommerceRoomsSquares,
                        CommerceConditionId,
                        CommerceBuildingStatusId,
                        CommerceFireId,
                        CommerceVentilationId,
                        CommerceHeatingId,
                        CommercePower,
                        CommerceFloorLoad,
                        CommerceParkingId,
                        CommerceParkingPlaces,
                        CommerceLifts,
                        LiftBrand,
                        CommerceAgentPay,
                        CommerceClientPay,
                        TelecomProvider,
                        OptionInternet,
                        OptionToilet,
                        OptionCafe,
                        OptionBankomat,
                        OptionFitness,
                        OptionShop
                    )
             VALUES (
                NOW(), $SavedObjectId,
                NULLIF('{$_REQUEST['CommerceSquareMin']}',     ''),
                NULLIF('{$_REQUEST['CommerceBuildingTypeId']}',     ''),
                NULLIF('{$_REQUEST['CommerceEnterTypeId']}',     ''),
                NULLIF('{$_REQUEST['CommercePhoneLinesCount']}',     ''),
                NULLIF('{$_REQUEST['CommercePhoneLinesAddId']}',     ''),
                NULLIF('{$_REQUEST['CommerceFurnitureId']}',     ''),
                NULLIF('{$_REQUEST['CommerceBuildingsCount']}',     ''),
                NULLIF('{$_REQUEST['CommerceCeilingHeight']}',     ''),
                NULLIF('{$_REQUEST['CommerceBuildingYear']}',     ''),
                NULLIF('{$_REQUEST['CommerceBuildingClass']}',     ''),
                NULLIF('{$_REQUEST['CommerceCommunPayId']}',     ''),
                NULLIF('{$_REQUEST['CommerceExplutPayId']}',     ''),
                NULLIF('{$_REQUEST['RoomMapId']}',     ''),
                NULLIF('{$_REQUEST['CommerceRoomsSquares']}',     ''),
                NULLIF('{$_REQUEST['CommerceConditionId']}',     ''),
                NULLIF('{$_REQUEST['CommerceBuildingStatusId']}',     ''),
                NULLIF('{$_REQUEST['CommerceFireId']}',     ''),
                NULLIF('{$_REQUEST['CommerceVentilationId']}',     ''),
                NULLIF('{$_REQUEST['CommerceHeatingId']}',     ''),
                NULLIF('{$_REQUEST['CommercePower']}',     ''),
                NULLIF('{$_REQUEST['CommerceFloorLoad']}',     ''),
                NULLIF('{$_REQUEST['CommerceParkingId']}',     ''),
                NULLIF('{$_REQUEST['CommerceParkingPlaces']}',     ''),
                NULLIF('{$_REQUEST['CommerceLifts']}',     ''),
                NULLIF('{$_REQUEST['LiftBrand']}',     ''),
                NULLIF('{$_REQUEST['CommerceAgentPay']}',     ''),
                NULLIF('{$_REQUEST['CommerceClientPay']}',     ''),
                NULLIF('{$_REQUEST['TelecomProvider']}',     ''),
                '".@$_REQUEST['OptionInternet']."',
                '".@$_REQUEST['OptionToilet']."',
                '".@$_REQUEST['OptionCafe']."',
                '".@$_REQUEST['OptionBankomat']."',
                '".@$_REQUEST['OptionFitness']."',
                '".@$_REQUEST['OptionShop']."'
                )";
    $GLOBALS['FirePHP']->info($sql2);
    $res2 = mysql_query($sql2);
    $msg2 = mysql_error();
    if($res && $res2) {
        $EventParams['UserId']  = $CURRENT_USER->id;
        $EventParams['Message'] = 'создал объект';
        AddObjectEvent($SavedObjectId, $EventParams);

        echo '{"success":true,"LoadedObjectId":' . $SavedObjectId . ',"Latitude":"' . $GeoCoords->Latitude . '","message":"Новый объект успешно добавлен"}';
    } else {
        echo '{"success":false,"message":"'.$msg.' '.$msg2.'"}';
    }
}
