<?php

function BuildXmlForMiel($XmlTag, $Document, $MainSqlQuery, $SysParams = array()) {
    global $CONF;
    $tmp = mysql_query($MainSqlQuery);
    $Result = null;
    while($Obj = mysql_fetch_object($tmp)) {

        $Element = $XmlTag->createElement( $SysParams['ElementTag'] );

        if(@$CONF['MielDepart_ID'] < 1) { echo "MielDepart_ID не заполнен"; break; }

        //Depart_ID - ID отдела - Заказчику предоставляет ОКПО ДИТ перед формированием фида
        $Status = $XmlTag->createElement('Depart_ID', $CONF['MielDepart_ID'] );
        $Element->appendChild($Status);

        $id = $XmlTag->createElement('ID', $Obj->id);
        $Element->appendChild($id);


        // Имя клиента - <ClientFirstName>Ания</ClientFirstName>
        //$AgentIOArr = preg_split("/\s/", $Obj->FirstName);
        //if(strlen($AgentIOArr[0]) < 1) {$AgentIOArr[0] = 'Галина';}
        $AgentIOArr[0] = 'Галина';
        $usr = $XmlTag->createElement('ClientFirstName', $AgentIOArr[0]);
        $Element->appendChild($usr);

        //Отчество клиента - <ClientMiddleName>Равильевна</ClientMiddleName>
        //if(@strlen($AgentIOArr[1]) < 1) {$AgentIOArr[1] = 'Васильевна';}
        $AgentIOArr[1] = 'Васильевна';
        $usr = $XmlTag->createElement('ClientMiddleName', $AgentIOArr[1]);
        $Element->appendChild($usr);

        // Фамилия клиента - <ClientLastName>Иванова</ClientLastName>
        //if(strlen($Obj->LastName) < 1) {$Obj->LastName = 'Сударикова';}
        $Obj->LastName = 'Сударикова';
        $usr = $XmlTag->createElement('ClientLastName', $Obj->LastName);
        $Element->appendChild($usr);

        // Телефон клиента - <ClientPhone>74955375051</ClientPhone>
        $AgentPhonesArr = array($Obj->MobilePhone, $Obj->MobilePhone1, $Obj->MobilePhone2);
        //$AgentPhonesArr[$Obj->OwnerPhoneId]
        //$usr = $XmlTag->createElement('ClientPhone', $CONF['SysParams']['NavigatorXmlCompanyPhone'] );
        $usr = $XmlTag->createElement('ClientPhone', '89853568735' );
        //
        $Element->appendChild($usr);


        $usr = $XmlTag->createElement('AgentFirstName', $AgentIOArr[0]);
        $Element->appendChild($usr);
        $usr = $XmlTag->createElement('AgentMiddleName', $AgentIOArr[1]);
        $Element->appendChild($usr);
        $usr = $XmlTag->createElement('AgentLastName', $Obj->LastName);
        $Element->appendChild($usr);

        // Дата и время последнего изменения информации об объекте во внутренней базе заказчика - <Changed>2015-11-13 23:59:59</Changed>
        $Changed = $XmlTag->createElement('Changed', $Obj->AddedDate);
        $Element->appendChild($Changed);

        // Наименование станции метро Москвы, значение из справочника - <SUBWAY>Новокосино</SUBWAY>
        if($Obj->MetroStation1Id > 0) {
            $metro = $XmlTag->createElement('SUBWAY', GetMetroStationNameById($Obj->MetroStation1Id));
            $Element->appendChild($metro);

            // 1-Пешком, 0- транспортом: <isOnFoot>1</isOnFoot>
            $param = $XmlTag->createElement('isOnFoot', GetObjectParamByIdAndColumn($Obj->MetroWayType, 'MielMark'));
            $Element->appendChild($param);

            // Расстояние от метро - <JourneyTime>22</JourneyTime>
            if($Obj->MetroWayMinutes) {
                $param = $XmlTag->createElement('JourneyTime', $Obj->MetroWayMinutes);
                $Element->appendChild($param);
            }

        }

        // Магистраль- для Москвы и МО, значение из справочника - < Highway>Горьковское</ Highway >
        $DirectionRoad = GetHighwayNameById($Obj->HighwayId);
        if($DirectionRoad) {
            $param = $XmlTag->createElement('Highway', $DirectionRoad);
            $Element->appendChild($param);
        }


        // Лифт: да, нет <Elevator>нет</Elevator>
        if($Obj->Lift) {
            $param = GetObjectParamByIdAndColumn($Obj->Lift, 'MielMark');
            $lift = $XmlTag->createElement('Elevator', $param );
            $Element->appendChild($lift );
        }

        // № этажа - <FloorNumber>2</FloorNumber>
        if(@$Obj->Floor > 0) {
            $floor = $XmlTag->createElement('FloorNumber', $Obj->Floor);
            $Element->appendChild($floor);
        }

        // Этажность <FloorCount>5</FloorCount>
        if(@$Obj->Floors > 0) {
            $floors = $XmlTag->createElement('FloorCount', $Obj->Floors);
            $Element->appendChild($floors);
        }

        // Площадь общая <SQRAll>30.2</SQRAll>
        if($Obj->SquareAll) {
            $param = $XmlTag->createElement('SQRAll', $Obj->SquareAll);
            $Element->appendChild($param);
        }

        // Площадь жилая <SQRLiving>16.8</SQRLiving>
        if($Obj->SquareLiving) {
            $param = $XmlTag->createElement('SQRLiving', $Obj->SquareLiving);
            $Element->appendChild($param);
        }

        // Площадь кухни <SQRKitchen>6</SQRKitchen>
        if($Obj->SquareKitchen) {
            $param = $XmlTag->createElement('SQRKitchen', $Obj->SquareKitchen);
            $Element->appendChild($param);
        }

        //Тип операции, значение из справочника <Operation_Type>Альтернатива</Operation_Type>
        $param = $XmlTag->createElement('Operation_Type', GetObjectParamByIdAndColumn($Obj->DealType, 'MielMark'));
        $Element->appendChild($param);

        /*
         Адрес объекта (пример общий)
        <Address name="Московская обл, Железнодорожный г, Новая ул, 33">
        <Country>Россия</Country>
        <Region type="обл">Московская</Region>
        <City type="г">Железнодорожный</City>
        <Street type="ул">Новая</Street>
        <House>33</House>
        </Address>
         */
        if($Obj->Region == $Obj->City) {$City = ', ';} else {$City = ', '.$Obj->City.', ';}
        if(strlen($Obj->Street))      { $Street = $Obj->Street.', '.$Obj->HouseNumber; }

        $location = $XmlTag->createElement('Address');
        $location->setAttribute('name', "{$Obj->Region}{$City}{$Street}");



        // <Country>Россия</Country>
        $param = $XmlTag->createElement('Country', 'Россия');
        $location->appendChild($param);

        // <Region type="обл">Московская</Region> ----------------------

        $ar         = array('область', 'обл');
        $Obl        = 'обл';                                // TODO автоматизировать сокращение через КЛАДР
        $TypeAttr   = null;
        $Tag        = 'Region';
        $Value      = $Obj->Region;
        foreach($ar as $a) {
            if (strpos($Value, $a)) {
                $Value   = preg_replace("/\s$a/u", '', $Value); // TODO надо убрать пробел в конце
                $TypeAttr= $Obl;//$a;
                break;
            }
        }
        if($Value != 'Москва') {
            // тег Region – это для области
            $param = $XmlTag->createElement($Tag, $Value);
            if ($TypeAttr) { $param->setAttribute('type', $TypeAttr); }
            $location->appendChild($param);
        }


        //<City type="г">Железнодорожный</City>-----------------------------
        $ar       = array('город', 'гор');
        $TypeAttr = null;
        if($Obj->PlaceTypeSocr == 'г') {
            $Tag        = 'City';
        } else {
            $Tag        = 'Town';
        }
        $Value    = $Obj->City;
        if($Value)         { $param = $XmlTag->createElement($Tag, $Value);
            if ($Obj->PlaceTypeSocr) { $param->setAttribute('type', $Obj->PlaceTypeSocr); }
            $location->appendChild($param);
        }

        // <Street type="ул">Новая</Street> ---------------------------------
        /*$ar         = array('улица', 'ул');
        $TypeAttr   = null;
        $Value      = $Obj->Street;
        $Tag        = 'Street';
        foreach($ar as $a) {
            if (strpos($Value, $a)) {
                $Value   = preg_replace("/\s?$a\.?/", '', $Value);
                $TypeAttr= $a;
                break;
            }
        }
        if($Value) {         $param = $XmlTag->createElement($Tag, $Value);
            if ($TypeAttr) { $param->setAttribute('type', $TypeAttr); }
            $location->appendChild($param);
        }*/
        /////////////
        if(!isset($SocrArr)) {
            $prms['RegexpCompatible'] = true;
            $SocrArr = GetSocrArr($prms); // для облегчения цикла
        }
        list($StreetName, $KladrSocr) = SplitStreetTextOnKladrSocrAndName($Obj->Street, $SocrArr);

        $param = $XmlTag->createElement('Street', $StreetName);
        if($KladrSocr) {
            $param->setAttribute('type', $KladrSocr);
        }
        $location->appendChild($param);


        // <House>33</House>
        if($Obj->HouseNumber) {
            $HouseNoSpace = preg_replace("/\s/u",'',$Obj->HouseNumber);
            $h = $XmlTag->createElement('House', $HouseNoSpace);
            $location->appendChild($h);
        }

        $Element->appendChild($location);

        // GPS-координаты объекта
        // <GPSLatitude>55.749182</GPSLatitude>
        // <GPSLongitude>38.023683</GPSLongitude>
        if(@$Obj->Latitude > 0 && @$Obj->Longitude > 0) {
            $Element->appendChild( $XmlTag->createElement('GPSLatitude', $Obj->Latitude) );
            $Element->appendChild( $XmlTag->createElement('GPSLongitude', $Obj->Longitude) );
        }

        // Тип цены объекта, значение из справочника <Price_Type>полная стоимость продажи</Price_Type>
        // TODO не все типы учтены!
        $Element->appendChild( $XmlTag->createElement('Price_Type', 'полная стоимость продажи') );

        // Цена <Price>2700000</Price>
        $Element->appendChild( $XmlTag->createElement('Price', $Obj->Price) );

        //Валюта, значение из справочника <Currency>р.</Currency>
        $Param = GetObjectParamByIdAndColumn($Obj->Currency, 'MielMark');
        $currency = $XmlTag->createElement('Currency', $Param);
        $Element->appendChild($currency);

        //Тип объекта, значение из справочника <Object_Type>Квартира</Object_Type>
        $ObjectType = GetObjectTypeByIdAndColumn($Obj->ObjectType, 'MielMark'); // $ObjectType используется ниже
        $property = $XmlTag->createElement('Object_Type', $ObjectType);
        $Element->appendChild($property);

        // Количество комнат <RoomCount>1</RoomCount>
        if($Obj->RoomsCount) {
            $Element->appendChild($XmlTag->createElement('RoomCount', $Obj->RoomsCount));
        }

        // Состояние квартиры, значение из справочника <FinishState>требует ремонта</FinishState>
        if($Obj->ObjectCondition) {
            $param = $XmlTag->createElement('FinishState', GetObjectParamByIdAndColumn($Obj->ObjectCondition, 'MielMark'));
            $Element->appendChild($param);
        }
        // Балкон , значение из справочника <Balcony>Б</Balcony>
        if($Obj->Balcon) {
            $param = $XmlTag->createElement('Balcony', GetObjectParamByIdAndColumn($Obj->Balcon, 'MielMark'));
            $Element->appendChild($param);
        }
        // Вид из окна, значение из справочника <WindowView>двор</WindowView>
        if($Obj->WindowView) {
            $param = $XmlTag->createElement('WindowView', GetObjectParamByIdAndColumn($Obj->WindowView, 'MielMark'));
            $Element->appendChild($param);
        }
        // Телефон, значение из справочника <Telephone>нет</Telephone>
        if($Obj->Telephone) {
            $param = $XmlTag->createElement('Telephone', GetObjectParamByIdAndColumn($Obj->Telephone, 'MielMark'));
            $Element->appendChild($param);
        }
        // Санузел, значение из справочника <Bathroom>совмещенный</Bathroom>
        if($Obj->Toilet) {
            $Element->appendChild( $XmlTag->createElement('Bathroom', GetObjectParamByIdAndColumn($Obj->Toilet, 'MielMark')) );
        }
        //Материал несущих конструкций, значение из справочника <BearingMaterial>кирпич</BearingMaterial>
        //if($Obj->BuildingType) {
        //      $Element->appendChild($XmlTag->createElement('BearingMaterial', GetObjectParamByIdAndColumn($Obj->BuildingType, 'MielMark')));
        //}

        //Тип дома, значение по справочнику <HouseType>панельный</HouseType>
        if($Obj->BuildingType) {
            $Element->appendChild($XmlTag->createElement('HouseType', GetObjectParamByIdAndColumn($Obj->BuildingType, 'MielMark')));
        }
        // TODO Наличие паркинга: 1- есть, 0- нет <hasParking>1</hasParking>

        // Доля <Share>1/2</Share>
        if($ObjectType == 'доля' && $Obj->PartsSell && $Obj->PartsTotal) {
            $Element->appendChild( $XmlTag->createElement('Share', "{$Obj->PartsSell}/{$Obj->PartsTotal}") );
        }

        // Текст описания должен быть помещен в CDATA: <description><![CDATA[Улица Новая - это ...]]></description>
        if(strlen($Obj->Description) >= 1) {
            $text = ClearXmlText($Obj->Description);
            $note = $XmlTag->createElement('description');
            $text = $XmlTag->createCDATASection($text);
            $note->appendChild($text);
            $Element->appendChild($note);
        }

        //Корневой элемент фото <images>
        // <images><image Comment="Вид из окна" Changed="2015-10-22 13:51:18" Width="450" Height="600" Order="1">http://zd.crm.ru/work/exp/1442588202.jpg</image>


        $ImagesArr = GetImagesObjByObjectId( $Obj->id );
        if( count($ImagesArr) > 0 ) {
            $Images = $XmlTag->createElement('images');
            $c=1;
            foreach($ImagesArr as $ImgObj) {
                // Много фотографий с размерами менее 300*400 или 400*300, рекомендую их убрать из фида (эти фото в базу мы все равно не загружаем, а ошибки супятся).
                if(($ImgObj->Width >=300 && $ImgObj->Height >= 400) || ($ImgObj->Width >=400 && $ImgObj->Height >= 300)) {
                    $Image = $XmlTag->createElement('image', $CONF['MainSiteUrl'] . $ImgObj->FilePath);
                    $Image->setAttribute('Changed', $Obj->AddedDate);
                    $Image->setAttribute('Comment', '');
                    if ($ImgObj->Width) {
                        $Image->setAttribute('Width', $ImgObj->Width);
                    }
                    if ($ImgObj->Height) {
                        $Image->setAttribute('Height', $ImgObj->Height);
                    }

                    $Image->setAttribute('Order', $c);
                    $Images->appendChild($Image);
                    $c++;
                }
            }
            $Element->appendChild($Images);
        }

        $Document->appendChild($Element);

    }

    return $Document;
}
