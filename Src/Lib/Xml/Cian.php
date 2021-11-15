<?php
// admin_area 	числовой идентификатор административной области,
// http://www.cian.ru/admin_areas.php
// 84 строки
// некоторые строки не совпадают с КЛАДРом,
$CianAdminAreas['Москва'] = '1';
$CianAdminAreas['Московская область'] = '2';
$CianAdminAreas['Санкт-Петербург'] = '10';
$CianAdminAreas['Ленинградская область'] = '11';
$CianAdminAreas['Владимирская область'] = '3';
$CianAdminAreas['Калужская область'] = '4';
$CianAdminAreas['Рязанская область'] = '5';
$CianAdminAreas['Смоленская область'] = '9';
$CianAdminAreas['Тверская область'] = '6';
$CianAdminAreas['Тульская область'] = '7';
$CianAdminAreas['Ярославская область'] = '8';
$CianAdminAreas['Амурская область'] = '12';
$CianAdminAreas['Архангельская область'] = '13';
$CianAdminAreas['Астраханская область'] = '14';
$CianAdminAreas['Белгородская область'] = '15';
$CianAdminAreas['Брянская область'] = '16';
$CianAdminAreas['Волгоградская область'] = '17';
$CianAdminAreas['Вологодская область'] = '18';
$CianAdminAreas['Воронежская область'] = '19';
$CianAdminAreas['Ивановская область'] = '20';
$CianAdminAreas['Иркутская область'] = '21';
$CianAdminAreas['Калининградская область'] = '22';
$CianAdminAreas['Камчатский край'] = '85';
$CianAdminAreas['Кемеровская область'] = '23';
$CianAdminAreas['Кировская область'] = '24';
$CianAdminAreas['Костромская область'] = '25';
$CianAdminAreas['Курганская область'] = '26';
$CianAdminAreas['Курская область'] = '27';
$CianAdminAreas['Липецкая область'] = '28';
$CianAdminAreas['Магаданская область'] = '29';
$CianAdminAreas['Мурманская область'] = '30';
$CianAdminAreas['Нижегородская область'] = '31';
$CianAdminAreas['Новгородская область'] = '32';
$CianAdminAreas['Новосибирская область'] = '33';
$CianAdminAreas['Омская область'] = '34';
$CianAdminAreas['Оренбургская область'] = '35';
$CianAdminAreas['Орловская область'] = '36';
$CianAdminAreas['Пензенская область'] = '37';
$CianAdminAreas['Пермский край'] = '84';
$CianAdminAreas['Псковская область'] = '38';
$CianAdminAreas['Ростовская область'] = '39';
$CianAdminAreas['Самарская область'] = '40';
$CianAdminAreas['Саратовская область'] = '41';
$CianAdminAreas['Сахалинская область'] = '42';
$CianAdminAreas['Свердловская область'] = '43';
$CianAdminAreas['Тамбовская область'] = '44';
$CianAdminAreas['Томская область'] = '45';
$CianAdminAreas['Тюменская область'] = '46';
$CianAdminAreas['Ульяновская область'] = '47';
$CianAdminAreas['Челябинская область'] = '48';
$CianAdminAreas['Забайкальский край'] = '49';
$CianAdminAreas['Алтайский край'] = '71';
$CianAdminAreas['Республика Алтай'] = '53';
$CianAdminAreas['Республика Адыгея'] = '50';
$CianAdminAreas['Республика Башкортостан'] = '51';
$CianAdminAreas['Республика Бурятия'] = '52';
$CianAdminAreas['Республика Дагестан'] = '54';
$CianAdminAreas['Республика Ингушетия'] = '55';
$CianAdminAreas['Кабардино-Балкарская Республика'] = '56';
$CianAdminAreas['Республика Калмыкия'] = '57';
$CianAdminAreas['Карачаево-Черкесская Республика'] = '58';
$CianAdminAreas['Республика Карелия'] = '59';
$CianAdminAreas['Республика Коми'] = '60';
$CianAdminAreas['Республика Марий эл'] = '61';
$CianAdminAreas['Республика Мордовия'] = '62';
$CianAdminAreas['Республика Саха (Якутия)'] = '63';
$CianAdminAreas['Республика Северная Осетия-Алания'] = '64';
$CianAdminAreas['Республика Татарстан'] = '65';
$CianAdminAreas['Республика Тыва'] = '66';
$CianAdminAreas['Удмуртская Республика'] = '67';
$CianAdminAreas['Республика Хакасия'] = '68';
$CianAdminAreas['Чеченская Республика'] = '69';
$CianAdminAreas['Чувашская Республика'] = '70';
$CianAdminAreas['Краснодарский край'] = '72';
$CianAdminAreas['Красноярский край'] = '73';
$CianAdminAreas['Крым и Севастополь'] = '87';
$CianAdminAreas['Приморский край'] = '74';
$CianAdminAreas['Ставропольский край'] = '75';
$CianAdminAreas['Хабаровский край'] = '76';
$CianAdminAreas['Еврейская автономная область'] = '77';
$CianAdminAreas['НАО'] = '78';
$CianAdminAreas['ХМАО'] = '80';
$CianAdminAreas['Чукотский автономный округ'] = '81';
$CianAdminAreas['ЯНАО'] = '82';

