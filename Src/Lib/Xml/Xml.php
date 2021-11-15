<?php

    function CreateSQLQueryForXMLLoad($BaseName, $SysParams = array()) {
        // TODO при внесении объекта некоторые поля м.б. = NULL (загородка), тогда они не будут выбираться, добавить IFNULL?
        if($BaseName == 'WinnerFlats') {
            // Запрос берет только квартиры и комнаты, без долей
            if(@$SysParams['EnableCorpSite'] == true) {
                // включить "winner совместимую" выгрузку для корпоративного сайта
                $out = "
                    SELECT
                        o.*,
                        o.id AS id,
                        DATE_FORMAT(o.AddedDate,'%d.%m.%Y') AS WinnerAddedDate,
                        ot.TypeName AS TypeName,
                        CONCAT_WS('', o.City, ' ', o.PlaceTypeSocr) AS WinnerCity,
                        u.MobilePhone AS MobilePhone,
                        u.MobilePhone1 AS MobilePhone1,
                        u.MobilePhone2 AS MobilePhone2
                    FROM
                        Objects AS o,
                        ObjectTypes AS ot,
                        Users AS u,
                        AdPortalObjects AS apo,
                        BillAdTarifs AS bat
                    WHERE
                        o.RealtyType   = 'city' AND # городская
                        # городская вторичка и новостройки. Без выделения новостроек: (o.ObjectAgeType= 56 OR o.ObjectAgeType= 57)
                        o.Active      = 1     AND
                        o.Advertize   = 1     AND
                        o.ObjectType  = ot.id AND
                        o.OwnerUserId = u.id  AND
                        o.id = apo.ObjectId   AND   # берем только объекты указанные в AdPortalObjects
                        bat.TarifShortName = 'TrfAnSiteFree' AND
                        apo.TarifId  = bat.id AND  # id тарифа по названию
                        bat.Active   = 1      AND  # только если выгрузка включена
                        (ot.id = 1 OR ot.id = 3)  # в данной выгрузке идут только квартиры и комнаты, без долей
                    ORDER BY o.id
                ";
            } else {
                // выгрузка в Winner
                $out = "
                    SELECT
                        o.*,
                        o.id AS id,
                        DATE_FORMAT(o.AddedDate,'%d.%m.%Y') AS WinnerAddedDate,
                        ot.TypeName AS TypeName,
                        CONCAT_WS('', o.City, ' ', o.PlaceTypeSocr) AS WinnerCity,
                        u.MobilePhone AS MobilePhone,
                        u.MobilePhone1 AS MobilePhone1,
                        u.MobilePhone2 AS MobilePhone2
                    FROM
                        Objects AS o,
                        ObjectTypes AS ot,
                        Users AS u,
                        AdPortalObjects AS apo,
                        BillAdTarifs AS bat
                    WHERE
                        o.RealtyType  = 'city' AND # городская вторичка и новостройки. Без выделения новостроек: (o.ObjectAgeType= 56 OR o.ObjectAgeType= 57)
                        o.Active      = 1 AND
                        o.Advertize   = 1 AND
                        o.ObjectType  = ot.id AND
                        o.OwnerUserId = u.id AND
                        o.id = apo.ObjectId AND   # берем только объекты указанные в AdPortalObjects
                        bat.TarifShortName = 'TrfWinner' AND
                        apo.TarifId  = bat.id AND  # id тарифа по названию
                        bat.Active   = 1      AND  # только если выгрузка включена
                        (ot.id = 1 OR ot.id = 3)  # в данной выгрузке идут только квартиры и комнаты, без долей
                    ORDER BY o.id
                ";
            }
        } elseif($BaseName == 'WinnerCountry') {
            if (@$SysParams['EnableCorpSite'] == true) {
                // включить "winner совместимую" выгрузку для корпоративного сайта
                $out = "
                    SELECT
                        o.*,
                        o.id AS id,
                        DATE_FORMAT(o.AddedDate,'%d.%m.%Y') AS WinnerAddedDate,
                        ot.TypeName AS TypeName,
                        CONCAT_WS('', o.City, ' ', o.PlaceTypeSocr) AS WinnerCity,
                        hw.DirectionName AS DirectionName,
                        u.MobilePhone AS MobilePhone,
                        u.MobilePhone1 AS MobilePhone1,
                        u.MobilePhone2 AS MobilePhone2
                    FROM
                        Objects AS o,
                        ObjectTypes AS ot,
                        Users AS u,
                        AdPortalObjects AS apo,
                        ObjectHighways AS hw,
                        BillAdTarifs AS bat
                    WHERE
                        o.RealtyType  = 'country' AND # загородка
                        o.Active      = 1 AND
                        o.Advertize   = 1 AND
                        o.ObjectType  = ot.id AND
                        o.OwnerUserId = u.id AND
                        o.HighwayId   = hw.id AND
                        o.id          = apo.ObjectId AND   # берем только объекты указанные в AdPortalObjects
                        bat.TarifShortName = 'TrfAnSiteFree' AND
                        apo.TarifId  = bat.id AND  # id тарифа по названию
                        bat.Active   = 1           # только если выгрузка включена
                    ORDER BY o.id
                ";
            } else {
                // выгрузка в Winner
                $out = "
                    SELECT
                        o.*,
                        o.id AS id,
                        DATE_FORMAT(o.AddedDate,'%d.%m.%Y') AS WinnerAddedDate,
                        ot.TypeName AS TypeName,
                        CONCAT_WS('', o.City, ' ', o.PlaceTypeSocr) AS WinnerCity,
                        hw.DirectionName AS DirectionName,
                        u.MobilePhone AS MobilePhone,
                        u.MobilePhone1 AS MobilePhone1,
                        u.MobilePhone2 AS MobilePhone2
                    FROM
                        Objects AS o,
                        ObjectTypes AS ot,
                        Users AS u,
                        AdPortalObjects AS apo,
                        ObjectHighways AS hw,
                        BillAdTarifs AS bat
                    WHERE
                        o.RealtyType  = 'country' AND # загородка
                        o.Active      = 1 AND
                        o.Advertize   = 1 AND
                        o.ObjectType  = ot.id AND
                        o.OwnerUserId = u.id AND
                        o.HighwayId   = hw.id AND
                        o.id          = apo.ObjectId AND   # берем только объекты указанные в AdPortalObjects
                        bat.TarifShortName = 'TrfWinner' AND
                        apo.TarifId  = bat.id AND  # id тарифа по названию
                        bat.Active   = 1        # только если выгрузка включена
                    ORDER BY o.id
                ";
            }
        } elseif($BaseName == 'WinnerNovo') {
            // запрос на новостройки встроен в WinnerFlats
            /*$out = "
                    SELECT
                        o.*,
                        o.id AS id,
                        DATE_FORMAT(o.AddedDate,'%d.%m.%Y') AS WinnerAddedDate,
                        ot.TypeName AS TypeName,
                        CONCAT_WS('', o.City, ' ', o.PlaceTypeSocr) AS WinnerCity,
                        u.MobilePhone AS MobilePhone,
                        u.MobilePhone1 AS MobilePhone1,
                        u.MobilePhone2 AS MobilePhone2
                    FROM
                        Objects AS o,
                        ObjectTypes AS ot,
                        Users AS u,
                        AdPortalObjects AS apo,
                        BillAdTarifs AS bat
                    WHERE
                        o.RealtyType   = 'city' AND # городская
                        o.ObjectAgeType= 57 AND     # новостройка
                        o.Active      = 1 AND
                        o.Advertize   = 1 AND
                        o.ObjectType  = ot.id AND
                        o.OwnerUserId = u.id AND
                        o.id = apo.ObjectId AND   # берем только объекты указанные в AdPortalObjects

                        bat.TarifShortName = 'TrfWinner' AND
                        apo.TarifId  = bat.id AND  # id тарифа по названию
                        bat.Active   = 1      AND  # только если выгрузка включена
                        (ot.id = 1 OR ot.id = 3)  # в данной выгрузке идут только квартиры и комнаты, без долей
                    ORDER BY o.id
                ";*/
        } elseif($BaseName == 'NavigatorCountry') {
            $out = "
                SELECT
                    o.*,
                    o.id AS id,
                    DATE_FORMAT(o.AddedDate,'%d.%m.%Y') AS WinnerAddedDate,
                    DATE_FORMAT(CURRENT_DATE(),'%d.%m.%Y') AS TodayDate,
                    ot.TypeName AS TypeName,
                    CONCAT_WS('', o.City, ' ', o.PlaceTypeSocr) AS WinnerCity,
					hw.DirectionName AS DirectionName,
					u.MobilePhone AS MobilePhone,
                    u.MobilePhone1 AS MobilePhone1,
                    u.MobilePhone2 AS MobilePhone2
                FROM
                    Objects AS o,
                    ObjectTypes AS ot,
					Users AS u,
					AdPortalObjects AS apo,
					ObjectHighways AS hw,
					BillAdTarifs AS bat
                WHERE
                    o.RealtyType  = 'country' AND # загородка
                    o.Active      = 1 AND
                    o.Advertize   = 1 AND
                    o.ObjectType  = ot.id AND
					o.OwnerUserId = u.id AND
					o.HighwayId   = hw.id AND
					o.id          = apo.ObjectId AND   # берем только объекты указанные в AdPortalObjects
					bat.TarifShortName = 'TrfNavigatorFree' AND
                    apo.TarifId  = bat.id AND   # id тарифа по названию
                    bat.Active   = 1            # только если выгрузка включена
                ORDER BY o.id
            ";
        } elseif($BaseName == 'NavigatorFlats') {
                // Запрос берет только квартиры и комнаты, без долей
                // формат navigatorа - такой же как у виннера, ничего не меняем
                $out = "
                SELECT
                    o.*,
                    o.id AS id,
                    DATE_FORMAT(o.AddedDate,'%d.%m.%Y') AS WinnerAddedDate,
                    DATE_FORMAT(CURRENT_DATE(),'%d.%m.%Y') AS TodayDate,
                    ot.TypeName AS TypeName,
                    CONCAT_WS('', o.City, ' ', o.PlaceTypeSocr) AS WinnerCity,
					u.MobilePhone AS MobilePhone,
                    u.MobilePhone1 AS MobilePhone1,
                    u.MobilePhone2 AS MobilePhone2
                FROM
                    Objects AS o,
                    ObjectTypes AS ot,
					Users AS u,
					AdPortalObjects AS apo,
					BillAdTarifs AS bat
                WHERE
                    o.RealtyType  = 'city' AND # городская
                    o.ObjectAgeType= 56 AND    #вторичка
                    o.Active      = 1 AND
                    o.Advertize   = 1 AND
                    o.ObjectType  = ot.id AND
					o.OwnerUserId = u.id AND
					o.id = apo.ObjectId AND   # берем только объекты указанные в AdPortalObjects
					bat.TarifShortName = 'TrfNavigatorFree' AND
                    apo.TarifId  = bat.id AND   # id тарифа по названию
                    bat.Active   = 1      AND   # только если выгрузка включена
					(ot.id = 1 OR ot.id = 3)  # в данной выгрузке идут только (ObjectTypes) квартиры и комнаты, без долей
                ORDER BY o.id
            ";
        } elseif($BaseName == 'WinnerDoli') {
            // Запрос берет только доли
            $out = "
                SELECT
                    o.*,
                    o.id AS id,
                    DATE_FORMAT(o.AddedDate,'%d.%m.%Y') AS WinnerAddedDate,
                    ot.TypeName AS TypeName,
                    CONCAT_WS('', o.City, ' ', o.PlaceTypeSocr) AS WinnerCity,
					u.MobilePhone AS MobilePhone,
                    u.MobilePhone1 AS MobilePhone1,
                    u.MobilePhone2 AS MobilePhone2
                FROM
                    Objects AS o,
                    ObjectTypes AS ot,
					Users AS u,
					AdPortalObjects AS apo,
					BillAdTarifs AS bat
                WHERE
                    o.RealtyType   = 'city' AND # городская
                    o.ObjectAgeType= 56 AND     # вторичка
                    o.Active      = 1 AND
                    o.Advertize   = 1 AND
                    o.ObjectType = ot.id AND
					o.OwnerUserId = u.id AND
					o.id = apo.ObjectId AND   # берем только объекты указанные в AdPortalObjects
					bat.TarifShortName = 'TrfWinner' AND
                    apo.TarifId  = bat.id AND   # id тарифа по названию
                    bat.Active   = 1      AND   # только если выгрузка включена
					(ot.id = 2)  # толко доли
                ORDER BY o.id
            ";
        } elseif($BaseName == 'CianFlats') {
            $out = "
                SELECT
                    o.*,
                    o.id AS id,
                    DATE(o.AddedDate) AS CianAddedDate,
                    ot.id AS ObjectTypeId,
					m.CianId AS CianMetroStationId,
					op.CianMark AS CianMetroWayTypeMark,
                    u.MobilePhone AS MobilePhone,
                    u.MobilePhone1 AS MobilePhone1,
                    u.MobilePhone2 AS MobilePhone2,
                    bat.TarifShortName            # несколько тарифов циана различаем в скрипте выгрузки
                FROM
                    Objects AS o,
                    ObjectTypes AS ot,
					Users AS u,
					MetroStations AS m,
					ObjectParams AS op,
					AdPortalObjects AS apo,
					BillAdTarifs AS bat
                WHERE
                    o.RealtyType  = 'city' AND  # городская
                    #o.ObjectAgeType= 56 AND    # вторичка и новостройки
                    o.Active      = 1 AND
                    o.Advertize   = 1 AND
                    o.ObjectType  = ot.id AND
					o.OwnerUserId = u.id AND
					o.MetroStation1Id = m.id AND
					o.MetroWayType = op.id AND
					o.id = apo.ObjectId AND     # берем только объекты указанные в AdPortalObjects

					(bat.TarifShortName = 'TrfCian' OR bat.TarifShortName = 'TrfCianPremium' OR bat.TarifShortName = 'TrfCianRentPremium') AND
                    apo.TarifId  = bat.id AND   # id тарифа по названию
                    bat.Active   = 1            # только если выгрузка включена
                GROUP BY o.id
            ";
        } elseif($BaseName == 'CianCountry') {
            $out = "
                SELECT
                    o.*,
                    o.id AS id,
                    DATE(o.AddedDate) AS CianAddedDate,
                    ot.CianMark AS ObjectType,
					hw.CianValue AS CianRouteId,
					u.MobilePhone AS MobilePhone,
                    u.MobilePhone1 AS MobilePhone1,
                    u.MobilePhone2 AS MobilePhone2,
                    bat.TarifShortName            # несколько тарифов циана различаем в скрипте выгрузки
                FROM
                    Objects AS o,
                    ObjectTypes AS ot,
					Users AS u,
					AdPortalObjects AS apo,
					ObjectHighways AS hw,
					BillAdTarifs AS bat
                WHERE
                    o.RealtyType  = 'country' AND # загородка
                    o.Active      = 1 AND
                    o.Advertize   = 1 AND
                    o.ObjectType  = ot.id AND
					o.OwnerUserId = u.id AND
					o.HighwayId   = hw.id AND
					o.id = apo.ObjectId AND   # берем только объекты указанные в AdPortalObjects

					(bat.TarifShortName = 'TrfCian' OR bat.TarifShortName = 'TrfCianPremium' OR bat.TarifShortName = 'TrfCianRentPremium') AND
                    apo.TarifId  = bat.id AND   # id тарифа по названию
                    bat.Active   = 1            # только если выгрузка включена
                GROUP BY o.id
            ";
        } elseif($BaseName == 'avito') {
            $out = "
                SELECT
                    o.*,
                    o.id AS id,
                    DATE(o.AddedDate) AS AvitoAddedDate,
                    DATE_ADD(CURRENT_DATE, INTERVAL 2 WEEK) AS AvitoDateEnd,
                    ot.id AS ObjectTypeId,
					m.StationName AS MetroStationName,
					u.MobilePhone AS MobilePhone,
                    u.MobilePhone1 AS MobilePhone1,
                    u.MobilePhone2 AS MobilePhone2
                FROM
                    Objects AS o,
                    ObjectTypes AS ot,
					Users AS u,
					MetroStations AS m,
					AdPortalObjects AS apo,
					BillAdTarifs AS bat
                WHERE
                    o.Active      = 1 AND
                    o.Advertize   = 1 AND
                    o.ObjectType  = ot.id AND
					o.OwnerUserId = u.id AND
					o.MetroStation1Id = m.id AND
					o.id = apo.ObjectId AND   # берем только объекты указанные в AdPortalObjects
					bat.TarifShortName = 'TrfAvito' AND
                    apo.TarifId        = bat.id AND   # id тарифа по названию
                    bat.Active         = 1            # только если выгрузка включена
                ORDER BY o.id
            ";
        } elseif($BaseName == 'RbcFlats') {
            $out = "
                SELECT
                    o.*,
                    o.id AS id,
                    ot.id AS ObjectTypeId,
					m.StationName AS MetroStationName,
					hw.DirectionName AS DirectionName,
					CONCAT(SUBSTR(o.Description,1,150), '...') AS DescriptionShort,
					ROUND(SquareAll) AS SquareAll,
					ROUND(SquareLiving) AS SquareLiving,
					ROUND(SquareKitchen) AS SquareKitchen,
					u.MobilePhone AS MobilePhone,
                    u.MobilePhone1 AS MobilePhone1,
                    u.MobilePhone2 AS MobilePhone2
                FROM
                    Objects AS o,
                    ObjectTypes AS ot,
					Users AS u,
					MetroStations AS m,
					ObjectParams AS op,
					AdPortalObjects AS apo,
					ObjectHighways AS hw,
					BillAdTarifs AS bat
                WHERE
                    o.RealtyType  = 'city' AND # только городская
                    o.ObjectAgeType= 56 AND    #вторичка
                    o.Active      = 1 AND
                    o.Advertize   = 1 AND
                    o.ObjectType  = ot.id AND
					o.OwnerUserId = u.id AND
					o.MetroStation1Id = m.id AND
					o.MetroWayType = op.id AND
					o.HighwayId   = hw.id AND
					o.id = apo.ObjectId AND   # берем только объекты указанные в AdPortalObjects
					bat.TarifShortName = 'TrfRbcFree' AND
                    apo.TarifId        = bat.id AND   # id тарифа по названию
                    bat.Active         = 1            # только если выгрузка включена
                ORDER BY o.id
            ";
        } elseif($BaseName == 'RbcCountry') {
            $out = "
                SELECT
                    o.*,
                    o.id AS id,
                    ot.id AS ObjectTypeId,
					hw.DirectionName AS DirectionName,
					CONCAT(SUBSTR(o.Description,1,150),'...') AS DescriptionShort,
					ROUND(LandSquare) AS LandSquare,
					ROUND(SquareAll) AS SquareAll,
					ROUND(SquareLiving) AS SquareLiving,
					ROUND(SquareKitchen) AS SquareKitchen,
					u.MobilePhone  AS MobilePhone,
                    u.MobilePhone1 AS MobilePhone1,
                    u.MobilePhone2 AS MobilePhone2
                FROM
                    Objects AS o,
                    ObjectTypes AS ot,
					Users AS u,
					AdPortalObjects AS apo,
					ObjectHighways AS hw,
					BillAdTarifs AS bat
                WHERE
                    o.RealtyType  = 'country' AND # только загородка
                    o.Active      = 1 AND
                    o.Advertize   = 1 AND
                    o.ObjectType  = ot.id AND
					o.OwnerUserId = u.id  AND
					o.HighwayId   = hw.id AND
					o.id = apo.ObjectId AND   # берем только объекты указанные в AdPortalObjects
					bat.TarifShortName = 'TrfRbcFree' AND
                    apo.TarifId        = bat.id AND   # id тарифа по названию
                    bat.Active         = 1            # только если выгрузка включена
                ORDER BY o.id
            ";
        } elseif($BaseName == 'Afy') {
            $out = "SELECT
                        o.*,
                        o.id AS id,
                        ot.TypeName AS TypeName,
                        u.MobilePhone  AS MobilePhone,
                        u.MobilePhone1 AS MobilePhone1,
                        u.MobilePhone2 AS MobilePhone2
                    FROM
                        Objects AS o,
                        ObjectTypes AS ot,
                        Users AS u,
                        AdPortalObjects AS apo,
                        BillAdTarifs AS bat
                    WHERE
                        o.Active      = 1 AND
                        o.Advertize   = 1 AND
                        o.ObjectType  = ot.id AND
                        o.OwnerUserId = u.id AND
                        o.id = apo.ObjectId AND   # берем только объекты указанные в AdPortalObjects
                        bat.TarifShortName = 'TrfAfy' AND
                        apo.TarifId        = bat.id AND   # id тарифа по названию
                        bat.Active         = 1            # только если выгрузка включена
                    ORDER BY o.id
                ";
        } elseif($BaseName == 'YandexFlats') {
            // todo переделать внутри на YANDEX
            $out = "
                    SELECT
                        o.*,
                        o.id AS id,
                        DATE_FORMAT(o.AddedDate,'%d.%m.%Y') AS WinnerAddedDate,
                        ot.TypeName AS TypeName,
                        CONCAT_WS('', o.City, ' ', o.PlaceTypeSocr) AS WinnerCity,
                        u.MobilePhone  AS MobilePhone,
                        u.MobilePhone1 AS MobilePhone1,
                        u.MobilePhone2 AS MobilePhone2
                    FROM
                        Objects AS o,
                        ObjectTypes AS ot,
                        Users AS u,
                        AdPortalObjects AS apo,
                        BillAdTarifs AS bat
                    WHERE
                        o.Active      = 1 AND
                        o.Advertize   = 1 AND
                        o.ObjectType  = ot.id AND
                        o.OwnerUserId = u.id AND
                        o.id = apo.ObjectId AND   # берем только объекты указанные в AdPortalObjects
                        bat.TarifShortName = 'TrfYandex' AND
                        apo.TarifId        = bat.id AND   # id тарифа по названию
                        bat.Active         = 1            # только если выгрузка включена
                    ORDER BY o.id
                ";
        } elseif($BaseName == 'MielFlats') {
            $out = "
                    SELECT
                        o.*,
                        o.id AS id,
                        DATE_FORMAT(o.AddedDate,'%d.%m.%Y') AS WinnerAddedDate,
                        ot.TypeName AS TypeName,
                        CONCAT_WS('', o.City, ' ', o.PlaceTypeSocr) AS WinnerCity,
                        u.FirstName,
                        u.LastName,
                        NOW() AS Changed,
                        u.MobilePhone  AS MobilePhone,
                        u.MobilePhone1 AS MobilePhone1,
                        u.MobilePhone2 AS MobilePhone2
                    FROM
                        Objects AS o,
                        ObjectTypes AS ot,
                        Users AS u,
                        AdPortalObjects AS apo,
                        BillAdTarifs AS bat
                    WHERE
                        o.RealtyType  = 'city' AND # городская
                        o.ObjectAgeType= 56 AND    # вторичка
                        o.Active      = 1 AND
                        o.Advertize   = 1 AND
                        o.ObjectType  = ot.id AND
                        o.OwnerUserId = u.id AND
                        o.id = apo.ObjectId AND   # берем только объекты указанные в AdPortalObjects
                        bat.TarifShortName = 'TrfWinner' AND
                        apo.TarifId        = bat.id AND   # id тарифа по названию
                        bat.Active         = 1      AND   # только если выгрузка включена
                        (ot.id = 1 OR ot.id = 3)  # в данной выгрузке идут только квартиры и комнаты, без долей
                    ORDER BY o.id
                ";
        } else {
            echo 'FATAL: unknown param';exit;
        }
        return $out;
    }

    function ClearXmlText($text) {
        $text = str_replace("\n",' ',$text);
        $text  = htmlspecialchars($text);
        return $text;
    }

