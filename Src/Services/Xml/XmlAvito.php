<?php

    require(dirname(__FILE__) . '/../../Conf/Config.php');
    require(dirname(__FILE__) . '/../../Lib/Xml/Xml.php');

    DBConnect();

    $XmlTag = new DOMDocument('1.0', 'UTF-8');
    // создаем корневой элемент
    $Ads = $XmlTag->createElement('Ads');
    $Ads->setAttribute('target', 'Avito.ru');
    $Ads->setAttribute('formatVersion', '3');

    $MainSqlQuery  = CreateSQLQueryForXMLLoad('avito');

    $Ads = BuildXmlForAvito($XmlTag, $Ads, $MainSqlQuery);

    $XmlTag->appendChild($Ads);

    $out = preg_replace('#</Ads>#', "</Ads>\n\n", $XmlTag->saveXML() );   // пробелы для читаемости
    $out = preg_replace('/></', ">\n<", $out );
    echo $out;




    function BuildXmlForAvito($XmlTag, $Ads, $MainSqlQuery) {//print $MainSqlQuery;exit;
        global $CONF;
        $tmp = mysql_query($MainSqlQuery);
        $Result = null;
        while($Obj = mysql_fetch_object($tmp)) {

            $Ad = $XmlTag->createElement('Ad');

            $id = $XmlTag->createElement('Id', $Obj->id);
            $Ad->appendChild($id);

            // Дата в формате - ГГГГ-MM-ДД
            // Дата начала экспозиции объявления
            $DateBegin = $XmlTag->createElement('DateBegin', $Obj->AvitoAddedDate);
            $Ad->appendChild($DateBegin);
            // Дата конца экспозиции объявления
            $DateEnd = $XmlTag->createElement('DateEnd', $Obj->AvitoDateEnd);
            $Ad->appendChild($DateEnd);

            //Описание - строка не более 3000 символов
            $DescrText = substr($Obj->Description, 0, 3000);
            $DescrText = preg_replace('/\n/',' ', $DescrText);
            //$DescrText = iconv('UTF-8', 'UTF-8//IGNORE', $DescrText);
            $DescrText = mb_convert_encoding($DescrText, 'UTF-8', 'UTF-8');
            $Description = $XmlTag->createElement('Description', $DescrText );
            $Ad->appendChild($Description);

            // TODO AdStatus - Информация о платной услуге, которую нужно применить к объявлению — одно из значений списка:

            // TODO EMail Контактный адрес электронной почты — строка в формате корректного email адреса, например "manager@company.com".

            // TODO AllowEmail	Возможность получать вопросы на контактный адрес электронной почты — одно из значений списка: "Да", "Нет".

            // TODO CompanyName - Название компании — строка не более 40 символов.

            // TODO ManagerName	- Имя менеджера, контактного лица компании по данному объявлению — строка не более 40 символов.

            if($Obj->RealtyType == 'country') { // TODO надо переделать на id к тбл.RealtyTypes ?
                if($Obj->ObjectTypeId == 9 || $Obj->ObjectTypeId == 10) {
                    $CategoryName = "Земельные участки";
                    /*  Земельные участки: - Вид объекта - ObjectType:
                        "Поселений (ИЖС)",
                        "Сельхозназначения (СНТ, ДНП)",
                        "Промназначения"; */
                    $ObjectType = GetObjectParamByIdAndColumn($Obj->LandTypeId, 'AvitoMark');

                } else {
                    // Категория объекта недвижимости: "Дома, дачи, коттеджи"
                    $CategoryName = "Дома, дачи, коттеджи";
                    $ObjectType = GetObjectTypeByIdAndColumn($Obj->ObjectType, 'AvitoMark');
                }
            } else {
                // Категория объекта недвижимости: "Комнаты", "Квартиры"
                $ObjectType = null;
                if($Obj->ObjectTypeId == 1 || $Obj->ObjectTypeId == 2) {
                    // это квартира или доля (доли не выделены в Авито)
                    $CategoryName = "Квартиры";
                } elseif( $Obj->ObjectTypeId == 3) {
                    // это комната
                    $CategoryName = "Комнаты";
                    //SaleRooms	Количество комнат на продажу
                    $SaleRooms = $XmlTag->createElement('SaleRooms', $Obj->RoomsSell);
                    $Ad->appendChild($SaleRooms);
                } else {
                    //другие типы авито: "Земельные участки", "Гаражи и стоянки"

                }
            }
            $Category = $XmlTag->createElement('Category', $CategoryName);
            $Ad->appendChild($Category);

            if(isset($ObjectType)) {
                // для загородки добавляем тип объекта
                $Ad->appendChild( $XmlTag->createElement('ObjectType', $ObjectType) );
            }
            if($Obj->RealtyType == 'country') {
                // Площадь земли в сотках
                $Ad->appendChild( $XmlTag->createElement('LandArea', $Obj->LandSquare) );
                // Расстояние до города в км.  Примечание:  значение 0 означает, что объект находится в черте города.
                $Ad->appendChild( $XmlTag->createElement('DistanceToCity', $Obj->Distance) );
                // Направление, можно указывать если объект не в черте города.
                $DirectionRoad = GetHighwayNameById($Obj->HighwayId);
                if($DirectionRoad) { $Ad->appendChild( $XmlTag->createElement('DirectionRoad', $DirectionRoad) ); }

                // Материал стен
                $Param = GetObjectParamByIdAndColumn($Obj->CountryWallsTypeId, 'AvitoMark');
                if($Param) { $Ad->appendChild( $XmlTag->createElement('WallsType', $Param) );    }

                if($Obj->Floors > 0) { $Ad->appendChild( $XmlTag->createElement('Floors', $Obj->Floors) ); }

            }
            if($Obj->RealtyType == 'city') {
                // Floor	Этаж, на котором находится объект
                // Floors	Количество этажей в доме
                if($Obj->Floor > 0) {
                    $Floor = $XmlTag->createElement('Floor', $Obj->Floor);
                    $Ad->appendChild($Floor);
                }
                if($Obj->Floors > 0) {
                    $Floors = $XmlTag->createElement('Floors', $Obj->Floors);
                    $Ad->appendChild($Floors);
                }
            }
            //OperationType - Продам, Сдам
            $OperationType = $XmlTag->createElement('OperationType', 'Продам');
            $Ad->appendChild($OperationType);

            // Region - Регион, в котором находится объект объявления.
            $Region = $XmlTag->createElement('Region', $Obj->Region);
            $Ad->appendChild($Region);

            // City
            // 1. Город или населенный пункт, в котором находится объект объявления — в соответствии со значениями из справочника.
            // 2. Элемент обязателен для всех регионов, кроме Москвы и Санкт-Петербурга.
            // 3. Справочник является неполным. Если требуемое значение в нем отсутствует, то указывается ближайший к вашему объекту
            //    пункт из справочника, а точное название населенного пункта указывается в элементе Street.
            // TODO Сделать проверку на совместимые с Авито города, если в авито-справочнике нашего города нет, выносим его в Street
            // TODO  и принуждаем выбрать ближайший пункт из авито справочника.(Автоматическое решение в интерфейсе формы только для авито)
            //($Obj->City != 'Москва') ? $CityPrefix = $Obj->PlaceTypeSocr.' ' : $CityPrefix = '';

            if(isset($Obj->AltCityName)) {
                $City = $XmlTag->createElement('City',  $Obj->AltCityName);
                $UserCity = $Obj->PlaceTypeSocr.' ' . $Obj->City . ', ';
            } else {
                $City = $XmlTag->createElement('City',  $Obj->City);
                $UserCity = '';
            }
            $Ad->appendChild($City);
            // District	-
            // Район города — в соответствии со значениями из справочника. Обязательно, если в справочнике для города указаны районы
            // Для Москвы и области нет районов

            // Locality	- Город или населенный пункт, уточнение (УСТАРЕЛО)
            //$Locality = $XmlTag->createElement('Locality', $Obj->PlaceTypeSocr . ' ' . $Obj->City);
            //$Ad->appendChild($Locality);

            /* Street - Адрес объекта объявления — строка до 65 символов, содержащая:
                - название улицы (обязательно) и номер дома (опционально) — если задан точный населенный пункт из справочника;
                - если нужного населенного пункта нет в справочнике, то в этом элементе нужно указать:
                - район региона (если есть),
                - населенный пункт (обязательно),
                - улицу и номер дома (опционально),
                - например для Тамбовской обл.: "Моршанский р-н, с. Устьи, ул. Лесная, д. 7"; */

            //if($Obj->RealtyType == 'city') {
                $Street = $XmlTag->createElement('Street', $UserCity . $Obj->Street . ' ' . $Obj->HouseNumber);
                $Ad->appendChild($Street);
            //}


            // Subway
            if($Obj->City == 'Москва' && $Obj->MetroStation1Id > 0) {
                $Subway = $XmlTag->createElement('Subway', $Obj->MetroStationName);
                $Ad->appendChild($Subway);
            }

            if($Obj->RealtyType == 'city') {
                // Rooms - Количество комнат в квартире
                ($Obj->RoomsCount > 9) ? $RoomsCount = '>9' : $RoomsCount = $Obj->RoomsCount;
                $Rooms = $XmlTag->createElement('Rooms', $RoomsCount);
                $Ad->appendChild($Rooms);
            }

            if($Obj->RealtyType == 'city') {
                // Square - Площадь комнаты (в м.кв.) Если продается/сдается несколько комнат, указывается их суммарная площадь
                // decimal, >= 6 (не менее 6)
                $Square = $XmlTag->createElement('Square', $Obj->SquareAll);
                $Ad->appendChild($Square);

                // MarketType - Принадлежность квартиры к рынку новостроек или вторичному, только для типа "Продам"	+	Вторичка/Новостройка
                $Param = GetObjectParamByIdAndColumn($Obj->ObjectAgeType, 'AvitoMark');
                $MarketType = $XmlTag->createElement('MarketType', $Param);
                $Ad->appendChild($MarketType);

            } elseif($Obj->RealtyType == 'country') {
                // При "Дома, дачи, коттеджи" - Площадь дома в м. кв. >= 20 (не менее 20)
                // Элемент `Square` не поддерживается в категории `Земельные участки`.
                if($CategoryName != "Земельные участки") {
                    $Square = $XmlTag->createElement('Square', $Obj->SquareLiving);
                    $Ad->appendChild($Square);
                }
            } else {
                // TODO
            }

            // HouseType - Тип дома, Значение из Справочника
            $Param = GetObjectParamByIdAndColumn($Obj->BuildingType, 'AvitoMark');
            if( isset($Param) ) {
                $HouseType = $XmlTag->createElement( 'HouseType', $Param );
                $Ad->appendChild($HouseType);
            }


            // Price	Цена в рублях
            $Price = $XmlTag->createElement('Price', $Obj->Price);
            $Ad->appendChild($Price);

            // ContactPhone - строка, содержащая только один российский телефон;
            $AgetPhonesArr = array($Obj->MobilePhone, $Obj->MobilePhone1, $Obj->MobilePhone2);

            $ContactPhone = $XmlTag->createElement('ContactPhone', $AgetPhonesArr[$Obj->OwnerPhoneId]);
            $Ad->appendChild($ContactPhone);


            // Images - принимается максимум 20 фото
            $ImagesArr = GetImagesObjByObjectId( $Obj->id );
            if( count($ImagesArr) > 0 ) {
                $c = 0;
                $Images = $XmlTag->createElement('Images');
                foreach($ImagesArr as $ImgObj) {
                    $c++;
                    $Image = $XmlTag->createElement('Image');
                    $Image->setAttribute('url', $CONF['MainSiteUrl'] . $ImgObj->FilePath);
                    $Images->appendChild($Image);
                    if($c>=20) {break;}
                }
                $Ad->appendChild($Images);
            }

            /*
                        $ = $XmlTag->createElement('xxx', $Obj->);
                        $Ad->appendChild($);
            */


            $Ads->appendChild($Ad);

        }

        return $Ads;
    }