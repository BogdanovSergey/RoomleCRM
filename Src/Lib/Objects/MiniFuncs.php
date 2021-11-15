<?php
    function GetRealtyTypesArr($Params=array()) {
        $out = array();
        $sql = "SELECT
                    *
                FROM
                    RealtyTypes";
        $res = mysql_query($sql);
        $res = mysql_query($sql);
        while($str = mysql_fetch_object($res)) {
            if(isset($Params['OnlyNames'])) {
                array_push($out, $str->TypeName);
            }else{
                array_push($out, $str);
            }
        }
        $GLOBALS['FirePHP']->info($sql);
        return $out;
    }

    function GetObjectById($ObjectId) {
        $out = null;
        if(@$ObjectId>0) {
            $sql = "SELECT
                        *
                    FROM
                        Objects
                    WHERE
                        id = $ObjectId";
            $res = mysql_query($sql);
            $GLOBALS['FirePHP']->info($sql);
            $out = mysql_fetch_object($res);
        }
        return $out;
    }

    function QuickObjectQueryById($ObjectId) {
        $out = null;
        if($ObjectId) {
            $sql = "SELECT
                        RealtyType, Active
                    FROM
                        Objects
                    WHERE
                        id = $ObjectId";
            $res = mysql_query($sql);
            $GLOBALS['FirePHP']->info($sql);
            $out = mysql_fetch_object($res);
        }
        return $out;
    }

    function GetHighwayNameById($id, $Column = 'DirectionName') {
        $out = null;
        if($id) {
            $sql = "SELECT
                        $Column AS result
                    FROM
                        ObjectHighways
                    WHERE
                        id = $id";
            $res = mysql_query($sql);
            $GLOBALS['FirePHP']->info($sql);
            $out = mysql_fetch_object($res);
            $out = $out->result;
        }
        return $out;
    }

    function MakeSqlQuery($ObjectType, $QueryType, $DataArr, $MoreParams) {
        $sql = '';
        if($ObjectType == 'city') {

            if($QueryType == 'insert') {
                $sql = "
                    INSERT INTO Objects
                        (
                        AddedDate,    RealtyType, ImportId,
                        Color, ObjectType,    RoomsCount,
                        RoomsSell,    PartsTotal, PartsSell, Advertize, Active,
                        OwnerUserId,  Region,     Raion,
                        DealType,     NovoDealType,   ObjectAgeType,

                        City,         AltCityName,PlaceType,  PlaceTypeSocr, Street,
                        HouseNumber,  Floor,      Floors,
                        MetroStation1Id,  MetroWayType, MetroWayMinutes,
                        SquareAll,    SquareLiving, SquareKitchen,

                        ObjectCondition,      Toilet,     Balcon,
                        WindowView,           Lift,       Parking,
                        Telephone,            Territory,  Garbage,
                        Price,        Description,        BuildingType,
                        SobPhone,     SobName, Flooring,  Mortgage,
                        Currency,     Latitude, Longitude,YandexAddress,
                        Metro2StationId,Metro2WayType,Metro2WayMinutes,
                        Metro3StationId,Metro3WayType,Metro3WayMinutes,
                        Metro4StationId,Metro4WayType,Metro4WayMinutes,
                        OwnerPhoneId, AddCorpPhone, OwnerClientId,
                        ObjectBrandName, BuildingSeriesId,
                        Utka
                        )
                    VALUES (
                        NOW(),    'city',               NULLIF('".@$DataArr['ImportId']."',''),
                        NULLIF('".@$DataArr['Color']."',''),   '".@$DataArr['ObjectType']."',      '".@$DataArr['RoomsCount']."',
                        '".@$DataArr['RoomsSell']."',    NULLIF('".@$DataArr['PartsTotal']."',''),      NULLIF('".@$DataArr['PartsSell']."',''), 1, 1,
                        '".@$DataArr['OwnerUserId']."', '".@$DataArr['KladrRegion']."',     '".@$DataArr['KladrRaion']."',
                        '".@$DataArr['DealType']."',    '".@$DataArr['NovoDealType']."',        '".@$DataArr['ObjectAgeType']."',

                        '".@$DataArr['KladrCity']."',   NULLIF('".@$DataArr['AltCityName']."',''), '".@$DataArr['PlaceType']."',       '".@$MoreParams['PlaceTypeSocr']."', '".@$DataArr['Street']."',
                        '".@$DataArr['HouseNumber']."', '".@$DataArr['Floor']."',            '".@$DataArr['Floors']."',
                        '".@$DataArr['MetroStation1Id']."', '".@$DataArr['MetroWayType']."', '".@$DataArr['MetroWayMinutes']."',
                        '".@$DataArr['SquareAll']."',       '".@$DataArr['SquareLiving']."', '".@$DataArr['SquareKitchen']."',

                        '".@$DataArr['ObjectCondition']."', '".@$DataArr['Toilet']."',     '".@$DataArr['Balcon']."',
                        '".@$DataArr['WindowView']."',      '".@$DataArr['Lift']."',       '".@$DataArr['Parking']."',
                        '".@$DataArr['Telephone']."',       '".@$DataArr['Territory']."',  '".@$DataArr['Garbage']."',
                        '".@$DataArr['Price']."',           '".@$DataArr['Description']."', '".@$DataArr['BuildingType']."',
                        '".@$DataArr['SobPhone']."',        '".@$DataArr['SobName']."',     '".@$DataArr['Flooring']."',    '".@$DataArr['Mortgage']."',
                        '".@$DataArr['Currency']."',        NULLIF('".@$MoreParams['GeoCoords']->Latitude."',''), NULLIF('".@$MoreParams['GeoCoords']->Longitude."',''), NULLIF('".@$MoreParams['GeoCoords']->YandexAddress."',''),
                        NULLIF('".@$DataArr['Metro2StationId']."',   ''),
                        NULLIF('".@$DataArr['Metro2WayType']."',     ''),
                        NULLIF('".@$DataArr['Metro2WayMinutes']."',  ''),

                        NULLIF('".@$DataArr['Metro3StationId']."',   ''),
                        NULLIF('".@$DataArr['Metro3WayType']."',     ''),
                        NULLIF('".@$DataArr['Metro3WayMinutes']."',  ''),

                        NULLIF('".@$DataArr['Metro4StationId']."',   ''),
                        NULLIF('".@$DataArr['Metro4WayType']."',     ''),
                        NULLIF('".@$DataArr['Metro4WayMinutes']."',  ''),
                        '".@$DataArr['OwnerPhoneId']."',
                        NULLIF('".@$DataArr['AddCorpPhone']."',  ''),
                        NULLIF('".@$DataArr['OwnerClientId']."',  ''),
                        NULLIF('".@$DataArr['ObjectBrandName']."',  ''),
                        NULLIF('".@$DataArr['BuildingSeriesId']."',  ''),
                        '".@$DataArr['Utka']."'
                    )
                ";
            } elseif($QueryType == 'update') {
                if($MoreParams['EditSpecial']) {
                    $sql = "
                    UPDATE Objects SET
                            LastUpdateDate  = NOW(),
                            Price           = '".@$DataArr['Price']."',
                            Description     = '".@$DataArr['Description']."'
                        WHERE
                            id = ".$DataArr['LoadedObjectId'];
                } else {
                    $sql = "
                    UPDATE Objects SET
                            LastUpdateDate  = NOW(),
                            Color           = NULLIF('".@$DataArr['Color']."',''),
                            HasErrors       = 0,
                            Price           = '".@$DataArr['Price']."',
                            DealType        = '".@$DataArr['DealType']."',
                            NovoDealType    = '".@$DataArr['NovoDealType']."',
                            ObjectAgeType   = '".@$DataArr['ObjectAgeType']."',
                            ObjectType      = '".@$DataArr['ObjectType']."',
                            RoomsCount      = '".@$DataArr['RoomsCount']."',
                            RoomsSell       = '".@$DataArr['RoomsSell']."',
                            PartsSell       = '".@$DataArr['PartsSell']."',
                            PartsTotal      = '".@$DataArr['PartsTotal']."',
                            OwnerUserId     = '".@$DataArr['OwnerUserId']."',
                            Region          = '".@$DataArr['KladrRegion']."',
                            Raion           = '".@$DataArr['KladrRaion']."',
                            City            = '".@$DataArr['KladrCity']."',
                            AltCityName     = NULLIF('".@$DataArr['AltCityName']."',''),
                            PlaceType       = '".@$DataArr['PlaceType']."',
                            PlaceTypeSocr   = '".@$MoreParams['PlaceTypeSocr']."',
                            Street          = '".@$DataArr['Street']."',
                            HouseNumber     = '".@$DataArr['HouseNumber']."',
                            Floor           = '".@$DataArr['Floor']."',
                            Floors          = '".@$DataArr['Floors']."',
                            MetroStation1Id = '".@$DataArr['MetroStation1Id']."',
                            MetroWayType    = '".@$DataArr['MetroWayType']."',
                            MetroWayMinutes = '".@$DataArr['MetroWayMinutes']."',

                            Metro2StationId  = NULLIF('".@$DataArr['Metro2StationId']."',   ''),
                            Metro2WayType    = NULLIF('".@$DataArr['Metro2WayType']."',     ''),
                            Metro2WayMinutes = NULLIF('".@$DataArr['Metro2WayMinutes']."',  ''),

                            Metro3StationId  = NULLIF('".@$DataArr['Metro3StationId']."',   ''),
                            Metro3WayType    = NULLIF('".@$DataArr['Metro3WayType']."',     ''),
                            Metro3WayMinutes = NULLIF('".@$DataArr['Metro3WayMinutes']."',  ''),

                            Metro4StationId  = NULLIF('".@$DataArr['Metro4StationId']."',   ''),
                            Metro4WayType    = NULLIF('".@$DataArr['Metro4WayType']."',     ''),
                            Metro4WayMinutes = NULLIF('".@$DataArr['Metro4WayMinutes']."',  ''),

                            SquareAll       = '".@$DataArr['SquareAll']."',
                            SquareLiving    = '".@$DataArr['SquareLiving']."',
                            SquareKitchen   = '".@$DataArr['SquareKitchen']."',
                            ObjectCondition = '".@$DataArr['ObjectCondition']."',
                            Toilet          = '".@$DataArr['Toilet']."',
                            Balcon          = '".@$DataArr['Balcon']."',
                            WindowView      = '".@$DataArr['WindowView']."',
                            Lift            = '".@$DataArr['Lift']."',
                            Parking         = '".@$DataArr['Parking']."',
                            Telephone       = '".@$DataArr['Telephone']."',
                            Territory       = '".@$DataArr['Territory']."',
                            Garbage         = '".@$DataArr['Garbage']."',
                            Description     = '".@$DataArr['Description']."',
                            BuildingType    = '".@$DataArr['BuildingType']."',
                            SobPhone        = '".@$DataArr['SobPhone']."',
                            SobName         = '".@$DataArr['SobName']."',
                            Flooring        = '".@$DataArr['Flooring']."',
                            Mortgage        = '".@$DataArr['Mortgage']."',
                            Currency        = '".@$DataArr['Currency']."',
                            Latitude        = NULLIF('".@$MoreParams['GeoCoords']->Latitude."',''),
                            Longitude       = NULLIF('".@$MoreParams['GeoCoords']->Longitude."',''),
                            YandexAddress   = NULLIF('".@$MoreParams['GeoCoords']->YandexAddress."',''),
                            OwnerPhoneId    = '".@$DataArr['OwnerPhoneId']."',
                            AddCorpPhone    = NULLIF('".@$DataArr['AddCorpPhone']."',  ''),
                            OwnerClientId   = NULLIF('".@$DataArr['OwnerClientId']."',  ''),
                            ObjectBrandName = NULLIF('".@$DataArr['ObjectBrandName']."',  ''),
                            BuildingSeriesId= NULLIF('".@$DataArr['BuildingSeriesId']."',  ''),
                            Utka            = '".@$DataArr['Utka']."'
                        WHERE
                            id = ".$DataArr['LoadedObjectId'];
                }

            } else {

            }
        } else if($ObjectType == 'country') {
            if($QueryType == 'update') {
                if($MoreParams['EditSpecial']) {
                    $sql = "
                    UPDATE Objects SET
                            LastUpdateDate  = NOW(),
                            Price           = '".@$DataArr['Price']."',
                            Description     = '".@$DataArr['Description']."'
                        WHERE
                            id = ".$DataArr['LoadedObjectId'];
                } else {
                    $sql = "
                        UPDATE Objects SET
                            LastUpdateDate  = NOW(),
                            Color           = NULL,
                            HasErrors       = 0,
                            Price           = '".@$DataArr['Price']."',
                            DealType        = '".@$DataArr['DealType']."',
                            ObjectType      = '".@$DataArr['ObjectType']."',
                            OwnerUserId     = '".@$DataArr['OwnerUserId']."',
                            Region          = '".@$DataArr['KladrRegion']."',
                            Raion           = '".@$DataArr['KladrRaion']."',
                            City            = '".@$DataArr['KladrCity']."',
                            AltCityName     = NULLIF('".@$DataArr['AltCityName']."',''),
                            PlaceType       = '".@$DataArr['PlaceType']."',
                            PlaceTypeSocr   = '".$MoreParams['PlaceTypeSocr']."',
                            Street          = '".@$DataArr['Street']."',
                            HouseNumber     = '".@$DataArr['HouseNumber']."',
                            LandSquare      = '".@$DataArr['LandSquare']."',
                            SquareLiving    = '".@$DataArr['SquareLiving']."',
                            Floors          = '".@$DataArr['Floors']."',
                            Description     = '".@$DataArr['Description']."',
                            SobPhone        = '".@$DataArr['SobPhone']."',
                            SobName         = '".@$DataArr['SobName']."',
                            Currency        = '".@$DataArr['Currency']."',

                            HighwayId       = '".@$DataArr['HighwayId']."',
                            Distance        = '".@$DataArr['Distance']."',
                            LandTypeId      = NULLIF('".@$DataArr['LandTypeId']."',     ''),
                            CountryElectro  = NULLIF('".@$DataArr['CountryElectro']."', ''),
                            CountryWater    = NULLIF('".@$DataArr['CountryWater']."',   ''),
                            CountryGas      = NULLIF('".@$DataArr['CountryGas']."',     ''),
                            CountrySewer    = NULLIF('".@$DataArr['CountrySewer']."',   ''),
                            CountryHeat     = NULLIF('".@$DataArr['CountryHeat']."',    ''),
                            CountryPmg      = NULLIF('".@$DataArr['CountryPmg']."',     ''),
                            CountrySecure   = NULLIF('".@$DataArr['CountrySecure']."',  ''),

                            CountryToilet   = NULLIF('".@$DataArr['CountryToilet']."',   ''),
                            CountryBath     = NULLIF('".@$DataArr['CountryBath']."',     ''),
                            CountryGarage   = NULLIF('".@$DataArr['CountryGarage']."',   ''),
                            CountryPool     = NULLIF('".@$DataArr['CountryPool']."',     ''),
                            CountryMaterial = NULLIF('".@$DataArr['CountryMaterial']."', ''),
                            CountryWallsTypeId= NULLIF('".@$DataArr['CountryWallsTypeId']."', ''),
                            CountryPhone      = NULLIF('".@$DataArr['CountryPhone']."',    ''),

                            SobPhone        = NULLIF('".@$DataArr['SobPhone']."',  ''),
                            SobName         = NULLIF('".@$DataArr['SobName']."',   ''),

                            Latitude        = NULLIF('".@$MoreParams['GeoCoords']->Latitude."',''),
                            Longitude       = NULLIF('".@$MoreParams['GeoCoords']->Longitude."',''),
                            YandexAddress   = NULLIF('".@$MoreParams['GeoCoords']->YandexAddress."',''),

                            OwnerPhoneId    = '".@$DataArr['OwnerPhoneId']."',
                            AddCorpPhone    = NULLIF('".@$DataArr['AddCorpPhone']."',   ''),
                            OwnerClientId   = NULLIF('".@$DataArr['OwnerClientId']."',   ''),
                            Utka            = '".@$DataArr['Utka']."'
                        WHERE
                            id = ".@$DataArr['LoadedObjectId'];

                }
            }

        } else if($ObjectType == 'commerce') {
            if($MoreParams['EditSpecial']) {
                $sql = "
                    UPDATE Objects SET
                            LastUpdateDate        = NOW(),
                            Price                 = '".@$DataArr['Price']."',
                            PriceTypeId           = '".@$DataArr['CommercePriceTypeId']."',
                            CommercePricePeriodId = NULLIF('".@$DataArr['CommercePricePeriodId']."',''),
                            CommercePriceTypeId   = NULLIF('".@$DataArr['CommercePriceTypeId']."',  ''),
                            Description           = '".@$DataArr['Description']."'
                        WHERE
                            id = ".$DataArr['LoadedObjectId'];
            } else {
                    $sql = "UPDATE Objects SET
                                LastUpdateDate  = NOW(),
                                HasErrors       = 0,
                                Price       = '".@$DataArr['Price']."',
                                Currency    = '".@$DataArr['Currency']."',
                                OwnerUserId = '".@$DataArr['OwnerUserId']."',
                                PriceTypeId = '".@$DataArr['CommercePriceTypeId']."',
                                DealType    = '".@$DataArr['DealType']."',
                                ObjectType  = '".@$DataArr['CommerceObjectTypeId']."',
                                Region      = '".@$DataArr['KladrRegion']."',
                                Raion       = '".@$DataArr['KladrRaion']."',
                                City        = '".@$DataArr['KladrCity']."',
                                AltCityName = NULLIF('".@$DataArr['AltCityName']."',''),
                                PlaceType   = '".@$DataArr['PlaceType']."',
                                PlaceTypeSocr='".$MoreParams['PlaceTypeSocr']."',
                                Street      = '".@$DataArr['Street']."',
                                HouseNumber = '".@$DataArr['HouseNumber']."',
                                SquareAll   = '".@$DataArr['SquareAll']."',
                                RoomsCount  = '".@$DataArr['RoomsCount']."',
                                Floor       = '".@$DataArr['Floor']."',
                                Floors      = '".@$DataArr['Floors']."',
                                MetroStation1Id       = '".@$DataArr['MetroStation1Id']."',
                                MetroWayMinutes       = '".@$DataArr['MetroWayMinutes']."',
                                MetroWayType          = '".@$DataArr['MetroWayType']."',
                                Description           = '".@$DataArr['Description']."',
                                CommercePricePeriodId = NULLIF('".@$DataArr['CommercePricePeriodId']."',''),
                                CommercePriceTypeId   = NULLIF('".@$DataArr['CommercePriceTypeId']."',  ''),
                                ObjectBrandName       = NULLIF('".@$DataArr['ObjectBrandName']."',   ''),
                                CommerceObjectTypeId  = NULLIF('".@$DataArr['CommerceObjectTypeId']."', ''),
                                CommerceRoomTypeId    =	NULLIF('".@$DataArr['CommerceRoomTypeId']."',   ''),
                                Latitude              = NULLIF('".@$MoreParams['GeoCoords']->Latitude."',  ''),
                                Longitude             = NULLIF('".@$MoreParams['GeoCoords']->Longitude."', ''),
                                YandexAddress         = NULLIF('".@$MoreParams['GeoCoords']->YandexAddress."',''),
                                OwnerPhoneId          = '".@$DataArr['OwnerPhoneId']."',
                                AddCorpPhone          = NULLIF('".@$DataArr['AddCorpPhone']."',''),
                                OwnerClientId         = NULLIF('".@$DataArr['OwnerClientId']."',   '')
                            WHERE
                                id = {$DataArr['LoadedObjectId']}
                            ";
            }
        } else if($ObjectType == 'commerceMore') {
            $sql = "UPDATE ObjectsData SET
                        LastUpdateDate          =   NOW(),
                        CommerceSquareMin       =   NULLIF('".@$DataArr['CommerceSquareMin']."',     ''),
                        CommerceBuildingTypeId  =   NULLIF('".@$DataArr['CommerceBuildingTypeId']."',     ''),
                        CommerceEnterTypeId     =	NULLIF('".@$DataArr['CommerceEnterTypeId']."',     ''),
                        CommercePhoneLinesCount =	NULLIF('".@$DataArr['CommercePhoneLinesCount']."',     ''),
                        CommercePhoneLinesAddId =	NULLIF('".@$DataArr['CommercePhoneLinesAddId']."',     ''),
                        CommerceFurnitureId     =	NULLIF('".@$DataArr['CommerceFurnitureId']."',     ''),
                        CommerceBuildingsCount  =	NULLIF('".@$DataArr['CommerceBuildingsCount']."',     ''),
                        CommerceCeilingHeight   =	NULLIF('".@$DataArr['CommerceCeilingHeight']."',     ''),
                        CommerceBuildingYear    =	NULLIF('".@$DataArr['CommerceBuildingYear']."',     ''),
                        CommerceBuildingClass   =	NULLIF('".@$DataArr['CommerceBuildingClass']."',     ''),
                        CommerceCommunPayId     =	NULLIF('".@$DataArr['CommerceCommunPayId']."',     ''),
                        CommerceExplutPayId     =	NULLIF('".@$DataArr['CommerceExplutPayId']."',     ''),
                        RoomMapId               =	NULLIF('".@$DataArr['RoomMapId']."',     ''),
                        CommerceRoomsSquares    =	NULLIF('".@$DataArr['CommerceRoomsSquares']."',     ''),
                        CommerceConditionId     =	NULLIF('".@$DataArr['CommerceConditionId']."',     ''),
                        CommerceBuildingStatusId=	NULLIF('".@$DataArr['CommerceBuildingStatusId']."',     ''),
                        CommerceFireId          =	NULLIF('".@$DataArr['CommerceFireId']."',     ''),
                        CommerceVentilationId   =	NULLIF('".@$DataArr['CommerceVentilationId']."',     ''),
                        CommerceHeatingId       =	NULLIF('".@$DataArr['CommerceHeatingId']."',     ''),
                        CommercePower           =	NULLIF('".@$DataArr['CommercePower']."',     ''),
                        CommerceFloorLoad       =	NULLIF('".@$DataArr['CommerceFloorLoad']."',     ''),
                        CommerceParkingId       =	NULLIF('".@$DataArr['CommerceParkingId']."',     ''),
                        CommerceParkingPlaces   =	NULLIF('".@$DataArr['CommerceParkingPlaces']."',     ''),
                        CommerceLifts           =	NULLIF('".@$DataArr['CommerceLifts']."',     ''),
                        LiftBrand               =	NULLIF('".@$DataArr['LiftBrand']."',     ''),
                        CommerceAgentPay        =	NULLIF('".@$DataArr['CommerceAgentPay']."',     ''),
                        CommerceClientPay       =	NULLIF('".@$DataArr['CommerceClientPay']."',  ''),
                        TelecomProvider         =	NULLIF('".@$DataArr['TelecomProvider']."',    ''),
                        OptionInternet          =	NULLIF('".@$DataArr['OptionInternet']."',  ''),
                        OptionToilet            =	NULLIF('".@$DataArr['OptionToilet']."',    ''),
                        OptionCafe              =	NULLIF('".@$DataArr['OptionCafe']."',      ''),
                        OptionBankomat          =	NULLIF('".@$DataArr['OptionBankomat']."',  ''),
                        OptionFitness           =	NULLIF('".@$DataArr['OptionFitness']."',   ''),
                        OptionShop              =	NULLIF('".@$DataArr['OptionShop']."',      '')
                    WHERE
                        ObjectId = {$DataArr['LoadedObjectId']}
                    ";
        } else {
            echo __FUNCTION__."() params error";
        }
        return $sql;
    }

    function ResetObjectImagesPrimarity($ObjectId) {
        $sql = "UPDATE
                    ObjectImages
                SET
                    IsPrimary = 0
                WHERE
                    ObjectId = $ObjectId";
        mysql_query($sql);
        $GLOBALS['FirePHP']->info($sql);
    }

    function SetObjectFirstImage($ImageId) {
        $sql = "UPDATE
                    ObjectImages
                SET
                    IsPrimary = 1
                WHERE
                    id = $ImageId";
        mysql_query($sql);
        $GLOBALS['FirePHP']->info($sql);
    }

    function GetObjectOwnerPhonesArr($ObjectOwnerId) {
        $out = array();
        $User   = User_GetUserObj($ObjectOwnerId);

        $element            = array();
        $element['id']      = 0;
        $element['VarName'] = $User->MobilePhone;
        array_push($out, $element);

        $element            = array();
        $element['id']      = 1;
        $element['VarName'] = $User->MobilePhone1;
        array_push($out, $element);

        $element            = array();
        $element['id']      = 2;
        $element['VarName'] = $User->MobilePhone2;
        array_push($out, $element);

        return $out;
    }

