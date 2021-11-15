<?php

    function MakeObjectArrByObj($Obj, $AddObj) {
        $Arr = array();
        $Arr = (array)$Obj; // ??? ВСЕ ПОЛЯ будут ВИДНЫ В JSON...

        if($Obj->Region == 'Москва') {
            $Arr['CityType']         = 'Moscow'; // указано в radiofield в полях адрес
        } else {
            $Arr['CityType']         = 'Oblast';
        }
        $Arr['LoadedObjectId']= $Obj->id;   // важный параметр для extjs функций
        $Arr['KladrRegion']   = $Obj->Region;
        $Arr['KladrRaion']    = $Obj->Raion;
        $Arr['KladrCity']     = $Obj->City;
        $Arr['KladrPlaceType']= $Obj->PlaceType;

        $Arr['OwnerPhoneId'] = $Obj->OwnerPhoneId;

        if(count($AddObj) > 0) {
            $Arr = array_merge($AddObj, $Arr ); // совпадающие поля будут заменены из таблицы Objects ($Arr)
        }

        /*
        $Arr['OwnerUserId']   = $Obj->OwnerUserId;
        $Arr['ObjectType']    = $Obj->ObjectType;//GetObjectTypeNameById($Obj->ObjectType);

        $Arr['ObjectAgeType'] = $Obj->ObjectAgeType;
        $Arr['DealType']      = $Obj->DealType;
        $Arr['Price']         = $Obj->Price;
        $Arr['RoomsCount']    = $Obj->RoomsCount;
        $Arr['RoomsSell']     = $Obj->RoomsSell;
        $Arr['PartsTotal']    = $Obj->PartsTotal;
        $Arr['PartsSell']     = $Obj->PartsSell;

        $Arr['Street']        = $Obj->Street;
        $Arr['HouseNumber']   = $Obj->HouseNumber;
        $Arr['Floor']         = $Obj->Floor;
        $Arr['Floors']        = $Obj->Floors;
        $Arr['MetroStation1Id']=$Obj->MetroStation1Id;
        $Arr['MetroWayMinutes']=$Obj->MetroWayMinutes;
        $Arr['MetroWayType']  = $Obj->MetroWayType;
        $Arr['SquareAll']     = $Obj->SquareAll;
        $Arr['SquareLiving']  = $Obj->SquareLiving;
        $Arr['SquareKitchen'] = $Obj->SquareKitchen;

        $Arr['ObjectCondition']=$Obj->ObjectCondition;
        $Arr['Balcon']        = $Obj->Balcon;
        $Arr['Toilet']        = $Obj->Toilet;
        $Arr['WindowView']    = $Obj->WindowView;
        $Arr['Lift']          = $Obj->Lift;
        $Arr['Telephone']     = $Obj->Telephone;
        $Arr['Territory']     = $Obj->Territory;
        $Arr['Garbage']       = $Obj->Garbage;
        $Arr['Parking']       = $Obj->Parking;
        $Arr['BuildingType']  = $Obj->BuildingType;
        $Arr['Description']   = $Obj->Description;
        $Arr['SobPhone']      = $Obj->SobPhone;
        $Arr['SobName']       = $Obj->SobName;
        $Arr['Flooring']      = $Obj->Flooring;
        $Arr['Mortgage']      = $Obj->Mortgage;
        $Arr['Currency']      = $Obj->Currency;
*/
        // а нужно ли детализировать каждое поле??
        /*if($Obj->ObjectType == 'city') {

        } elseif($Obj->ObjectType == 'country') {
            $Arr['HighwayId']      = $Obj->HighwayId;
            $Arr['']      = $Obj->;
        } else {

        }*/

        return $Arr;
    }

    function MakeObjectAdditionsArrByObj($Obj) {
        $Arr = array();
        $Arr['LoadedObjectId']  = $Obj->id;   // важный параметр для extjs функций
        $Arr['SiteTitle']       = $Obj->SiteTitle;
        $Arr['SiteKeywords']    = $Obj->SiteKeywords;
        $Arr['SiteDescription'] = $Obj->SiteDescription;
        $Arr['SiteVideo']       = $Obj->SiteVideo;
        $Arr['RealtyType']      = $Obj->RealtyType;
        return $Arr;
    }