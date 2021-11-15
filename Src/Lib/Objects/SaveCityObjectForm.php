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
if(@$_REQUEST['Utka'] == 1) { $_REQUEST['Color'] = 'LightBrown'; } else { $_REQUEST['Color'] = null; }
if(!isset($_REQUEST['Currency']))   { $_REQUEST['Currency'] = 70;}// TODO статика - оч плохо! подумать,переделать

if($_REQUEST['LoadedObjectId'] > 0) {
    // обновление существующего объекта
    // TODO эскейпнуть и все другое
    $_REQUEST['Description'] = mysql_real_escape_string( $_REQUEST['Description'] );
    $_REQUEST['KladrCity']   = mysql_real_escape_string( @$_REQUEST['KladrCity'] );
    $_REQUEST['Street']      = mysql_real_escape_string( @$_REQUEST['Street'] );

    if(@$_REQUEST['Street'] && @$_REQUEST['KladrCity']) {
        $GeoCoords                   = GetGeoCoordsByYandex($_REQUEST); // TODO делает задержку при сохранении, вывести в отдельный сервис?
    } else {
        $GeoCoords = (object)[];
        $GeoCoords->Latitude = '';
    }
    $MoreParams                  = array();
    $MoreParams['PlaceTypeSocr'] = $PlaceTypeSocr;
    $MoreParams['GeoCoords']     = @$GeoCoords;
    $MoreParams['EditSpecial']   = $_REQUEST['EditSpecial'];

    $sql                         = MakeSqlQuery('city', 'update', $_REQUEST, $MoreParams);   // делаем строку sql запроса

    $GLOBALS['FirePHP']->info($sql);
    $res = mysql_query($sql);
    $msg = mysql_error();
    if($res) {
        echo '{"success":true,"message":"Объект № '.$_REQUEST['LoadedObjectId'].' успешно обновлен","Latitude":"'.$GeoCoords->Latitude.'"}';
    } else {
        echo '{"success":false,"message":"'.$msg.'"}';
    }

} else {
    // новый объект

    // TODO ескапировать все оставшиеся переменные
    $_REQUEST['Description'] = mysql_real_escape_string( $_REQUEST['Description'] );
    $_REQUEST['KladrCity']   = mysql_real_escape_string( $_REQUEST['KladrCity'] );
    $_REQUEST['Street']      = mysql_real_escape_string( $_REQUEST['Street'] );
    if(@$_REQUEST['Utka']) { $_REQUEST['Color'] = 'LightBrown'; } else { $_REQUEST['Color'] = null; }
    $GeoCoords      = GetGeoCoordsByYandex($_REQUEST); // TODO делает задержку при сохранении, вывести в отдельный сервис?
    $MoreParams     = array();
    $MoreParams['PlaceTypeSocr'] = $PlaceTypeSocr;
    $MoreParams['GeoCoords']     = $GeoCoords;
    $sql            = MakeSqlQuery('city', 'insert', $_REQUEST, $MoreParams);   // делаем строку sql запроса
    $GLOBALS['FirePHP']->info($sql);
    $res            = mysql_query($sql);
    $SavedObjectId  = mysql_insert_id();
    $msg            = mysql_error();
    if($res) {
        echo '{"success":true,"LoadedObjectId":' . $SavedObjectId . ',"Latitude":"' . $GeoCoords->Latitude . '","message":"Новый объект успешно добавлен"}';
    } else {
        echo '{"success":false,"message":"'.$msg.'"}';
    }
}
