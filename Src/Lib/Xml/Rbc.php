<?php

    function BuildXmlForRbc($XmlTag, $Flats, $MainSqlQuery, $SysParams = array()) {
        global $CONF;
        $tmp = mysql_query($MainSqlQuery);
        $Result = null;
        while($Obj = mysql_fetch_object($tmp)) {
            $Element = $XmlTag->createElement( $SysParams['ElementTag'] );
            //$Element->setAttribute( 'internal-id', 123 );

            // Идентификатор <id>3456345</id> – внутренний идентификатор предложения в базе данных партнера, у каждого объекта должен быть свой уникальный id
            $id = $XmlTag->createElement('id', $Obj->id);
            $Element->appendChild($id);

            //Тип сделки <deal_type>R</deal_type> – тип сделки. Возможные значения: S – продажа, R — аренда
            // todo нет аренды!
            $actual = $XmlTag->createElement('deal_type', 'S');
            $Element->appendChild($actual);

            if($SysParams['RealtyType'] == 'country') {
                //<realty_type>K</realty_type> – тип недвижимости, возможные значения: K - коттедж, A - земельный участок
                $Param = GetObjectTypeByIdAndColumn($Obj->ObjectType, 'RbcMark');
                $RealtyType = $XmlTag->createElement('realty_type', $Param);
                $Element->appendChild($RealtyType);
            }

            //Расположение
            $Address = $XmlTag->createElement('address');
            $region = $XmlTag->createElement('region', $Obj->Region);

            // <region type="M">Москва</region> – возможные значения — R и M. R – область, M — крупный город (Москва, Санкт-Петербург)
            if($Obj->Region == 'Москва') { $type = 'M'; } else { $type = 'R'; }
            $region->setAttribute('type', $type);

            //TODO <district>Юго-Западный</district> – район города, если неизвестен, то тэг не указывается

            // <metro>Проспект Вернадского</metro> – название ближайшей станции метро
            if($Obj->MetroStation1Id > 0) {
                $metro = $XmlTag->createElement('metro', GetMetroStationNameById($Obj->MetroStation1Id));} else {$metro=null;}

            // <street>ул. Удальцова</street> – название улицы
            $street = $XmlTag->createElement('street', $Obj->Street);

            // <houseNo>69</houseNo> – номер дома
            $houseNo = $XmlTag->createElement('houseNo', $Obj->HouseNumber);

            // <range type="T">5</range> – тип расстояния от метро, возможные значения T и P. T - транспорт, P - пешком, после типа пишется время в минутах
            if($SysParams['RealtyType'] == 'city') {
                $Param = GetObjectParamByIdAndColumn($Obj->MetroWayType, 'RbcMark');
                $range = $XmlTag->createElement('range', $Obj->MetroWayMinutes);
                $range->setAttribute('type', $Param);
            } else {$range = null;}

            // TODO <range>108</range>  – расстояние от областного центра в километрах


            //<highway>Киевское ш.</highway> – название шоссе
            if($Obj->HighwayId > 0) {
                $highway = $XmlTag->createElement('highway', $Obj->DirectionName); } else {$highway=null;}

            // <range>108</range> – расстояние от областного центра в километрах
            if($Obj->Distance > 0) {
                $otMKAD = $XmlTag->createElement('range', $Obj->Distance ); } else {$otMKAD=null;}
            //$Element->appendChild($otMKAD);

            // закрываем групповой тег - address
            $Address->appendChild($region);
            // address/district	Для недвижимости расположенной в области населенный пункт обязательное поле
            if($Obj->Region != 'Москва') {
                // <district>Орехово-Зуево</district> – крупный населенный пункт
                //if(strlen($Obj->City) > 0) {
                //$districtCity = $XmlTag->createElement('district', $Obj->City); } else { $districtCity = null; }
                $districtCity = $XmlTag->createElement('district', $Obj->City);
                $Address->appendChild($districtCity);
            }
            if($metro) { $Address->appendChild($metro); }
            $Address->appendChild($street);
            $Address->appendChild($houseNo);
            if($range)   { $Address->appendChild($range); }
            if($otMKAD)  { $Address->appendChild($otMKAD); }
            //if($highway) { $Address->appendChild($highway); }

            //if($districtCity) { $Address->appendChild($districtCity); }
            $Element->appendChild($Address);

            // Цена
            $price = $XmlTag->createElement('price', $Obj->Price);
            $Element->appendChild($price);

            // Валюта <currency>RUR</currency> – возможные значения: RUR или USD
            $Param = GetObjectParamByIdAndColumn($Obj->Currency, 'RbcMark');
            $currency = $XmlTag->createElement('currency', $Param);
            $Element->appendChild($currency);


            /*<area><total>65</total> – общая площадь квартиры в квадратных метрах
                    <live>45</live> – жилая площадь квартиры в квадратных метрах
                    <kitchen>10</kitchen> – площадь кухни в квадратных метрах
            </area>*/
            $area  = $XmlTag->createElement('area');

            if($SysParams['RealtyType'] == 'country' && $Obj->LandSquare > 0) { // только для загородки
                //<plot>20</plot> – площадь участка в сотках
                $plot = $XmlTag->createElement('plot', $Obj->LandSquare);
                $area->appendChild($plot);

            }
            ($Obj->SquareAll > 0) ? $total = $XmlTag->createElement('total', $Obj->SquareAll) : $total  = $XmlTag->createElement('total', 0);
            $area->appendChild($total);

            if($SysParams['RealtyType'] == 'city') {
                // общая и кухня только для городской
                ($Obj->SquareLiving > 0)  ? $live    = $XmlTag->createElement('live', $Obj->SquareLiving)    : $live   = $XmlTag->createElement('live', 0);
                ($Obj->SquareKitchen > 0) ? $kitchen = $XmlTag->createElement('kitchen', $Obj->SquareKitchen): $kitchen= $XmlTag->createElement('kitchen', 0);
                $area->appendChild($live);
                $area->appendChild($kitchen);
            }
            $Element->appendChild($area);


            /*<description><short>краткое описание</short><full>полное описание</full></description>*/
            $description = $XmlTag->createElement('description');
            $TextShort = ClearXmlText($Obj->DescriptionShort);
            $short = $XmlTag->createElement('short', $TextShort );
            $text = ClearXmlText($Obj->Description);
            $full = $XmlTag->createElement('full', $text );
            $description->appendChild($short);
            $description->appendChild($full);
            $Element->appendChild($description);

            // <photo>http://www.realty-xxxxxxxx.ru/img/8765487_3.gif</photo> – фотографии объекта
            $ImagesArr = GetImagesObjByObjectId( $Obj->id );
            if( count($ImagesArr) > 0 ) {
                $c = 0;
                foreach($ImagesArr as $ImgObj) {
                    $img = $XmlTag->createElement('photo', $CONF['MainSiteUrl'] . $ImgObj->FilePath);
                    $Element->appendChild($img);
                    //if($c>=20) {break;}
                    $c++;
                }
            }
            // TODO <plan>http://www.real-x.ru/img/877_5.gif</plan>
            // TODO – Изображение с планом объекта, обязательно указание расширения. Изображений может быть несколько. Они так же добавляются через тэг <plan></plan>


            // Опции:

            if($SysParams['RealtyType'] == 'city') {
                if( $Obj->RoomsCount > 0) {
                    // TODO <rooms>3</rooms> – количество комнат в цифрах. Продажа комнаты указывается буквой K
                    $Param = GetObjectTypeByIdAndColumn($Obj->ObjectType, 'RbcMark');
                    if($Param == 'K') {
                        $RoomsCount = $XmlTag->createElement('rooms', $Param); // это комната
                    } else {
                        $RoomsCount = $XmlTag->createElement('rooms', $Obj->RoomsCount);
                    }
                    $Element->appendChild($RoomsCount); }
                //<floors>12</floors> – количество этажей в доме
                if( $Obj->Floors > 0 ) {
                    $floors = $XmlTag->createElement('floors', $Obj->Floors);
                    $Element->appendChild($floors); }
                //<floors_count>5</floors_count> – этаж квартиры
                if( $Obj->Floor > 0 ) {
                    $floors_count = $XmlTag->createElement('floors_count', $Obj->Floor);
                    $Element->appendChild($floors_count); }

                /////////////////////////////////
                // <options> – открывающий тэг дополнительных сведений о квартире, только для городской

                $options  = $XmlTag->createElement('options');
                // <lift>1</lift> – наличие лифта. Возможные значения: 0 – нет, 1- есть. Если параметр неизвестен, то тэг не указывается.
                $Param = GetObjectParamByIdAndColumn($Obj->Lift, 'RbcMark');
                $lift  = $XmlTag->createElement('lift', $Param);

                // 0-3 только балконы;      0=нет,1=1,2=2,3=3
                // 4-7 только лоджии;       4=нет,5=1,6=2,7=3
                // 8-11 и балконы и лоджии; 8=ничего нет, 9=1балк и 1лодж, 10=2балк и 2лодж, 11=3балк и 3лодж
                //<balcon>1</balcon> – наличие балкона. Возможные значения: 0 – нет, 1 - один балкон, 2 – два балкона, 3 - три балкона. Если параметр неизвестен, то тэг не указывается.
                //<loggia>0</loggia> – наличие лоджии. Возможные значения: 0 – нет, 1 – одна лоджия , 2 – две лоджии, 3 – три лоджии. Если параметр неизвестен, то тэг не указывается.
                $l = 0;
                $b = 0;
                $BalcOrLodg  = GetObjectParamByIdAndColumn($Obj->Balcon, 'RbcMark');
                switch($BalcOrLodg) {
                    case 0: $b = 0;         break;
                    case 1: $b = 1;         break;
                    case 2: $b = 2;         break;
                    case 3: $b = 3;         break;
                    case 4:         $l = 0; break;
                    case 5:         $l = 1; break;
                    case 6:         $l = 2; break;
                    case 7:         $l = 3; break;
                    case 8: $b = 0; $l = 0; break;
                    case 9: $b = 1; $l = 1; break;
                    case 10: $b = 2; $l = 2; break;
                    case 11: $b = 3; $l = 3; break;
                }
                $balcon = $XmlTag->createElement('balcon',  $b);
                $loggia = $XmlTag->createElement('loggia',  $l);
                $options->appendChild($balcon);
                $options->appendChild($loggia);
                // <bathroom>S</bathroom> – тип санузла. Возможные значения: U - смежный, S – раздельный, D – два санузла. Если параметр неизвестен, то тэг не указывается.
                $param  = GetObjectParamByIdAndColumn($Obj->Toilet, 'RbcMark');
                if($param) {
                    $bathroom = $XmlTag->createElement('bathroom',  $param);
                    $options->appendChild($bathroom);
                }
                // <musor>1</musor> – наличие мусоропровода. Возможные значения: 0 - нет, 1- есть. Если параметр неизвестен, то тэг не указывается.
                $param  = GetObjectParamByIdAndColumn($Obj->Garbage, 'RbcMark');
                if($param) {
                    $musor = $XmlTag->createElement('musor',  $param);
                    $options->appendChild($musor);
                }
                //<havephone>1</havephone> – наличие телефона. Возможные значения: 0 - нет, 1- есть. Если параметр неизвестен, то тэг не указывается.
                $param  = GetObjectParamByIdAndColumn($Obj->Telephone, 'RbcMark');
                if($param) {
                    $Telephone = $XmlTag->createElement('havephone',  $param);
                    $options->appendChild($Telephone);
                }

                $Element->appendChild($options); /////////////////////////////////
            }


            // <contact> – Открывающий тэг для контактов. Можно добавлять этот тег, если у агентства несколько отделений,
            // этим тегом можно привязать к определенному объекту определенное отделение
            $contact  = $XmlTag->createElement('contact');

            $AgentPhonesArr = array($Obj->MobilePhone, $Obj->MobilePhone1, $Obj->MobilePhone2);
            $info = $XmlTag->createElement('info', $AgentPhonesArr[$Obj->OwnerPhoneId]);
            $contact->appendChild($info);
            $Element->appendChild($contact);







            $Flats->appendChild($Element);
        }

        return $Flats;
    }
