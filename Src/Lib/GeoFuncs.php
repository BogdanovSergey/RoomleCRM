<?php

    function DrawGeoWinTemplate($ObjectId) {
        global $CONF;
        $Obj = GetObjectById($ObjectId);
        $Template = file_get_contents($CONF['SystemPath'] . $CONF['CrmSubDir'] . '/Lib/Html/Templates/GeoWin.html');
        $patterns     = array('/\[Latitude\]/', '/\[Longitude\]/', '/\[YandexAddress\]/');
        $replacements = array($Obj->Latitude, $Obj->Longitude, $Obj->YandexAddress);
        $Template     = preg_replace($patterns, $replacements, $Template);
        return $Template;
    }

    function GetGeoCoordsByYandex($Params) {
        $AddressStr = $Params['KladrRegion'] . ', '. $Params['PlaceType'] . ' ' .
                      $Params['KladrCity'] . ', ' . $Params['Street'] . ' ' . $Params['HouseNumber'];
        $out        = (object)[];
        $ParamsArr  = array();
        $out->YandexAddress = '';
        $out->Latitude      = '';
        $out->Longitude     = '';
        $url        = "http://geocode-maps.yandex.ru/1.x/?geocode=".$AddressStr."&results=1"; //&key=".$cfg['map']['yandex_key']
        $fh         = @fopen($url,'r');
        if($fh) {
            $content    = fread($fh, 32768);
            fclose($fh);

            $xml = simplexml_load_string($content);
            if($xml) {
                if(isset($xml->GeoObjectCollection->featureMember->GeoObject->Point->pos)) {
                    $ll  = explode(" ", $xml->GeoObjectCollection->featureMember->GeoObject->Point->pos);
                    $out->YandexAddress = $xml->GeoObjectCollection->featureMember->GeoObject->metaDataProperty->GeocoderMetaData->text;
                    $out->Latitude      = $ll[1];
                    $out->Longitude     = $ll[0];
                }
            } else {
                $msg                    = __FUNCTION__."() error making simplexml_load_string()";
                $ParamsArr['OnlyMsg']   = true;
                CrmCopyNoticeLog($msg, $ParamsArr);
            }
        }
        return $out;
    }
