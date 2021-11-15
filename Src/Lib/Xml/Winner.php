<?php

    function BuildXmlForWinner($XmlTag, $Flats, $MainSqlQuery, $SysParams = array()) {
        global $CONF;
        $tmp = mysql_query($MainSqlQuery);
        $Result = null;
        while($Obj = mysql_fetch_object($tmp)) {

            $Element = $XmlTag->createElement( $SysParams['ElementTag'] );
            //$Element->setAttribute( 'internal-id', 123 );

            // Уникальный идентификатор объекта. Для одного и того же объекта значение данного элемента не должно изменяться.
            $id = $XmlTag->createElement('id', $Obj->id);
            $Element->appendChild($id);

            //Дата обновления состояния объекта, в формате 'DD.MM.YYYY', например: 12.02.2013.
            if(@$SysParams['Navigator']) {
                // для навигатора: в теге <дата>  должна быть актуальная дата (сегодняшняя)
                $date = $XmlTag->createElement('date', $Obj->TodayDate);
            } else {
                $date = $XmlTag->createElement('date', $Obj->WinnerAddedDate);
            }
            $Element->appendChild($date);

            //Актуальность объявления. "продается", "аванс", "продана"
            $actual = $XmlTag->createElement('actual', 'продается');
            $Element->appendChild($actual);

            // Тип объекта. "квартира", "комната"
            // TODO на будущее: в winnere меньше типов чем в базе, возможно нужно будет ограничивать
            $WinnerTypeName = GetObjectTypeByIdAndColumn($Obj->ObjectType, 'WinnerMark');
            $aptp = $XmlTag->createElement('aptp', $WinnerTypeName);
            $Element->appendChild($aptp);

            if(@$SysParams['RealtyType'] == 'country') { // только для загородки
                $optp = $XmlTag->createElement('optp', 'продажа');
                $Element->appendChild($optp);
            }

            if(@$SysParams['RealtyType'] == 'city' && $Obj->ObjectAgeType == 56) { // только для вторички
                //Статус, тип сделки, операции: "прямая продажа", "альтернатива"
                $Param = GetObjectParamByIdAndColumn($Obj->DealType, 'WinnerMark');
                $Status = $XmlTag->createElement('status', $Param);
                $Element->appendChild($Status);
            }

            if(@$SysParams['Doli']) {
                // специфично для долей
                // ! <slpartqt> Кол-во долей на продажу в квартире (min 1, max 999)
                $slpartqt = $XmlTag->createElement('slpartqt', $Obj->PartsSell);
                $Element->appendChild($slpartqt);

                // ! <tlpartqt> Всего долей в квартире (min 2, max 1000)
                $tlpartqt = $XmlTag->createElement('tlpartqt', $Obj->PartsTotal);
                $Element->appendChild($tlpartqt);

            }

            if($SysParams['RealtyType'] == 'city') { // только для вторички
                //Является ли объект новостройкой или нет.
                //"+" - объект является новостройкой
                //"-" - объект не является новостройкой - вторичная недвижимость
                $Param = GetObjectParamByIdAndColumn($Obj->ObjectAgeType, 'WinnerMark');
                $nova = $XmlTag->createElement('nova', $Param);
                $Element->appendChild($nova);
            }

            // Регион объекта. "Москва", "Московская обл."
            $area = $XmlTag->createElement('area', $Obj->Region);
            $Element->appendChild($area);

            if($SysParams['RealtyType'] == 'city') { // только для вторички
                //Район Московской области. Данный элемент обязателен и заполняется только для объектов по региону "Московская обл.".
                if($Obj->Region == 'Московская область') {
                    $locality = $XmlTag->createElement('locality', $Obj->Raion);
                    $Element->appendChild($locality);
                }
            }
            if($SysParams['RealtyType'] == 'country') { // только для загородки
                //Район Московской области. Данный элемент обязателен и заполняется только для объектов по региону "Московская обл.".
                if(@$Obj->Region == 'Московская область') {
                // Название населённого пункта (если в МО несколько нас.пунтков с одинаковым названием, указание района обязательно!)
                // (Для Москвы необходимо указать название нас.пункта в черте Москвы, либо саму Москву)
                // <locality>Васино д. (Чеховский р-н)</locality>
                // <locality>Солнцево д. (Чеховский р-н)</locality>
                //if($Obj->Region == 'Московская область') {
                    //$locality = $XmlTag->createElement('locality', $Obj->City. ' '. $Obj->PlaceType. ' ('.$Obj->Raion.')');
                    $locality = $XmlTag->createElement('locality', $Obj->Raion);
                    $Element->appendChild($locality);
                }

                // Населенный пункт Московской области с указанием типа поселения после названия.
                // Варианты сокращения для типа населенного пункта:
                // - "г." - город, - "д." - деревня, - "пос." - поселок, - "с." - село, - "пгт" - поселок городского типа
                // Пример заполнения элемента <town>:
                // <town>Гжель пос.</town>
                $town = $XmlTag->createElement('town', $Obj->WinnerCity);
                $Element->appendChild($town);


                //!	<highway> Название шоссе/Направление (для Москвы обязательный параметр!)
                // 		<highway>Рязанское ш.</highway>
                $highway = $XmlTag->createElement('highway', $Obj->DirectionName);
                $Element->appendChild($highway);

                // ! <otMKAD> Удаленность от МКАД в сотнях метров, если объект не находится внутри МКАДа
                // <otMKAD>250</otMKAD>
                $otMKAD = $XmlTag->createElement('otMKAD', $Obj->Distance * 10);
                $Element->appendChild($otMKAD);

            }


            if($SysParams['RealtyType'] == 'city') { // только для вторички
                // Населенный пункт в черте Москвы (Новой Москвы в том числе) и Московской области с указанием типа поселения после названия.
                // Варианты сокращения для типа населенного пункта:
                // - "г." - город, - "д." - деревня, - "пос." - поселок, - "с." - село, - "пгт" - поселок городского типа
                // Пример заполнения элемента <town>:
                // <town>Гжель пос.</town>
                $town = $XmlTag->createElement('town', $Obj->WinnerCity);
                $Element->appendChild($town);

                /* Список ближайших к объекту станций метро. Данный элемент обязателен и заполняется только для объектов по региону "Москва".
                В атрибутах элемента указываются параметры удаленности объекта от станции метро:
                 - атрибут "farval" - удаленность в минутах
                 - атрибут "fartp" - тип удаленности объекта, допустимые варианты: "п" - пешком, "т" - транспортом
                <metro_list>
                <metro farval="5" fartp="п">Сокол</metro>
                <metro farval="10" fartp="т">Октябрьское поле</metro>
                </metro_list>*/
                // TODO вместо отдельного запроса (название станции, тип удаленности) сделать один запрос? в CreateSQLQueryForXMLLoad() с учетом отсутствия
                //if($Obj->Region == 'Москва' && $Obj->MetroStation1Id > 0) { // && $Param
                if($Obj->Region == 'Москва') {
                    if($Obj->MetroStation1Id > 0 || $Obj->Metro2StationId > 0 || $Obj->Metro3StationId > 0 || $Obj->Metro4StationId > 0) { // для вторички показываем!
                        $metro_list = $XmlTag->createElement('metro_list');
                        if($Obj->MetroStation1Id > 0) {
                            $Param = GetObjectParamByIdAndColumn($Obj->MetroWayType, 'WinnerMark');
                            $metro  = $XmlTag->createElement('metro', GetMetroStationNameById($Obj->MetroStation1Id));
                            $metro->setAttribute('farval', $Obj->MetroWayMinutes);
                            $metro->setAttribute('fartp', $Param);
                            $metro_list->appendChild($metro);
                        }
                        if($Obj->Metro2StationId > 0) {
                            $Param = GetObjectParamByIdAndColumn($Obj->Metro2WayType, 'WinnerMark');
                            $metro = $XmlTag->createElement('metro', GetMetroStationNameById($Obj->Metro2StationId));
                            $metro->setAttribute('farval', $Obj->Metro2WayMinutes);
                            $metro->setAttribute('fartp', $Param);
                            $metro_list->appendChild($metro);
                        }
                        if($Obj->Metro3StationId > 0) {
                            $Param = GetObjectParamByIdAndColumn($Obj->Metro3WayType, 'WinnerMark');
                            $metro = $XmlTag->createElement('metro', GetMetroStationNameById($Obj->Metro3StationId));
                            $metro->setAttribute('farval', $Obj->Metro3WayMinutes);
                            $metro->setAttribute('fartp', $Param);
                            $metro_list->appendChild($metro);
                        }
                        if($Obj->Metro4StationId > 0) {
                            $Param = GetObjectParamByIdAndColumn($Obj->Metro4WayType, 'WinnerMark');
                            $metro = $XmlTag->createElement('metro', GetMetroStationNameById($Obj->Metro4StationId));
                            $metro->setAttribute('farval', $Obj->Metro4WayMinutes);
                            $metro->setAttribute('fartp', $Param);
                            $metro_list->appendChild($metro);
                        }
                        $Element->appendChild($metro_list);
                    }
                }
            }
            //Улица с указанием ее типа после названия: ул., бул., проезд и т.д.
            //Данный элемент обязателен только для объектов по Москве,
            //для объектов по региону "Московская обл." его заполнение желательно, но не обязательно.
            //Если в населенном пункте нет улиц, то тег <address> следует оставить пустым.
            // ЗАГОРОДКА: ! <address> Название улицы (обязательный параметр только для городов)
            $address = $XmlTag->createElement('address', $Obj->Street);
            $Element->appendChild($address);


            //Номер дома, корпуса. Данный элемент обязателен только для объектов по региону "Москва", для объектов по региону
            //"Московская обл." его заполнение желательно, но не обязательно.
            $dom = $XmlTag->createElement('dom', $Obj->HouseNumber);
            $Element->appendChild($dom);

            //В значении данного элемента указывается числовое значение стоимости объекта.
            //В атрибуте "currency" элемента <price> указывается тип валюты, в которой указана стоимость объекта. Допустимые варианты
            //значения атрибута "currency":
            //"RUB" - российские рубли
            //"USD" - доллары США
            //"EUR" - евро
            $Param = GetObjectParamByIdAndColumn($Obj->Currency, 'WinnerMark');
            $price = $XmlTag->createElement('price', $Obj->Price);
            $price->setAttribute('currency', $Param);
            $Element->appendChild($price);

            if($SysParams['RealtyType'] == 'country') {
                /*<prc_type> тип цены (для объектов, имеющих тип операции <optp> = продажа):
                                За всю площадь (вне зависимости от типа объекта <aptp> )
                                За сотку ( для объектов, имеющих тип объекта <aptp> = участок)
                                За кв.м. (для всех, КРОМЕ типа объекта <aptp> = участок)*/
                $Param = GetObjectParamByIdAndColumn($Obj->PriceTypeId, 'WinnerMark');
                if($Obj->PriceTypeId > 0 && $Param) {
                    $Element->appendChild( $XmlTag->createElement( 'prc_type', $Param ) );
                }
            }

            //Количество комнат в квартире. Значение элемента <flats> должно быть больше значения, указанного в элементе <rooms>.
            $FlatsCount = $XmlTag->createElement('flats', $Obj->RoomsCount);
            $Element->appendChild($FlatsCount);
            $roomsCount = 0;
            if($SysParams['RealtyType'] == 'city') { // только для вторички
                //Количество продаваемых комнат в квартире. Обязательный элемент только если тип объекта "комната".
                if ($Obj->ObjectType == 1) {
                    /*квартира*/
                    $roomsCount = $Obj->RoomsCount;
                } elseif ($Obj->ObjectType == 3) {
                    /*комната*/
                    $roomsCount = $Obj->RoomsSell;
                } else {
                    // других типов в этой выгрузке не должно быть
                    //echo "type fatal error";exit;
                }
                if ($roomsCount > 0) {
                    $rooms = $XmlTag->createElement('rooms', $roomsCount);
                    $Element->appendChild($rooms);
                }

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
                $Element->appendChild($sq);
            }
            if($SysParams['RealtyType'] == 'country') {
            // !	<sq pl="площадь дома"(в м2) pl_s="площадь участка"(в сотках)></sq>
                $PlOk = 0;
                $sq = $XmlTag->createElement('sq', ' ');
                if($Obj->SquareLiving > 0) {
                    $sq->setAttribute('pl', $Obj->SquareLiving); $PlOk++;
                }
                if($Obj->LandSquare > 0) {
                    $sq->setAttribute('pl_s', $Obj->LandSquare); $PlOk++;
                }
                if($PlOk) {
                    $Element->appendChild($sq);
                }
            }

            if($SysParams['RealtyType'] == 'city') { // только для вторички
                //Этаж, на котором расположен объект.
                $floor = $XmlTag->createElement('floor', $Obj->Floor);
                $Element->appendChild($floor);

                //Этажность строения, в котором расположен объект.
                $fl_ob = $XmlTag->createElement('fl_ob', $Obj->Floors);
                $Element->appendChild($fl_ob);

                // <house_series> серия дома: для новостроек
                if($Obj->ObjectAgeType == 57 && @$Obj->BuildingSeriesId > 0) {
                    $Param = GetObjectParamByIdAndColumn($Obj->BuildingSeriesId, 'WinnerMark');
                    if ( $Param ) {
                        $ser = $XmlTag->createElement('house_series', $Param);
                        $Element->appendChild($ser);
                    }
                }

                //Тип дома.
                //"М" - монолитный
                //"П" - панельный  //"К" - кирпичный
                //"Б" - блочный    //"С" - сталинский
                //"Э" - элитный     "МК" - монолитно-кирпичный
                //"БП" - блочно-панельный
                $Param = GetObjectParamByIdAndColumn($Obj->BuildingType, 'WinnerMark');
                if($Obj->BuildingType > 0 && $Param) {
                    $tip = $XmlTag->createElement( 'tip', $Param );
                    $Element->appendChild($tip);
                }

                /*Тип лифта.
                  Допустимые варианты:
                  "без лифта"  "лифт" "лифт пассажирский"
                  "лифт грузовой" "лифт пассажирский и лифт грузовой"*/
                $Param = GetObjectParamByIdAndColumn($Obj->Lift, 'WinnerMark');
                if($Obj->Lift > 0 && $Param) {
                    $lift = $XmlTag->createElement('lift', $Param );
                    $Element->appendChild($lift);
                }
                /*Тип мусоропровода.
                Допустимые варианты:
                "без мусоропровода"
                "мусоропровод"*/
                $Param = GetObjectParamByIdAndColumn($Obj->Garbage, 'WinnerMark');
                if($Obj->Garbage > 0 && $Param) {
                    $musor = $XmlTag->createElement('musor', $Param );
                    $Element->appendChild($musor);
                }


                /* Наличие балконов, лоджий.
                   "-" - балкон/лоджия отсутствует, "Б" - один балкон
                   "Л" - одна лоджия, "Эрк" - эркер
                   "ЭркЛ" - эркер и лоджия, "БЛ" - балкон и лоджия
                   "2Б" - два балкона, "2Л" - две лоджии
                   "3Б" - три балкона, "3Л" - три лоджии
                   "4Л" - четыре и более лоджий, "Б2Л" - балкон и две лоджии
                   "2Б2Л" - бва балкона и две лоджии
                */
                $Param = GetObjectParamByIdAndColumn($Obj->Balcon, 'WinnerMark');
                if($Obj->Balcon > 0 && $Param) {
                    $balkon = $XmlTag->createElement('balkon', $Param );
                    $Element->appendChild($balkon);
                }
                /*Санузел.
                  "-" - нет санузла, "+" - есть санузел
                  "2" - два санузла, "3" - три санузла
                  "4" - четыре санузла, "С" - совмещенный санузел
                  "Р" - раздельный санузел, "2С" - два совмещенных санузла
                  "2Р" - два раздельных санузла, "3С" - три совмещенных санузла
                  "3Р" - три раздельных санузла, "4С" - четыре и более совмещенных санузла
                  "4Р" - четыре и более раздельных санузла*/
                // TODO не все типы есть в базе
                $Param = GetObjectParamByIdAndColumn($Obj->Toilet, 'WinnerMark');
                if($Obj->Toilet > 0 && $Param) {
                    $san = $XmlTag->createElement('san', $Param );
                    $Element->appendChild($san);
                }

                /*Тип окон.
                   "окна на улицу"
                   "окна во двор"
                   "окна во двор и на улицу"*/
                $Param = GetObjectParamByIdAndColumn($Obj->WindowView, 'WinnerMark');
                if($Obj->WindowView > 0 && $Param) {
                    $okna = $XmlTag->createElement('okna', $Param );
                    $Element->appendChild($okna);
                }

                /*
                * Тип состояния (ремонта) объекта.
                   "требуется капитальный ремонт", "плохое состояние"
                   "без отделки", "требуется ремонт"
                   "среднее состояние", "хорошее состояние"
                   "сделан ремонт", "отличное состояние"
                   "евроремонт", "эксклюзивный евроремонт"
                   "первичная отделка"
                */
                $Param = GetObjectParamByIdAndColumn($Obj->ObjectCondition, 'WinnerMark');
                if($Obj->ObjectCondition > 0 && $Param) {
                    $remont = $XmlTag->createElement('remont', $Param );
                    $Element->appendChild($remont);
                }

                /*
                * Наличие телефонной линии.
                   "-" - нет телефонной линии
                   "Т" - есть телефонная линия
                   "2Т" - две и более телефонные линии
                */
                $Param = GetObjectParamByIdAndColumn($Obj->Telephone, 'WinnerMark');
                if($Obj->Telephone > 0 && $Param) {
                    $tel = $XmlTag->createElement('tel', $Param );
                    $Element->appendChild($tel);
                }
            }

            if($SysParams['RealtyType'] == 'country') {
                /*<water> водоснабжение:
					нет,центральный,скважина,колодец
					магистральный,иное,есть,летний*/
                $Param = GetObjectParamByIdAndColumn($Obj->CountryWater, 'WinnerMark');
                if($Obj->CountryWater > 0 && $Param) {
                    $Element->appendChild( $XmlTag->createElement( 'water', $Param ) );
                }
                /* <gas> газификация: нет магистральный по границе перспектива рядом баллоны есть иное  центральный*/
                $Param = GetObjectParamByIdAndColumn($Obj->CountryGas, 'WinnerMark');
                if($Obj->CountryGas > 0 && $Param) {
                    $Element->appendChild( $XmlTag->createElement( 'gas', $Param ) );
                }

                /* <sewer> канализация: нет есть вне дома септик центральная иное */
                $Param = GetObjectParamByIdAndColumn($Obj->CountrySewer, 'WinnerMark');
                if($Obj->CountrySewer > 0 && $Param) {
                    $Element->appendChild( $XmlTag->createElement( 'sewer', $Param ) );
                }

                /* <heat> отопление: нет центральное электрокотел газовый котел жидкотопливный котел АГВ печь есть иное */
                $Param = GetObjectParamByIdAndColumn($Obj->CountryHeat, 'WinnerMark');
                if($Obj->CountryHeat > 0 && $Param) {
                    $Element->appendChild( $XmlTag->createElement( 'heat', $Param ) );
                }

                /* <electro> электроснабжение: нет есть 220 В 380 В перспектива по границе 10 КВт  иное */
                $Param = GetObjectParamByIdAndColumn($Obj->CountryElectro, 'WinnerMark');
                if($Obj->CountryElectro > 0 && $Param) {
                    $Element->appendChild( $XmlTag->createElement( 'electro', $Param ) );
                }

                /* <pmg> ПМЖ: + - */
                $Param = GetObjectParamByIdAndColumn($Obj->CountryPmg, 'WinnerMark');
                if($Obj->CountryPmg > 0 && $Param) {
                    $Element->appendChild( $XmlTag->createElement( 'pmg', $Param ) );
                }

                /* <ohrana> охрана: + - */
                $Param = GetObjectParamByIdAndColumn($Obj->CountrySecure, 'WinnerMark');
                if($Obj->CountrySecure > 0 && $Param) {
                    $Element->appendChild( $XmlTag->createElement( 'ohrana', $Param ) );
                }

                /*! <rent_term> срок аренды (для объектов, имеющих тип операции <optp> = аренда) Любой срок Длительный срок Посуточно
                                От месяца и более Сезонная сдача*/

            }

            if(@$SysParams['Navigator']) {
                // Навиг требует добавлять второй номер компании
                $CompanyPhone = ';' . $CONF['SysParams']['NavigatorXmlCompanyPhone'];
            } else {
                if(isset($Obj->AddCorpPhone) && $Obj->AddCorpPhone > 0 && strlen($CONF['SysParams']['NavigatorXmlCompanyPhone']) >= 7) {
                    // добавлять ли корпоративный номер
                    $CompanyPhone = ';'. $CONF['SysParams']['NavigatorXmlCompanyPhone'];
                } else {
                    $CompanyPhone = '';
                }
            }
            //Номера телефонов для контактов в формате "8xxxxxxxxxx" или "7xxxxxxxxxx". В списке значения разделяются символом ";"
            $AgentPhonesArr = array($Obj->MobilePhone, $Obj->MobilePhone1, $Obj->MobilePhone2);
            $telefon = $XmlTag->createElement('telefon', $AgentPhonesArr[$Obj->OwnerPhoneId] . $CompanyPhone);
            $Element->appendChild( $telefon );


            //Список URL фотографий объекта. В списке значения разделяются символом ";".
            $ImgList = '';
            $ImagesArr = GetImagesObjByObjectId( $Obj->id );
            foreach($ImagesArr as $ImgObj) {
                $ImgList .= $CONF['MainSiteUrl'] . $ImgObj->FilePath.';';
            }
            if(strlen($ImgList) > 1) {
                $photos = $XmlTag->createElement('photos', $ImgList);
                $Element->appendChild($photos);
            }

            //Дополнительная информация об объекте. Значение данного элемента необходимо указывать в секции <![CDATA[...]]>.
            if(strlen($Obj->Description) >= 1) {
                if(@$SysParams['EnableCorpSite']) {
                    $text  = htmlspecialchars($Obj->Description); // для сайта оставляем символы табов и новой строки
                } else {
                    $text = ClearXmlText($Obj->Description);
                }
                $remark = $XmlTag->createElement('remark');
                $text = $XmlTag->createCDATASection($text);
                $remark->appendChild($text);
                $Element->appendChild($remark);
            }

            if($SysParams['RealtyType'] == 'city') {
                /* Если элемент winner_only = 0 (или отсутствует), то ваш объект будет передан на открытые интернет-площадки:
               - Sob.ru,- Realty.yandex.ru,- Realty.mail.ru,- Mirkvartir.ru, и др. */
                $winner_only = $XmlTag->createElement('winner_only', '0');
                $Element->appendChild($winner_only);
            }

            if(@$SysParams['EnableSiteTags']) {
                $SiteTitle = $XmlTag->createElement('SiteTitle', $Obj->SiteTitle);
                $Element->appendChild($SiteTitle);
                $SiteKeywords = $XmlTag->createElement('SiteKeywords', $Obj->SiteKeywords);
                $Element->appendChild($SiteKeywords);
                $SiteDescription = $XmlTag->createElement('SiteDescription', $Obj->SiteDescription);
                $Element->appendChild($SiteDescription);
                //$SiteVideo = $XmlTag->createElement('SiteVideo', $Obj->SiteVideo);
                //$Element->appendChild($SiteVideo);
                $Element -> appendChild($XmlTag->createElement('SiteVideo'))    ->
                            appendChild($XmlTag->createTextNode($Obj->SiteVideo));
            }
            /*
            $ = $XmlTag->createElement('', $Obj->);
            $Element->appendChild($);
            */

            $Flats->appendChild($Element);
        }

        return $Flats;
    }
