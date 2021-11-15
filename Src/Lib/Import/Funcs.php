<?php

    function Import_CheckImportId($id) {
        $out = false;
        $sql = "SELECT
                    id
                FROM
                    Objects
                WHERE
                    ImportId = '$id'
                ";
        $res = mysql_query($sql);
        $str = @mysql_fetch_object($res);
        if(isset($str->id)) {$out = true;}
        return $out;
    }

    function Import_GetObjectParamIdByColumnValue($Column, $Value) {
        // берем id из тбл: ObjectParams по значению портала
        // если значений несколько, опираемся на ReversePriority
        $ResCount = 0;
        $OutId    = null;
        $sql = "SELECT
                    id, ReversePriority
                FROM
                    ObjectParams
                WHERE
                    {$Column} = '{$Value}'
                ";
        $res = mysql_query($sql);
        while($str = mysql_fetch_object($res)) {
            $ResCount++;
            if($str->ReversePriority > 0) { $OutId = $str->id; break; }
            $OutId = $str->id;
        }
        if($ResCount > 1) {
            $ParamsArr['OnlyMsg'] = true;
            MainNoticeLog("В колонке ObjectParams:'{$Column}' нет значения '{$Value}' или не указан ReversePriority (count: {$ResCount}, ".@$str->id.")", $ParamsArr);
        }
        return $OutId;

    }

    function Import_GetObjectTypeArrIdByColumnValue($ColumnsArr, $Value) {
        $SqlColumns='';
        foreach($ColumnsArr as $val) {
            if(!isset($p)) { $p=''; } else { $p=' OR '; }
            $SqlColumns .= $p . "(ot.{$val} = '{$Value}') ";
        }
        //$out = false;
        $sql = "SELECT
                    ot.id, (SELECT rt.TypeName FROM RealtyTypes AS rt WHERE rt.id = ot.RealtyType) AS RealtyTypeName
                FROM
                    ObjectTypes AS ot
                WHERE
                    ($SqlColumns)
                ";
        $res = mysql_query($sql);
        $str = @mysql_fetch_object($res);
        return array($str->id, $str->RealtyTypeName);
    }

    function Import_DownloadFilesArr($FilesArr) {
        global $CONF;
        $FilesPathsArr = array();
        foreach($FilesArr as $furl) {
            $Name       = pathinfo($furl, PATHINFO_FILENAME);//'dwnl_' . rand(1,10000);
            $Ext        = pathinfo($furl, PATHINFO_EXTENSION);
            $FullFileName = $CONF['TempDir']. $Name.".".$Ext;
            $res        = file_put_contents($FullFileName, fopen($furl, 'r')); // Downloading remote file
            $LenStr     = GetHumanFilesize( filesize($FullFileName) );
            if($res) {
                $msg = "Downloaded $furl, saved to: $FullFileName ($LenStr)\n";
                SimpleLog($CONF['Log']['CrmImportLog'], $msg);
                array_push($FilesPathsArr, $FullFileName);                      // новый путь к скачанному файлу
            } else {
                $msg = "ERROR $furl, cant save to: $FullFileName ($LenStr)\n";
                SimpleLog($CONF['Log']['CrmImportLog'], $msg);
            }
            //print_r($FilesPathsArr);
        }
        return $FilesPathsArr;
    }
    function Import_AttachUploadFilesToObjectId($FilesArr, $ObjectId, $Params = array() ) {
        global $CONF;
        if(count($FilesArr) > 0 && $ObjectId > 0) {
            $Params             = array();
            $Params['local']    = true;
            $Params['prefix']   = 'file';
            foreach($FilesArr as $file) {
                $ImgInfoArr     = Graphics_GetImageInfo($file);
                $FILES          = array();
                $FILES[$Params['prefix']]['name']      = $ImgInfoArr['filename'];
                $FILES[$Params['prefix']]['type']      = $ImgInfoArr['type'];
                $FILES[$Params['prefix']]['tmp_name']  = $file;
                $FILES[$Params['prefix']]['error']     = 0;
                $FILES[$Params['prefix']]['size']      = $ImgInfoArr['filesize'];
                $FILES[$Params['prefix']]['extension'] = $ImgInfoArr['extension'];
                $Params['filesize'] = $ImgInfoArr['filesize'];
                FileUploader($FILES, $ObjectId, $Params);     // ресайзим в 2 файла, сохраняем в БД
                if( !unlink($file) ) {SimpleLog($CONF['Log']['CrmImportLog'], "cant unlink $file\n");}
            }
        } else {
            $msg = __FUNCTION__."() params error\n";
            MainNoticeLog($msg);
            SimpleLog($CONF['Log']['CrmImportLog'], $msg);
        }
    }

    function Import_YandexObjectFieldsToArr($SimpleXmlObj) {
        global $CONF;
        $ObjectsData = array();
        if( $SimpleXmlObj->type == 'продажа' ) {

                                            // Категория объекта («комната», «квартира», «дом», «участок», «flat», «room», «house», «cottage»,
                                            // «townhouse», «таунхаус», «часть дома», «house with lot», «дом с участком», «дача», «lot», «земельный участок»). Сейчас принимаются объявления только о продаже и аренде жилой недвижимости: квартир, комнат, домов и участков.
            list($ObjectsData['ObjectType'], $RealtyTypeName) = Import_GetObjectTypeArrIdByColumnValue(array('YandexMark', 'YandexMark2'), $SimpleXmlObj->category);

            $ObjectsData['DealType']        = Import_GetObjectParamIdByColumnValue('YandexMark', $SimpleXmlObj->type);
            // проверить текст на альтернативу, изменить тип сделки
            // TODO Добавить в другие места сохранения объекта
            $ObjectsData['DealType'] = ChangeDealTypeIfAlternativaFoundInText($ObjectsData['DealType'], (string)$SimpleXmlObj->description);
            $ObjectsData['ImportId']        = $SimpleXmlObj->attributes()['internal-id'];
            $ObjectsData['KladrRegion']     = (string)$SimpleXmlObj->location->{"locality-name"};
            $ObjectsData['KladrCity']       = (string)$SimpleXmlObj->location->{"locality-name"};
            $ObjectsData['PlaceType']       = 'город';
            $ObjectsData['PlaceTypeSocr']   = 'г';
            $AddrArr = explode(', ', $SimpleXmlObj->location->address);
            $ObjectsData['Street']          = $AddrArr[0];
            $ObjectsData['HouseNumber']     = preg_replace( '/д\s/', '', $AddrArr[1] );
            $ObjectsData['MetroStation1Id'] = GetMetroStationIdByName( $SimpleXmlObj->location->metro->name );
            if( @$SimpleXmlObj->location->metro->{"time-on-transport"} ) {
                $ObjectsData['MetroWayType']    = Import_GetObjectParamIdByColumnValue('YandexMark', 'time-on-transport');
                $ObjectsData['MetroWayMinutes'] = (string)$SimpleXmlObj->location->metro->{"time-on-transport"};
            } elseif( @$SimpleXmlObj->location->metro->{"time-on-foot"} ) {
                $ObjectsData['MetroWayType']    = Import_GetObjectParamIdByColumnValue('YandexMark', 'time-on-foot');
                $ObjectsData['MetroWayMinutes'] = (string)$SimpleXmlObj->location->metro->{"time-on-foot"};
            }
            $ObjectsData['Price']               = (string)$SimpleXmlObj->price->value;      // Цена (сумма указывается без пробелов).
            $ObjectsData['Currency']            = Import_GetObjectParamIdByColumnValue('YandexMark', (string)$SimpleXmlObj->price->currency); //Валюта, в которой указана цена. «RUR» или «RUB» — российский рубль; «EUR» — евро; «USD» — американский доллар;
            $ObjectsData['ObjectCondition']     = Import_GetObjectParamIdByColumnValue('YandexMark', (string)$SimpleXmlObj->quality); // 	Состояние объекта (рекомендуемые значения — «хорошее», «отличное», «нормальное», «плохое»).
            $ObjectsData['Floors']              = (string)$SimpleXmlObj->{"floors-total"};  // Общее количество этажей в доме (обязательное поле для новостроек).
            $ObjectsData['Floor']               = (string)$SimpleXmlObj->{"floor"};
            $ObjectsData['RoomsCount']          = (string)$SimpleXmlObj->{"rooms"};
            $ObjectsData['RoomsSell']           = (string)$SimpleXmlObj->{"rooms-offered"}; // Для продажи и аренды комнат: количество комнат, участвующих в сделке.
            $ObjectsData['SquareAll']           = (string)$SimpleXmlObj->{"area"}->value;   // Общая площадь.
            $ObjectsData['SquareLiving']        = (string)$SimpleXmlObj->{"living-space"}->value;
            $ObjectsData['SquareKitchen']       = (string)$SimpleXmlObj->{"kitchen-space"}->value;
            $ObjectsData['Description']         = (string)$SimpleXmlObj->description;

            $ObjectsData['OwnerUserId']         = GetUserIdByPhone( $SimpleXmlObj->{"sales-agent"}->phone ); //<phone>79168038020</phone>
            if(!$ObjectsData['OwnerUserId']) {
                // пользователь не обнаружен, ставим "системного пользователя"
                $ObjectsData['OwnerUserId'] = $CONF['SysParams']['SystemUserId'];
            }
            $ObjectsData['ObjectAgeType']       = 56; // TODO статика ужасна, да, да, да = ВТОРИЧКА
            $ObjectsData['Color']               = 'LightYellow';    // для пометки об импорте

            // Добавление основных данных объекта в базу
            $MoreParams     = array();
            $MoreParams['PlaceTypeSocr'] = $ObjectsData['PlaceTypeSocr'];
            $MoreParams['GeoCoords']     = GetGeoCoordsByYandex($ObjectsData);
            $sql                         = MakeSqlQuery($RealtyTypeName, 'insert', $ObjectsData, $MoreParams);   // делаем строку sql запроса
            $res            = mysql_query($sql);
            $SavedObjectId  = mysql_insert_id();
            $msg            = mysql_error();
            if($res) {
                // данные объекта были успешно добавлены
                // Скачка и добавление фотографий к объекту
                $ImgArr = array();
                foreach($SimpleXmlObj->image as $ImgUrl) {
                    array_push($ImgArr, (string)$ImgUrl);
                }
                $DownloadedFilesArr = Import_DownloadFilesArr($ImgArr);
                Import_AttachUploadFilesToObjectId($DownloadedFilesArr, $SavedObjectId);
                $msg = __FUNCTION__."(): SUCCESS: new object id:$SavedObjectId, images: ".count($DownloadedFilesArr)."\n";
                SimpleLog($CONF['Log']['CrmImportLog'], $msg);
                echo $msg."\n";
            } else {
                SimpleLog($CONF['Log']['CrmImportLog'], __FUNCTION__."(): ERROR: cant import object by: $sql\n$msg\n");
            }

        } else if( $SimpleXmlObj->type == 'аренда' ) {

        } else {
            $msg = __FUNCTION__."(): обнаружен объект с неизвестным типом сделки: " . (string)$SimpleXmlObj->type . ", cat:" . (string)$SimpleXmlObj->category . "\n";
            SimpleLog($CONF['Log']['CrmImportLog'], $msg);
        }

    }