$CianAreasFormatted = array();// делаем новый массив без лишних символов, для сверки с пользовательским вводом (на случай попадания мусора)
foreach($CianAdminAreas as $k => $v) { $CianAreasFormatted[ preg_replace('/\W/iu', '', $k) ] = $v; }

function BuildXmlForCian($XmlTag, $Flats, $MainSqlQuery, $SysParams) {
    global $CONF;
    global $CianAdminAreas;
    global $CianAreasFormatted;
    $tmp = mysql_query($MainSqlQuery);
    $Result = null;
    while($Obj = mysql_fetch_object($tmp)) {

        $Offer = $XmlTag->createElement('offer');

        $id = $XmlTag->createElement('id', $Obj->id);
        $Offer->appendChild($id);

        if($SysParams['RealtyType'] == 'country') {
            $Status = $XmlTag->createElement('deal_type', GetObjectParamByIdAndColumn($Obj->DealType, 'CianMark'));
            $Offer->appendChild($Status);

            $ObjectType = $XmlTag->createElement('realty_type', $Obj->ObjectType);
            $Offer->appendChild($ObjectType);

        }
        if($SysParams['RealtyType'] == 'city') {
            /*  количество комнат
               0 – если комната
               от 1 до 5 – сколькикомнатная квартира
               6 – многокомнатная квартира (более 5 комнат)
               7 – свободная планировка
               8 – доля в квартире
               9 – студия
            */
            $rooms_num = null;
            if($Obj->ObjectTypeId == 1) {
                // это квартира
                if($Obj->RoomsCount >= 1 && $Obj->RoomsCount <= 5) {
                    // от 1 до 5 – сколькикомнатная квартира
                    $rooms_num = $Obj->RoomsCount;
                } elseif($Obj->RoomsCount >= 6) {
                    // 6 – многокомнатная квартира (более 5 комнат)
                    $rooms_num = 6;
                } else {
                    // не указано количество комнат, ставим 1 по-умолчанию
                    //TODO сделать протоколирование ошибки в интерфейс?
                    $rooms_num = 1;
                }
            } elseif($Obj->ObjectTypeId == 2) {
                // это доля
                $rooms_num = 8;
                //TODO не хватает полей указывающих подробные части доли
            } elseif($Obj->ObjectTypeId == 3) {
                // это комната
                $rooms_num = 0;
            } else {
                // ошибка
                //TODO сделать протоколирование ошибки в интерфейс?
            }
            // TODO нехватает следующих типов: 7 – свободная планировка, 9 – студия
            $RoomsNum = $XmlTag->createElement('rooms_num', $rooms_num);
            $Offer->appendChild($RoomsNum);
        }

        if($SysParams['RealtyType'] == 'city') {
            // площадь в кв. метрах
            // <area rooms="25 15 20 20" living="80" kitchen="12" total="120"/>
            // TODO нехватает полей для rooms - "по комнатам (текстовое поле)"
            $Area = $XmlTag->createElement('area');
            $Area->setAttribute('living', $Obj->SquareLiving);
            $Area->setAttribute('kitchen', $Obj->SquareKitchen);
            $Area->setAttribute('total', $Obj->SquareAll);
            $Offer->appendChild($Area);
        }
        if($SysParams['RealtyType'] == 'country') {
            //<area living="350" region="12"/>
            $Area = $XmlTag->createElement('area');
            if($Obj->SquareLiving > 0) { $Area->setAttribute('living', $Obj->SquareLiving); }  // living 	площадь дома в м2, параметр является необязательным, если выбранный тип объекта – земельный участок (realty_type="A")
            $Area->setAttribute('region', $Obj->LandSquare);    // 	region 	площадь участка в сотках
            $Offer->appendChild($Area);

            $land_type = $XmlTag->createElement('land_type', GetObjectParamByIdAndColumn($Obj->LandTypeId, 'CianMark'));
            $Offer->appendChild($land_type);

        }


        // <price currency="USD">380000</price>
        // currency 	валюта (USD, RUB, EUR), по-умолчанию, RUB
        $Param = GetObjectParamByIdAndColumn($Obj->Currency, 'CianMark');
        $Price = $XmlTag->createElement('price', $Obj->Price);
        $Price->setAttribute('currency', $Param);
        $Offer->appendChild($Price);

        if($SysParams['RealtyType'] == 'city') {
            // <floor total="10" type="3">4</floor>
            //тип дома    1 – панельный, 2 – кирпичный, 3 – монолитный,
            //            4 – кирпично-монолитный, 5 – блочный, 6 – деревянный, 7 – «сталинский»,
            $Floor = $XmlTag->createElement('floor', $Obj->Floor);
            $Floor->setAttribute('total', $Obj->Floors);
            $Param = GetObjectParamByIdAndColumn($Obj->BuildingType, 'CianMark');
            $Floor->setAttribute('type', $Param);
            $Offer->appendChild($Floor);
        }
        if($SysParams['RealtyType'] == 'country') {
            //floor_total 	количество этажей в доме
            $Floors = $XmlTag->createElement('floor_total', $Obj->Floors);
            $Offer->appendChild($Floors);
        }

        // контактные номера телефонов, максимум 2 номера, разделитель между номерами - «;», длина номера - 10 цифр
        // <phone>9261234567;4957251234</phone>
        $AgentPhonesArr  = array($Obj->MobilePhone, $Obj->MobilePhone1, $Obj->MobilePhone2);
        $PhoneString    = substr( $AgentPhonesArr[$Obj->OwnerPhoneId], -10);
        if(isset($Obj->AddCorpPhone) && $Obj->AddCorpPhone > 0 && strlen($CONF['SysParams']['NavigatorXmlCompanyPhone']) >= 7) {
            // добавлять ли корпоративный номер
            $PhoneString .= ';'.substr( $CONF['SysParams']['NavigatorXmlCompanyPhone'], -10);
        }
        $Phone = $XmlTag->createElement('phone', $PhoneString );
        $Offer->appendChild($Phone);

        // обработка теа Address
        $Address = $XmlTag->createElement('address');
        if($SysParams['RealtyType'] == 'city') {
            // <address admin_area="1" locality="Москва" street="Ленинградский проспект" house_str="55"/>
            if($Obj->Region == 'Москва') {
                $Address->setAttribute('locality', $Obj->Region );
                $admin_area = 1;        // значение взято из справочника Циана (числовой идентификатор административной области)
            /*} elseif($Obj->Region == 'Московская область') {
                if(!$Obj->Raion) { $Obj->Raion = $Obj->Region; }
                $Address->setAttribute('locality', $Obj->Raion . ', ' . $Obj->PlaceType . ' ' . $Obj->City );
                $admin_area = 2;*/
            } else {
                // далекий объект-блать или край: определяем $admin_area - название в КЛАДР может не совпасть с Cian admin_area
                $regionFormatted = preg_replace('/\W/iu', '', $Obj->Region);
                // делаем сравнение только по буквам, отрубая возможные пробелы и т.д.
                if( @isset( $CianAreasFormatted[$regionFormatted] ) ) {
                    if(!$Obj->Raion) { $Obj->Raion = $Obj->Region; }
                    $admin_area = $CianAreasFormatted[$regionFormatted]; // значение берется из справочника Циана (числовой идентификатор административной области)
                    $Address->setAttribute('locality', $Obj->Raion . ', ' . $Obj->PlaceType . ' ' . $Obj->City );
                } else {
                    CrmCopyNoticeLog("id: {$Obj->id} Не могу найти регион '{$Obj->Region}' -> '$regionFormatted' в CianAdminAreas");
                }
            }

            // <metro id="5" ttime="12"/> или <metro id="5" wtime="5"/>
            if( $Obj->CianMetroStationId > 0) {
                $Metro = $XmlTag->createElement('metro');
                $Metro->setAttribute($Obj->CianMetroWayTypeMark, $Obj->MetroWayMinutes);
                $Metro->setAttribute('id', $Obj->CianMetroStationId);
                $Offer->appendChild($Metro);
            }
        }
        if($SysParams['RealtyType'] == 'country') {
            //<address route="256" mcad="25" admin_area="2" locality="Пушкинский район, Пушкино" street="ул. Некрасова" house_str="9"/>
            // далекий объект-блать или край: определяем $admin_area - название в КЛАДР может не совпасть с Cian admin_area
            $regionFormatted = preg_replace('/\W/iu', '', $Obj->Region);
            // делаем сравнение только по буквам, отрубая возможные пробелы и т.д.
            if( @isset( $CianAreasFormatted[$regionFormatted] ) ) {
                if(!$Obj->Raion) { $Obj->Raion = $Obj->Region; }
                $admin_area = $CianAreasFormatted[$regionFormatted]; // значение взято из справочника Циана (числовой идентификатор административной области)
                $Address->setAttribute('locality', $Obj->Raion . ', ' . $Obj->PlaceType . ' ' . $Obj->City );
                $Address->setAttribute('mcad', $Obj->Distance);
                $Address->setAttribute('route', $Obj->CianRouteId); // значение взято из справочника Циана: http://www.cian.ru/routes.php

            } else {
                CrmCopyNoticeLog("id: {$Obj->id} Не могу найти регион '{$Obj->Region}' в CianAdminAreas");
            }

        }
        $Address->setAttribute('admin_area', $admin_area);
        $Address->setAttribute('street', $Obj->Street);
        $Address->setAttribute('house_str', $Obj->HouseNumber);
        $Offer->appendChild($Address);


        // дополнительные параметры
        $Options = $XmlTag->createElement('options');

        if($SysParams['RealtyType'] == 'city') {
            //object_type - тип жилья - 1 – вторичное жилье, 2 – новостройка
            $Novo = GetObjectParamByIdAndColumn($Obj->ObjectAgeType, 'CianMark');
            $Options->setAttribute('object_type', $Novo);
            if($Novo == 2) {
                // Для новостроек:
                // ddu - Договор долевого участия, zhsk - Договор ЖСК,pereustupka - Договор уступки прав требования, pdkp - Предварительный договор купли-продажи,invest - Договор инвестирования,free - Свободная продажа,alt - Альтернатива,по-умолчанию - свободная продажа
                $Param = GetObjectParamByIdAndColumn($Obj->NovoDealType, 'CianMark');
                $Options->setAttribute('sale_type', $Param);
                $Offer->appendChild($Options);
            } else {
                // Для вторичного жилья:
                // sale_type - тип продажи - F – свободная продажа, A – альтернатива
                $Param = GetObjectParamByIdAndColumn($Obj->DealType, 'CianMark');
                $Options->setAttribute('sale_type', $Param);
                $Offer->appendChild($Options);
            }




            $Param = GetObjectParamByIdAndColumn($Obj->Telephone, 'CianMark');
            if(isset($Param)) {
                $Options->setAttribute('phone', $Param);
                $Offer->appendChild($Options);
            }
            // lift_p -	количество пассажирских лифтов
            // lift_g -	количество грузовых лифтов
            $Param = GetObjectParamByIdAndColumn($Obj->Lift, 'CianMark');
            if($Param > 0) {
                if($Param == 1) {
                    $Options->setAttribute('lift_p', '0');
                    $Options->setAttribute('lift_g', '0');
                } elseif($Param == 2) {
                    $Options->setAttribute('lift_p', '1');
                } elseif($Param == 3) {
                    $Options->setAttribute('lift_g', '1');
                } elseif($Param == 4) {
                    $Options->setAttribute('lift_p', '1');
                    $Options->setAttribute('lift_g', '1');
                }
                $Offer->appendChild($Options);
            }

            // balсon 	количество балконов
            // lodgia 	количество лоджий
            $Param = GetObjectParamByIdAndColumn($Obj->Balcon, 'CianMark');
            if($Param > 0) {
                if($Param == 1) {
                    $Options->setAttribute('balkon', '0');
                    $Options->setAttribute('lodgia', '0');
                } elseif($Param == 2) {
                    $Options->setAttribute('balkon', '1');
                } elseif($Param == 3) {
                    $Options->setAttribute('balkon', '2');
                } elseif($Param == 4) {
                    $Options->setAttribute('balkon', '3');
                } elseif($Param == 5) {
                    $Options->setAttribute('lodgia', '1');
                } elseif($Param == 6) {
                    $Options->setAttribute('lodgia', '2');
                } elseif($Param == 7) {
                    $Options->setAttribute('lodgia', '3');
                } elseif($Param == 8) {
                    $Options->setAttribute('balkon', '1');
                    $Options->setAttribute('lodgia', '1');
                } elseif($Param == 9) {
                    $Options->setAttribute('balkon', '1');
                    $Options->setAttribute('lodgia', '2');
                } elseif($Param == 10) {
                    $Options->setAttribute('balkon', '2');
                    $Options->setAttribute('lodgia', '2');
                } elseif($Param == 11) {
                    $Options->setAttribute('lodgia', '4');
                }
                $Offer->appendChild($Options);
            }

            // su_s -	количество совмещенных санузлов
            // su_r -	количество раздельных санузлов
            $Param = GetObjectParamByIdAndColumn($Obj->Toilet, 'CianMark');
            if($Param > 0) {
                if($Param == 1) {
                    $Options->setAttribute('su_s', '0');
                    $Options->setAttribute('su_r', '1');
                } elseif($Param == 2) {
                    $Options->setAttribute('su_s', '1');
                    $Options->setAttribute('su_r', '0');
                } elseif($Param == 3) {
                    $Options->setAttribute('su_r', '2');
                } elseif($Param == 4) {
                    $Options->setAttribute('su_r', '3');
                } elseif($Param == 5) {
                    $Options->setAttribute('su_r', '4');
                } elseif($Param == 6) {
                    $Options->setAttribute('su_s', '0');
                    $Options->setAttribute('su_r', '0');
                }
                $Offer->appendChild($Options);
            }

            // windows 	куда выходят окна
            // 1 – двор, 2 – улица, 3 – двор и улица
            $Param = GetObjectParamByIdAndColumn($Obj->WindowView, 'CianMark');
            if(isset($Param)) {
                $Options->setAttribute('windows', $Param);
                $Offer->appendChild($Options);
            }
        }
/*
        if($SysParams['RealtyType'] == 'country') {
            $Param = GetObjectParamByIdAndColumn($Obj->CountryElectro, 'CianMark');
            if(isset($Param)) {
                $Options->setAttribute('elect', $Param);
                $Offer->appendChild($Options);
            }

            $Param = GetObjectParamByIdAndColumn($Obj->CountryHeat, 'CianMark');
            if(isset($Param)) {
                $Options->setAttribute('heat', $Param);
                $Offer->appendChild($Options);
            }

            $Param = GetObjectParamByIdAndColumn($Obj->CountrySewer, 'CianMark');
            if(isset($Param)) {
                $Options->setAttribute('canal', $Param);
                $Offer->appendChild($Options);
            }

            $Param = GetObjectParamByIdAndColumn($Obj->CountryGas, 'CianMark');
            if(isset($Param)) {
                $Options->setAttribute('gas', $Param);
                $Offer->appendChild($Options);
            }

            $Param = GetObjectParamByIdAndColumn($Obj->CountryWater, 'CianMark');
            if(isset($Param)) {
                $Options->setAttribute('water', $Param);
                $Offer->appendChild($Options);
            }
            $Param = GetObjectParamByIdAndColumn($Obj->CountrySecure, 'CianMark');
            if(isset($Param)) {
                $Options->setAttribute('security', $Param);
                $Offer->appendChild($Options);
            }
            ////////////////////
            $Param = GetObjectParamByIdAndColumn($Obj->CountryToilet, 'CianMark');
            if(isset($Param)) {
                $Options->setAttribute('toilet', $Param);
                $Offer->appendChild($Options);
            }
            $Param = GetObjectParamByIdAndColumn($Obj->CountryBath, 'CianMark');
            if(isset($Param)) {
                $Options->setAttribute('bathhouse', $Param);
                $Offer->appendChild($Options);
            }
            $Param = GetObjectParamByIdAndColumn($Obj->CountryGarage, 'CianMark');
            if(isset($Param)) {
                $Options->setAttribute('garage', $Param);
                $Offer->appendChild($Options);
            }
            $Param = GetObjectParamByIdAndColumn($Obj->CountryPool, 'CianMark');
            if(isset($Param)) {
                $Options->setAttribute('pool', $Param);
                $Offer->appendChild($Options);
            }
            $Param = GetObjectParamByIdAndColumn($Obj->CountryMaterial, 'CianMark');
            if(isset($Param)) {
                $Options->setAttribute('material', $Param);
                $Offer->appendChild($Options);
            }
            $Param = GetObjectParamByIdAndColumn($Obj->CountryPhone, 'CianMark');
            if(isset($Param)) {
                $Options->setAttribute('phone', $Param);
                $Offer->appendChild($Options);
            }

        }*/


        // TODO нехватает поля "возможность ипотеки"

        // note - текстовое примечание, помещенное внутрь CDATA
        if(strlen($Obj->Description) >= 1) {
            $text = ClearXmlText($Obj->Description);
            $note = $XmlTag->createElement('note');
            $text = $XmlTag->createCDATASection($text);
            $note->appendChild($text);
            $Offer->appendChild($note);
        }

        // photo -	постоянная ссылка на файл с фото объекта
        $ImagesArr = GetImagesObjByObjectId( $Obj->id );
        foreach($ImagesArr as $ImgObj) {
            $img = $XmlTag->createElement('photo', $CONF['MainSiteUrl'] . $ImgObj->FilePath);
            $Offer->appendChild($img);
        }
        //if($SysParams['RealtyType'] == 'country') {
        //}



        // premium - премиум-статус объявления
        if(CheckCianPremium($Obj->id) && @$Obj->TarifShortName == 'TrfCianPremium') { // И если премиум тариф включен
            $premium = $XmlTag->createElement('premium', '1');
            $Offer->appendChild($premium);
            $Flats->appendChild($Offer);    // добавляем!

        } elseif( @$Obj->TarifShortName == 'TrfCian') { // И если простой тариф включен
            $premium = $XmlTag->createElement('premium', '0');
            $Offer->appendChild($premium);
            $Flats->appendChild($Offer);    // добавляем!

        } else {
            // TODO добавить обработку TrfCianRentPremium
        }


    }

    return $Flats;
}

function CheckCianPremium($ObjectId, $TarifShortName = 'TrfCianPremium') {
    // Премиум опция обрабатывается как отдельный портал
    $out = false;
    list($TarifId, $TarifName) = GetBillAdTarifByShortName($TarifShortName);
    $sql = "SELECT
                id
            FROM
                AdPortalObjects
            WHERE
                ObjectId = '$ObjectId' AND
                TarifId = $TarifId
            ";
    $str = mysql_fetch_object( mysql_query($sql) );
    if( isset($str->id) && $str->id > 0 ) {
        $out = true;
    }
    return $out;
}


