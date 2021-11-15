<?php

function BuildXmlForYandex($XmlTag, $Document, $MainSqlQuery, $SysParams = array()) {
    global $CONF;
    $tmp = mysql_query($MainSqlQuery);
    $Result = null;
    while($Obj = mysql_fetch_object($tmp)) {

        $Element = $XmlTag->createElement( $SysParams['ElementTag'] );
        $Element->setAttribute( 'internal-id', $Obj->id );                  // <offer internal-id="1245">

        // Тип сделки («продажа», «аренда»).
        $Param = GetObjectParamByIdAndColumn($Obj->DealType, 'YandexMark');
        $Status = $XmlTag->createElement('type', $Param);
        $Element->appendChild($Status);

        // Тип недвижимости (рекомендуемое значение — «жилая»).
        $property = $XmlTag->createElement('property-type', 'жилая');
        $Element->appendChild($property);

        // Категория объекта («комната», «квартира», «дом», «участок», «flat», «room», «house», «cottage»,
        // «townhouse», «таунхаус», «часть дома», «house with lot», «дом с участком», «дача», «lot», «земельный участок»).
        // Сейчас принимаются объявления только о продаже и аренде жилой недвижимости: квартир, комнат, домов и участков.
        $category = GetObjectTypeByIdAndColumn($Obj->ObjectType, 'YandexMark');//
        $property = $XmlTag->createElement('category', $category);
        $Element->appendChild($property);
        //Для продажи и аренды комнат: количество комнат, участвующих в сделке.
        if( $category == 'комната' && $Obj->RoomsSell > 0) {
            $roomsoffered = $XmlTag->createElement('rooms-offered', $Obj->RoomsSell );
            $Element->appendChild($roomsoffered);
        }

        // TODO: URL страницы с объявлением.

        //Дата создания объявления (в формате YYYY-MM-DDTHH:mm:ss+00:00).
        $property = $XmlTag->createElement('creation-date', date('c'));
        $Element->appendChild($property);


        // Дата последнего обновления объявления (в формате YYYY-MM-DDTHH:mm:ss+00:00).
        $property = $XmlTag->createElement('last-update-date', date('c'));
        $Element->appendChild($property);

        //TODO: Дата и время, до которых объявление актуально (в формате YYYY-MM-DDTHH:mm:ss+00:00). //expire-date


        $location = $XmlTag->createElement('location');
        $country  = $XmlTag->createElement('country', 'Россия'); // TODO вынести в интерфейс?
        $region   = $XmlTag->createElement('region', $Obj->Region);

        /* Район указанного региона.
           Для России — название района субъекта РФ.
           Для Украины, Белорусии и Казахстана — название района области.*/
        $district       = $XmlTag->createElement('district', $Obj->Raion);
        $localityname   = $XmlTag->createElement('locality-name', $Obj->City);
        $address        = $XmlTag->createElement('address', $Obj->Street . ', ' . $Obj->HouseNumber);


        if(@$Obj->HighwayId > 0 && $Obj->RealtyType == 'country') {
            // Шоссе (только для Москвы).???????????
            $direction  = $XmlTag->createElement('direction', $Obj->HighwayId);
            $location->appendChild($direction);

            // Расстояние по шоссе до МКАД (указывается в километрах).
            if($Obj->Distance > 0) {
                $distance  = $XmlTag->createElement('distance', $Obj->Distance);
                $location->appendChild($distance);
            }
            //Географическая широта. долгота.
            if(@$Obj->latitude > 0 && $Obj->longitude > 0) {
                $latitude   = $XmlTag->createElement('latitude', $Obj->latitude);
                $location->appendChild($latitude);

                $longitude  = $XmlTag->createElement('longitude', $Obj->longitude);
                $location->appendChild($longitude);
            }
        }
        /* Для городской недвижимости обязательны следующие параметры:
             district (обязателен для городов, находящихся в областях субъектов РФ);
             locality-name (название населенного пункта);
             address (улица или улица и дом)
            или metro и time-on-foot (метро и время ходьбы до него).

          Для загородной недвижимости обязательны следующие поля:
             district (район субъекта РФ)
            или
            locality- name (название населенного пункта)
            или
            direction (шоссе — для Москвы)
            или
            railway-station (ближайшая ж/д станция).
        */
        $location->appendChild($country);
        $location->appendChild($region);
        $location->appendChild($district);
        $location->appendChild($localityname);
        $location->appendChild($address);

        /* Ближайшая станция метро (если таковых несколько, каждая должна быть указана в отдельном элементе).
                  name	Название станции метро.
                  time-on-transport	Время до метро в минутах на транспорте.
                  time-on-foot	Время до метро в минутах пешком.*/

        $MetroStations=0;

        if($Obj->MetroStation1Id > 0 && $Obj->MetroWayType && $Obj->MetroWayMinutes) {
            $MetroStations++;
            $metro = $XmlTag->createElement('metro');
            $name  = $XmlTag->createElement('name', GetMetroStationNameById($Obj->MetroStation1Id));
            $metro->appendChild($name);
            $name  = $XmlTag->createElement(GetObjectParamByIdAndColumn($Obj->MetroWayType, 'YandexMark'),
                $Obj->MetroWayMinutes);
            $metro->appendChild($name);
            $location->appendChild($metro);
        }
        if($Obj->Metro2StationId > 0 && $Obj->Metro2WayType && $Obj->Metro2WayMinutes) {
            $MetroStations++;
            $metro = $XmlTag->createElement('metro');
            $name  = $XmlTag->createElement('name', GetMetroStationNameById($Obj->Metro2StationId));
            $metro->appendChild($name);
            $name  = $XmlTag->createElement(GetObjectParamByIdAndColumn($Obj->Metro2WayType, 'YandexMark'),
                $Obj->Metro2WayMinutes);
            $metro->appendChild($name);
            $location->appendChild($metro);
        }
        if($Obj->Metro3StationId > 0 && $Obj->Metro3WayType && $Obj->Metro3WayMinutes) {
            $MetroStations++;
            $metro = $XmlTag->createElement('metro');
            $name  = $XmlTag->createElement('name', GetMetroStationNameById($Obj->Metro3StationId));
            $metro->appendChild($name);
            $name  = $XmlTag->createElement(GetObjectParamByIdAndColumn($Obj->Metro3WayType, 'YandexMark'),
                $Obj->Metro3WayMinutes);
            $metro->appendChild($name);
            $location->appendChild($metro);
        }
        if($Obj->Metro4StationId > 0 && $Obj->Metro4WayType && $Obj->Metro4WayMinutes) {
            $MetroStations++;
            $metro = $XmlTag->createElement('metro');
            $name  = $XmlTag->createElement('name', GetMetroStationNameById($Obj->Metro4StationId));
            $metro->appendChild($name);
            $name  = $XmlTag->createElement(GetObjectParamByIdAndColumn($Obj->Metro4WayType, 'YandexMark'),
                $Obj->Metro4WayMinutes);
            $metro->appendChild($name);
            $location->appendChild($metro);
        }

        // TODO добавить в интерфейс "ближайшая ж/д станция"

        $Element->appendChild($location);

        //Информация о продавце или арендодателе.////////////////////////////////////////
        $salesagent = $XmlTag->createElement('sales-agent');
        /* Контактный номер телефона (указывается в международном формате). Если номеров несколько, каждый из них необходимо передавать в отдельном элементе phone.
           Внимание! Для агентств недвижимости обязательно должны быть указаны прямые телефоны агентов. */
        $AgentPhonesArr = array($Obj->MobilePhone, $Obj->MobilePhone1, $Obj->MobilePhone2);

        $phone  = $XmlTag->createElement('phone', $AgentPhonesArr[$Obj->OwnerPhoneId]);
        $salesagent->appendChild($phone);
        if(isset($Obj->AddCorpPhone) && $Obj->AddCorpPhone > 0 && strlen($CONF['SysParams']['NavigatorXmlCompanyPhone']) >= 7) {
            // добавлять ли корпоративный номер
            $phone  = $XmlTag->createElement('phone', $CONF['SysParams']['NavigatorXmlCompanyPhone']);
            $salesagent->appendChild($phone);
        }
        $Element->appendChild($salesagent);

        $price = $XmlTag->createElement('price'); //Информация об условиях сделки
        $value = $XmlTag->createElement('value', $Obj->Price);
        $price->appendChild($value);

        $Param = GetObjectParamByIdAndColumn($Obj->Currency, 'YandexMark');
        $currency = $XmlTag->createElement('currency', $Param);
        $price->appendChild($currency);

        $Element->appendChild($price);

        // Ипотека (строго ограниченные значения — «да»/«нет», «true»/«false», «1»/«0»).
        if($Obj->Mortgage) {
            $Param = GetObjectParamByIdAndColumn($Obj->Mortgage, 'YandexMark');
            $mortgage = $XmlTag->createElement('mortgage', $Param);
            $Element->appendChild($mortgage);
        }

        // Информация об объекте
        $ImagesArr = GetImagesObjByObjectId( $Obj->id );
        // Фотография (может быть несколько тегов). Фотографии планировок следует передавать первым тегом image.
        foreach($ImagesArr as $ImgObj) {
            $image = $XmlTag->createElement('image', $CONF['MainSiteUrl'] . $ImgObj->FilePath);
            $Element->appendChild($image);
        }
        // Ремонт (рекомендуемые значения — «евро», «дизайнерский», «черновая отделка», «требует ремонта», «частичный ремонт», «с отделкой», «хороший»).
        $param = GetObjectParamByIdAndColumn($Obj->ObjectCondition, 'YandexMark');
        $quality = $XmlTag->createElement('quality', $param);
        $Element->appendChild($quality);

        //Дополнительная информация (описание в свободной форме, оставленное подателем объявления).
        $description = $XmlTag->createElement('description', ClearXmlText($Obj->Description) );
        $Element->appendChild($description);


        if($Obj->SquareLiving > 0) {
            // Жилая площадь (при продаже комнаты — площадь комнаты).
            $livingspace = $XmlTag->createElement('living-space'); //Информация об условиях сделки
            $value = $XmlTag->createElement('value', $Obj->SquareLiving);
            //$livingspace = $XmlTag->createElement('living-space', $Obj->SquareLiving);
            $livingspace->appendChild($value);
            $unit = $XmlTag->createElement('unit', 'кв. м');
            $livingspace->appendChild($unit);
            $Element->appendChild($livingspace);
        }

        if($Obj->SquareKitchen > 0) {
            // Площадь кухни.
            $kitchenspace = $XmlTag->createElement('kitchen-space');
            //$value->appendChild($kitchenspace);

            //$kitchenspace = $XmlTag->createElement('kitchen-space'); //Информация об условиях сделки
            $value = $XmlTag->createElement('value', $Obj->SquareKitchen);
            //$livingspace = $XmlTag->createElement('living-space', $Obj->SquareLiving);
            $kitchenspace->appendChild($value);
            $unit = $XmlTag->createElement('unit', 'кв. м');
            $kitchenspace->appendChild($unit);
            $Element->appendChild($kitchenspace);
        }

        //area - Общая площадь.
        if(@$Obj->SquareAll > 0) {
            $area = $XmlTag->createElement('area'); //Информация об условиях сделки
            $value = $XmlTag->createElement('value', $Obj->SquareAll);
            $area->appendChild($value);

            $unit = $XmlTag->createElement('unit', 'кв. м');
            $area->appendChild($unit);

            $Element->appendChild($area);
        }

        if( $Obj->RealtyType == 'country' && $Obj->LandSquare > 0) {
            //Площадь участка в случае предложения «дом с участком» или «участок».
            $lotarea = $XmlTag->createElement('lot-area');

            $value = $XmlTag->createElement('value', $Obj->LandSquare);
            $lotarea->appendChild($value);
            $unit = $XmlTag->createElement('unit', 'сот');
            $lotarea->appendChild($unit);

            $Element->appendChild($lotarea);

            if(isset($Obj->LandType)) {
                $lottype = $XmlTag->createElement('lot-type', GetObjectParamByIdAndColumn($Obj->LandType, 'YandexMark'));
                $Element->appendChild($lottype);
            }
        }

        // Устанавливается, если квартира продается в новостройке (строго ограниченные значения — «да», «true», «1», «+»).
        // Обязательный параметр для новостроек.
        if( $Obj->ObjectAgeType == 57) {
            $newflat = $XmlTag->createElement('new-flat', 'true' );
            $Element->appendChild($newflat);
        }
        //Общее количество комнат в квартире.
        if(@$Obj->RoomsCount > 0) {
            $rooms = $XmlTag->createElement('rooms', $Obj->RoomsCount);
            $Element->appendChild($rooms);
        }
        // Наличие телефона (строго ограниченные значения — «да»/ «нет», «true»/ «false», «1»/ «0», «+»/ «-»).
        if($Obj->Telephone) {
            $param = GetObjectParamByIdAndColumn($Obj->Telephone, 'YandexMark');
            $phone = $XmlTag->createElement('phone', $param );
            $Element->appendChild($phone);
        }

        // TODO Наличие интернета (строго ограниченные значения — «да»/«нет», «true»/«false», «1»/«0», «+»/«-»).

        // Тип балкона ( рекомендуемые значения — «балкон», «лоджия», «2 балкона», «2 лоджии»).
        if($Obj->Balcon) {
            $param = GetObjectParamByIdAndColumn($Obj->Balcon, 'YandexMark');
            $balcony = $XmlTag->createElement('balcony', $param );
            $Element->appendChild($balcony );
        }
        // Тип санузла (рекомендуемые значения — «совмещенный», «раздельный», «2»).
        if($Obj->Toilet) {
            $param = GetObjectParamByIdAndColumn($Obj->Toilet, 'YandexMark');
            $bathroomunit = $XmlTag->createElement('bathroom-unit', $param );
            $Element->appendChild($bathroomunit );
        }
        // Покрытие пола (рекомендуемые значения — «паркет», «ламинат», «ковролин», «линолеум»).
        if($Obj->Flooring) {
            $param = GetObjectParamByIdAndColumn($Obj->Flooring, 'YandexMark');
            $floorcovering = $XmlTag->createElement('floor-covering', $param );
            $Element->appendChild($floorcovering );
        }
        if($Obj->WindowView) {
            // Вид из окон (рекомендуемые значения — «во двор», «на улицу»).
            $param = GetObjectParamByIdAndColumn($Obj->WindowView, 'YandexMark');
            $window = $XmlTag->createElement('window-view', $param );
            $Element->appendChild($window );
        }

        // Этаж.
        if(@$Obj->Floor > 0) {
            $floor = $XmlTag->createElement('floor', $Obj->Floor);
            $Element->appendChild($floor);
        }
        // Описание здания ////////////////////////////////////////////////
        // Общее количество этажей в доме (обязательное поле для новостроек).
        if(@$Obj->Floors > 0) {
            $floors = $XmlTag->createElement('floors-total', $Obj->Floors);
            $Element->appendChild($floors);
        }

        // Тип дома (рекомендуемые значения — «кирпичный», «монолит», «панельный»).
        if(@$Obj->BuildingType > 0) {
            $param = GetObjectParamByIdAndColumn($Obj->BuildingType, 'YandexMark');
            $BuildingType = $XmlTag->createElement('building-type', $param);
            $Element->appendChild($BuildingType);
        }

        // Наличие лифта (строго ограниченные значения — «да»/«нет», «true»/«false», «1»/«0», «+»/«-»).
        if($Obj->Lift) {
            $param = GetObjectParamByIdAndColumn($Obj->Lift, 'YandexMark');
            $lift = $XmlTag->createElement('lift', $param );
            $Element->appendChild($lift );
        }

        // Наличие мусоропровода (строго ограниченные значения — «да»/«нет», «true»/«false», «1»/«0», «+»/«-»).
        if($Obj->Garbage) {
            $param = GetObjectParamByIdAndColumn($Obj->Garbage, 'YandexMark');
            $Garbage = $XmlTag->createElement('rubbish-chute', $param );
            $Element->appendChild($Garbage );
        }

        // Наличие парковки (строго ограниченные значения — «да»/«нет», «true»/«false», «1»/«0», «+»/«-»).
        if($Obj->Parking) {
            $param = GetObjectParamByIdAndColumn($Obj->Parking, 'YandexMark');
            $Parking = $XmlTag->createElement('parking', $param );
            $Element->appendChild($Parking );
        }

        // Описание загородной недвижимости////////////////////////////////////
        if( $Obj->RealtyType == 'country') {
            // Возможность ПМЖ (строго ограниченные значения — «да»/«нет», «true»/«false», «1»/«0», «+»/«-»).
            if($Obj->CountryPmg) {
                $param = GetObjectParamByIdAndColumn($Obj->CountryPmg, 'YandexMark');
                $Item = $XmlTag->createElement('pmg', $param );
                $Element->appendChild($Item );
            }
            // Расположение (возможные значения — «в доме», «на улице»).
            if($Obj->CountryToilet) {
                $param = GetObjectParamByIdAndColumn($Obj->CountryToilet, 'YandexMark');
                $Item = $XmlTag->createElement('toilet', $param );
                $Element->appendChild($Item );
            }
            //Наличие бассейна (строго ограниченные значения — «да»/«нет», «true»/ «false», «1»/«0», «+»/«-»).
            if($Obj->CountryPool) {
                $param = GetObjectParamByIdAndColumn($Obj->CountryPool, 'YandexMark');
                $Item = $XmlTag->createElement('pool', $param );
                $Element->appendChild($Item );
            }
            // Наличие сауны/бани (строго ограниченные значения — «да»/«нет», «true»/«false», «1»/«0», «+»/«-»).
            if($Obj->CountryBath) {
                $param = GetObjectParamByIdAndColumn($Obj->CountryBath, 'YandexMark');
                $Item = $XmlTag->createElement('sauna', $param );
                $Element->appendChild($Item );
            }
            //Наличие отопления (строго ограниченные значения — «да»/«нет», «true»/«false», «1»/«0», «+»/-).
            if($Obj->CountryHeat) {
                $param = GetObjectParamByIdAndColumn($Obj->CountryHeat, 'YandexMark');
                $Item = $XmlTag->createElement('heating-supply', $param );
                $Element->appendChild($Item );
            }
            // Наличие водопровода (строго ограниченные значения — «да»/«нет», « true»/«false», «1»/«0», «+»/«-»).
            if($Obj->CountryWater) {
                $param = GetObjectParamByIdAndColumn($Obj->CountryWater, 'YandexMark');
                $Item = $XmlTag->createElement('water-supply', $param );
                $Element->appendChild($Item );
            }
            // Канализация (строго ограниченные значения — «да»/«нет», «true»/«false», «1»/«0», «+»/«-»).
            if($Obj->CountrySewer) {
                $param = GetObjectParamByIdAndColumn($Obj->CountrySewer, 'YandexMark');
                $Item = $XmlTag->createElement('sewerage-supply', $param );
                $Element->appendChild($Item );
            }
            //Электроснабжение (строго ограниченные значения — «да»/«нет», «true»/«false», «1»/«0», «+»/«-»).
            if($Obj->CountryElectro) {
                $param = GetObjectParamByIdAndColumn($Obj->CountryElectro, 'YandexMark');
                $Item = $XmlTag->createElement('electricity-supply', $param );
                $Element->appendChild($Item );
            }
            //Подключение к газовым сетям (строго ограниченные значения — «да»/«нет», «true»/«false», «1»/«0», «+»/«-»).
            if($Obj->CountryGas) {
                $param = GetObjectParamByIdAndColumn($Obj->CountryGas, 'YandexMark');
                $Item = $XmlTag->createElement('gas-supply', $param );
                $Element->appendChild($Item );
            }
        }

        $Document->appendChild($Element);

    }

    return $Document;
}
