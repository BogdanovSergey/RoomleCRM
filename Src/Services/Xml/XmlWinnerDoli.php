<?php

    require(dirname(__FILE__) . '/../../Conf/Config.php');
    require(dirname(__FILE__) . '/../../Lib/Xml/Xml.php');
    require(dirname(__FILE__) . '/../../Lib/Xml/Winner.php');

    DBConnect();

    $XmlTag = new DOMDocument('1.0', 'UTF-8');
    // создаем корневой элемент
    $Flats = $XmlTag->createElement('flats');
    $Flats->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
    $Flats->setAttribute('xsi:noNamespaceSchemaLocation', 'flats.xsd');

    $MainSqlQuery  = CreateSQLQueryForXMLLoad('WinnerDoli');

    $SysParams['Doli']          = true;
    $SysParams['RealtyType']    = 'city';
    $SysParams['ElementTag']    = 'flat';
    $Flats = BuildXmlForWinner($XmlTag, $Flats, $MainSqlQuery, $SysParams);

    $XmlTag->appendChild($Flats);

    $out = preg_replace('#</flat>#', "</flat>\n\n", $XmlTag->saveXML() );   // пробелы для читаемости
    $out = preg_replace('/></', ">\n<", $out );
    echo $out;



/*
    function BuildXmlForWinner($XmlTag, $Flats, $MainSqlQuery) {
        global $CONF;
        $tmp = mysql_query($MainSqlQuery);
        $Result = null;
        while($Obj = mysql_fetch_object($tmp)) {

            $Flat = $XmlTag->createElement('flat');

            // Уникальный идентификатор объекта. Для одного и того же объекта значение данного элемента не должно изменяться.
            $id = $XmlTag->createElement('id', $Obj->id);
            $Flat->appendChild($id);

            //Дата обновления состояния объекта, в формате 'DD.MM.YYYY', например: 12.02.2013.
            $date = $XmlTag->createElement('date', $Obj->WinnerAddedDate);
            $Flat->appendChild($date);

            //Актуальность объявления. "продается", "аванс", "продана"
            $actual = $XmlTag->createElement('actual', 'продается');
            $Flat->appendChild($actual);

            // Тип объекта. "квартира", "комната"
            // TODO на будущее: в winnere меньше типов чем в базе, возможно нужно будет ограничивать
            $aptp = $XmlTag->createElement('aptp', $Obj->TypeName);
            $Flat->appendChild($aptp);

            //Является ли объект новостройкой или нет.
            //"+" - объект является новостройкой
            //"-" - объект не является новостройкой - вторичная недвижимость
            $Param = GetObjectParamByIdAndColumn($Obj->ObjectAgeType, 'WinnerMark');
            $nova = $XmlTag->createElement('nova', $Param);
            $Flat->appendChild($nova);


            // Регион объекта. "Москва", "Московская обл."
            $area = $XmlTag->createElement('area', $Obj->Region);
            $Flat->appendChild($area);

            //Район Московской области. Данный элемент обязателен и заполняется только для объектов по региону "Московская обл.".
            if($Obj->Region == 'Московская область') {
                $locality = $XmlTag->createElement('locality', $Obj->Raion);
                $Flat->appendChild($locality);
            }
            // Населенный пункт в черте Москвы (Новой Москвы в том числе) и Московской области с указанием типа поселения после названия.
            // Варианты сокращения для типа населенного пункта:
            // - "г." - город, - "д." - деревня, - "пос." - поселок, - "с." - село, - "пгт" - поселок городского типа
            // Пример заполнения элемента <town>:
            // <town>Гжель пос.</town>
            $town = $XmlTag->createElement('town', $Obj->WinnerCity);
            $Flat->appendChild($town);

            / * Список ближайших к объекту станций метро. Данный элемент обязателен и заполняется только для объектов по региону "Москва".
            В атрибутах элемента указываются параметры удаленности объекта от станции метро:
             - атрибут "farval" - удаленность в минутах
             - атрибут "fartp" - тип удаленности объекта, допустимые варианты: "п" - пешком, "т" - транспортом
            <metro_list>
            <metro farval="5" fartp="п">Сокол</metro>
            <metro farval="10" fartp="т">Октябрьское поле</metro>
            </metro_list>* /
            // TODO доделать выбор нескольких станций метро
            // TODO вместо отдельного запроса (название станции, тип удаленности) сделать один запрос в CreateSQLQueryForXMLLoad() с учетом отсутствия
            $Param = GetObjectParamByIdAndColumn($Obj->MetroWayType, 'WinnerMark');
            if($Obj->Region == 'Москва' && $Obj->MetroStation1Id > 0) { // && $Param
                $metro_list = $XmlTag->createElement('metro_list');
                $metro  = $XmlTag->createElement('metro', GetMetroStationNameById($Obj->MetroStation1Id));
                $metro->setAttribute('farval', $Obj->MetroWayMinutes);
                $metro->setAttribute('fartp', $Param);
                $metro_list->appendChild($metro);
                $Flat->appendChild($metro_list);
            }

            / * TODO в долях другой тег метро???
            !	<metro  станция метро (если area = Москва) или нас.пункт (Подмосковье)
                farval="удалённость от станции в минутах"
                fartp="тип удалённости(пешком или транспортом)"
                    п
                    т* /

            //Улица с указанием ее типа после названия: ул., бул., проезд и т.д.
            //Данный элемент обязателен только для объектов по Москве,
            //для объектов по региону "Московская обл." его заполнение желательно, но не обязательно.
            //Если в населенном пункте нет улиц, то тег <address> следует оставить пустым.
            $address = $XmlTag->createElement('address', $Obj->Street);
            $Flat->appendChild($address);


            //Номер дома, корпуса. Данный элемент обязателен только для объектов по региону "Москва", для объектов по региону
            //"Московская обл." его заполнение желательно, но не обязательно.
            $dom = $XmlTag->createElement('dom', $Obj->HouseNumber);
            $Flat->appendChild($dom);

            //В значении данного элемента указывается числовое значение стоимости объекта.
            //В атрибуте "currency" элемента <price> указывается тип валюты, в которой указана стоимость объекта. Допустимые варианты
            //значения атрибута "currency":
            //"RUB" - российские рубли
            //"USD" - доллары США
            //"EUR" - евро
            $Param = GetObjectParamByIdAndColumn($Obj->Currency, 'WinnerMark');
            $price = $XmlTag->createElement('price', $Obj->Price);
            $price->setAttribute('currency', $Param);
            $Flat->appendChild($price);

            //Количество комнат в квартире. Значение элемента <flats> должно быть больше значения, указанного в элементе <rooms>.
            $flats = $XmlTag->createElement('flats', $Obj->RoomsCount);
            $Flat->appendChild($flats);

            // ! <slpartqt> Кол-во долей на продажу в квартире (min 1, max 999)
            $slpartqt = $XmlTag->createElement('slpartqt', $Obj->PartsSell);
            $Flat->appendChild($slpartqt);

            // ! <tlpartqt> Всего долей в квартире (min 2, max 1000)
            $tlpartqt = $XmlTag->createElement('tlpartqt', $Obj->PartsTotal);
            $Flat->appendChild($tlpartqt);


            / * /Количество продаваемых комнат в квартире. Обязательный элемент только если тип объекта "комната".
            if($Obj->ObjectType == 1) {
                //квартира
                $roomsCount = $Obj->RoomsCount;
            } elseif($Obj->ObjectType == 3) {
                //комната
                $roomsCount = $Obj->RoomsSell;
            } else {
                // других типов в этой выгрузке не должно быть
                echo "type fatal error";exit;
            }
            $rooms = $XmlTag->createElement('rooms', $roomsCount);
            $Flat->appendChild($rooms);* /

            //Площадь объекта. В атрибутах элемента <sq> указываются:
            //- "pl_ob" - всегда общая площадь всей квартиры
            //- "pl" - жилая площадь, если тип объекта "квартира"  или площадь продаваемой комнаты, если тип объекта "комната"
            //- "kitch" - площадь кухни
            //- "pl_r" - разбивка площади по комнатам
            //Пример заполнения элемента <sq>:
            //<sq pl_ob="78.5" pl="48.3" kitch="12.5" pl_r="18+15.3+15"></sq>
            // TODO а нужна ли pl_r ?
            $sq = $XmlTag->createElement('sq', ' ');
            $sq->setAttribute('pl_ob', $Obj->SquareAll);
            $sq->setAttribute('pl', $Obj->SquareLiving);
            $sq->setAttribute('kitch', $Obj->SquareKitchen);
            $Flat->appendChild($sq);


            //Этаж, на котором расположен объект.
            $floor = $XmlTag->createElement('floor', $Obj->Floor);
            $Flat->appendChild($floor);

            //Этажность строения, в котором расположен объект.
            $fl_ob = $XmlTag->createElement('fl_ob', $Obj->Floors);
            $Flat->appendChild($fl_ob);

            //Тип дома.
            //"М" - монолитный
            //"П" - панельный  //"К" - кирпичный
            //"Б" - блочный    //"С" - сталинский
            //"Э" - элитный     "МК" - монолитно-кирпичный
            //"БП" - блочно-панельный
            $Param = GetObjectParamByIdAndColumn($Obj->BuildingType, 'WinnerMark');
            if($Obj->BuildingType > 0 && $Param) {
                $tip = $XmlTag->createElement( 'tip', $Param );
                $Flat->appendChild($tip);
            }

            / *Тип лифта.
            Допустимые варианты:
            "без лифта"  "лифт" "лифт пассажирский"
            "лифт грузовой" "лифт пассажирский и лифт грузовой"* /
            $Param = GetObjectParamByIdAndColumn($Obj->Lift, 'WinnerMark');
            if($Obj->Lift > 0 && $Param) {
                $lift = $XmlTag->createElement('lift', $Param );
                $Flat->appendChild($lift);
            }
            / *Тип мусоропровода.
            Допустимые варианты:
            "без мусоропровода"
            "мусоропровод"* /
            $Param = GetObjectParamByIdAndColumn($Obj->Garbage, 'WinnerMark');
            if($Obj->Garbage > 0 && $Param) {
                $musor = $XmlTag->createElement('musor', $Param );
                $Flat->appendChild($musor);
            }


             / * Наличие балконов, лоджий.
                "-" - балкон/лоджия отсутствует, "Б" - один балкон
                "Л" - одна лоджия, "Эрк" - эркер
                "ЭркЛ" - эркер и лоджия, "БЛ" - балкон и лоджия
                "2Б" - два балкона, "2Л" - две лоджии
                "3Б" - три балкона, "3Л" - три лоджии
                "4Л" - четыре и более лоджий, "Б2Л" - балкон и две лоджии
                "2Б2Л" - бва балкона и две лоджии
             * /
            $Param = GetObjectParamByIdAndColumn($Obj->Balcon, 'WinnerMark');
            if($Obj->Balcon > 0 && $Param) {
                $balkon = $XmlTag->createElement('balkon', $Param );
                $Flat->appendChild($balkon);
            }
            / *Санузел.
            "-" - нет санузла, "+" - есть санузел
            "2" - два санузла, "3" - три санузла
            "4" - четыре санузла, "С" - совмещенный санузел
            "Р" - раздельный санузел, "2С" - два совмещенных санузла
            "2Р" - два раздельных санузла, "3С" - три совмещенных санузла
            "3Р" - три раздельных санузла, "4С" - четыре и более совмещенных санузла
            "4Р" - четыре и более раздельных санузла* /
            // TODO не все типы есть в базе
            $Param = GetObjectParamByIdAndColumn($Obj->Toilet, 'WinnerMark');
            if($Obj->Toilet > 0 && $Param) {
                $san = $XmlTag->createElement('san', $Param );
                $Flat->appendChild($san);
            }

            / *Тип окон.
            "окна на улицу"
            "окна во двор"
            "окна во двор и на улицу"* /
            $Param = GetObjectParamByIdAndColumn($Obj->WindowView, 'WinnerMark');
            if($Obj->WindowView > 0 && $Param) {
                $okna = $XmlTag->createElement('okna', $Param );
                $Flat->appendChild($okna);
            }

            / *
             * Тип состояния (ремонта) объекта.
            "требуется капитальный ремонт", "плохое состояние"
            "без отделки", "требуется ремонт"
            "среднее состояние", "хорошее состояние"
            "сделан ремонт", "отличное состояние"
            "евроремонт", "эксклюзивный евроремонт"
            "первичная отделка"
             * /
            $Param = GetObjectParamByIdAndColumn($Obj->ObjectCondition, 'WinnerMark');
            if($Obj->ObjectCondition > 0 && $Param) {
                $remont = $XmlTag->createElement('remont', $Param );
                $Flat->appendChild($remont);
            }

            / *
             * Наличие телефонной линии.
            "-" - нет телефонной линии
            "Т" - есть телефонная линия
            "2Т" - две и более телефонные линии
             * /
            $Param = GetObjectParamByIdAndColumn($Obj->Telephone, 'WinnerMark');
            if($Obj->Telephone > 0 && $Param) {
                $tel = $XmlTag->createElement('tel', $Param );
                $Flat->appendChild($tel);
            }

            //Номера телефонов для контактов в формате "8xxxxxxxxxx" или "7xxxxxxxxxx". В списке значения разделяются символом ";"
            $telefon = $XmlTag->createElement('telefon', $Obj->AgentPhone);
            $Flat->appendChild($telefon);


            //Список URL фотографий объекта. В списке значения разделяются символом ";".
            $ImgList = '';
            $ImagesArr = GetImagesObjByObjectId( $Obj->id );
            foreach($ImagesArr as $ImgObj) {
                $ImgList .= $CONF['MainSiteUrl'] . $ImgObj->FilePath.';';
            }
            if(strlen($ImgList) > 1) {
                $photos = $XmlTag->createElement('photos', $ImgList);
                $Flat->appendChild($photos);
            }

            //Дополнительная информация об объекте. Значение данного элемента необходимо указывать в секции <![CDATA[...]]>.
            if(strlen($Obj->Description) >= 1) {
                $text = ClearXmlText($Obj->Description);
                $remark = $XmlTag->createElement('remark');
                $text = $XmlTag->createCDATASection($text);
                $remark->appendChild($text);
                $Flat->appendChild($remark);
            }

            / * Если элемент winner_only = 0 (или отсутствует), то ваш объект будет передан на открытые интернет-площадки:
            - Sob.ru,- Realty.yandex.ru,- Realty.mail.ru,- Mirkvartir.ru, и др.
             * /
            $winner_only = $XmlTag->createElement('winner_only', '0');
            $Flat->appendChild($winner_only);

            //Статус, тип сделки, операции: "прямая продажа", "альтернатива"
            $Param = GetObjectParamByIdAndColumn($Obj->DealType, 'WinnerMark');
            $Status = $XmlTag->createElement('status', $Param);
            $Flat->appendChild($Status);



            / *
            $ = $XmlTag->createElement('', $Obj->);
            $Flat->appendChild($);
            * /

            $Flats->appendChild($Flat);
        }

        return $Flats;
    }
*/