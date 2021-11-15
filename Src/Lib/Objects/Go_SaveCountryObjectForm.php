<?php

// берем сокращение типа нас.пункта
$PlaceTypeSocr = GetPlaceTypeSocr( @$_REQUEST['PlaceType'] );

if(@$_REQUEST['CityType'] == 'Moscow') {
    // объект в москве. По ключу (CityType) автоматически подготавливаем поля
        //$_REQUEST['KladrRegion']     =   'Москва';
        //$_REQUEST['KladrCity']       =   'Москва';
    //$_REQUEST['KladrRaion']      =   '';
        //$_REQUEST['PlaceType']       =   'город';
        //$PlaceTypeSocr               =   'г';
} else {
    // за москвой
}
if($_REQUEST['LoadedObjectId'] > 0) {
    // обновление существующего объекта
    // TODO эскейпнуть и все другое
    $_REQUEST['Description'] = mysql_real_escape_string( @$_REQUEST['Description'] );
    $_REQUEST['KladrCity']   = mysql_real_escape_string( @$_REQUEST['KladrCity'] );
    $_REQUEST['Street']      = mysql_real_escape_string( @$_REQUEST['Street'] );
    if(!isset($_REQUEST['Currency'])) { $_REQUEST['Currency'] = 70;}// TODO статика - оч плохо! подумать,переделать

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
    $sql                         = MakeSqlQuery('country', 'update', $_REQUEST, $MoreParams);   // делаем строку sql запроса

    $GLOBALS['FirePHP']->info($sql);
    $res = mysql_query($sql);
    $msg = mysql_error();
    if($res) {
        $EventParams['UserId']  = $CURRENT_USER->id;
        $EventParams['Message'] = 'внес изменения в объект';
        AddObjectEvent($_REQUEST['LoadedObjectId'], $EventParams);

        echo '{"success":true,"message":"Объект № '.$_REQUEST['LoadedObjectId'].' успешно обновлен","Latitude":"'.$GeoCoords->Latitude.'"}';
    } else {
        echo '{"success":false,"message":"'.$msg.'"}';
    }

} else {
    // новый объект

    // TODO ескапироватб все оставшиеся переменные
    $_REQUEST['Description'] = mysql_real_escape_string( $_REQUEST['Description'] );
    $_REQUEST['KladrCity']   = mysql_real_escape_string( $_REQUEST['KladrCity'] );
    $_REQUEST['Street']      = mysql_real_escape_string( $_REQUEST['Street'] );
    if(!isset($_REQUEST['Currency'])) { $_REQUEST['Currency'] = 70;}// TODO статика - оч плохо! подумать,переделать
    $GeoCoords = GetGeoCoordsByYandex($_REQUEST); // TODO делает задержку при сохранении, вывести в отдельный сервис?
    $sql = "        INSERT INTO Objects
                        (
                        AddedDate,    RealtyType,
                        Price,        Currency,   OwnerUserId,
                        PriceTypeId,  DealType,
                        ObjectType,
                        Region,       Raion,
                        City,         AltCityName, PlaceType, PlaceTypeSocr,
                        HighwayId,    Distance,
                        Street,       HouseNumber,
                        LandSquare,   SquareLiving, Floors,
                        LandTypeId,   CountryElectro,
                        CountryWater, CountryGas,
                        CountrySewer, CountryHeat,
                        CountryPmg,   CountrySecure,

                        CountryToilet,  CountryBath,
                        CountryGarage,  CountryPool,
                        CountryMaterial,CountryPhone,
                        CountryWallsTypeId,

                        SobPhone,     SobName,
                        Description,
                        Latitude, Longitude, YandexAddress
                        )
                    VALUES (
                        NOW(),  'country',
                        '{$_REQUEST['Price']}',       '{$_REQUEST['Currency']}', '{$_REQUEST['OwnerUserId']}',
                        '{$_REQUEST['PriceTypeId']}', '{$_REQUEST['DealType']}',
                        '{$_REQUEST['ObjectType']}',
                        '{$_REQUEST['KladrRegion']}', '{$_REQUEST['KladrRaion']}',
                        '{$_REQUEST['KladrCity']}',   NULLIF('{$_REQUEST['AltCityName']}',''), '{$_REQUEST['PlaceType']}', '{$PlaceTypeSocr}',
                        '{$_REQUEST['HighwayId']}',   '{$_REQUEST['Distance']}',
                        '{$_REQUEST['Street']}',      '{$_REQUEST['HouseNumber']}',
                        '{$_REQUEST['LandSquare']}',  '{$_REQUEST['SquareLiving']}', '{$_REQUEST['Floors']}',
                        NULLIF('{$_REQUEST['LandTypeId']}',''),     NULLIF('{$_REQUEST['CountryElectro']}',''),
                        NULLIF('{$_REQUEST['CountryWater']}',''),   NULLIF('{$_REQUEST['CountryGas']}',''),
                        NULLIF('{$_REQUEST['CountrySewer']}',''),   NULLIF('{$_REQUEST['CountryHeat']}',''),
                        NULLIF('{$_REQUEST['CountryPmg']}',  ''),   NULLIF('{$_REQUEST['CountrySecure']}',''),
                        NULLIF('{$_REQUEST['CountryToilet']}',''),  NULLIF('{$_REQUEST['CountryBath']}',''),
                        NULLIF('{$_REQUEST['CountryGarage']}',''),  NULLIF('{$_REQUEST['CountryPool']}',''),
                        NULLIF('{$_REQUEST['CountryMaterial']}',''),NULLIF('{$_REQUEST['CountryPhone']}',''),
                        NULLIF('{$_REQUEST['CountryWallsTypeId']}', ''),

                        NULLIF('".@$_REQUEST['SobPhone']."',''),     NULLIF('".@$_REQUEST['SobName']."',''),
                        '{$_REQUEST['Description']}', NULLIF('{$GeoCoords->Latitude}',''), NULLIF('{$GeoCoords->Longitude}',''), NULLIF('{$GeoCoords->YandexAddress}','')
                    )
                ";
    $GLOBALS['FirePHP']->info($sql);
    $res = mysql_query($sql);
    $SavedObjectId = mysql_insert_id();
    $msg = mysql_error();
    if($res) {
        echo '{"success":true,"LoadedObjectId":' . $SavedObjectId . ',"Latitude":' . $GeoCoords->Latitude . ',"message":"Новый объект успешно добавлен"}';
    } else {
        echo '{"success":false,"message":"'.$msg.'"}';
    }
}
