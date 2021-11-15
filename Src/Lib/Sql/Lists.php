<?php

    function MakeObjectsOrderString($Property, $Direction) { //#COLUMNSORTING
        $SQL['ORDER'] = "";
        switch($Property) {
            case 'id':
                $SQL['ORDER'] = "ORDER BY o.id $Direction";
                break;
            case 'AddedDate':
                $SQL['ORDER'] = "ORDER BY o.AddedDate $Direction";
                break;
            case 'ArchivedDate':
                $SQL['ORDER'] = "ORDER BY o.ArchivedDate $Direction";
                break;
            case 'RoomsCount':
                $SQL['ORDER'] = "ORDER BY o.RoomsCount $Direction";  // ,o.RoomsSell ?
                break;
            case 'City':
                $SQL['ORDER'] = "ORDER BY o.City $Direction";
                break;
            case 'Distance':
                $SQL['ORDER'] = "ORDER BY o.Distance $Direction";
                break;
            case 'LandSquare':
                $SQL['ORDER'] = "ORDER BY o.LandSquare $Direction";
                break;
            case 'SquareLiving':
                $SQL['ORDER'] = "ORDER BY o.SquareLiving $Direction";
                break;
            case 'Raion':
                $SQL['ORDER'] = "ORDER BY o.Raion $Direction";
                break;
            case 'Metro':
                $SQL['ORDER'] = "ORDER BY m.StationName $Direction";
                break;
            case 'Street':
                $SQL['ORDER'] = "ORDER BY o.Street $Direction";
                break;
            case 'Floors':
                $SQL['ORDER'] = "ORDER BY o.Floor $Direction, o.Floors $Direction";
                break;
            case 'Squares':
                $SQL['ORDER'] = "ORDER BY o.SquareAll $Direction, o.SquareLiving $Direction, o.SquareKitchen $Direction";
                break;
            case 'Price':
                $SQL['ORDER'] = "ORDER BY o.Price $Direction";
                break;
            case 'Agent':
                $SQL['ORDER'] = "ORDER BY FIO $Direction";
                break;
            case 'ImagesCount':
                $SQL['ORDER'] = "ORDER BY ImagesCount $Direction";
                break;
            case 'DirectionName':
                $SQL['ORDER'] = "ORDER BY DirectionName $Direction";
                break;
            case 'DealTypeName':
                $SQL['ORDER'] = "ORDER BY DealType $Direction";
                break;
            case 'RoomTypeName':
                $SQL['ORDER'] = "ORDER BY CommerceRoomTypeId $Direction";
                break;
            case 'CommerceObjectTypeName':
                $SQL['ORDER'] = "ORDER BY CommerceObjectTypeId $Direction";
                break;
            case 'CountryObjectTypeName':
                $SQL['ORDER'] = "ORDER BY ObjectType $Direction";
                break;
            case 'ObjectTypeName':
                $SQL['ORDER'] = "ORDER BY ObjectType $Direction";
                break;
            case 'PricePeriodName':
                $SQL['ORDER'] = "ORDER BY CommercePricePeriodId $Direction";
                break;
            case 'PriceTypeName':
                $SQL['ORDER'] = "ORDER BY CommercePriceTypeId $Direction";
                break;
            case 'ObjectBrandName':
                $SQL['ORDER'] = "ORDER BY ObjectBrandName $Direction";
                break;
        }
        //$GLOBALS['FirePHP']->info($sql);
        return $SQL['ORDER'];
    }

    function MakeObjectsLimitString($Page, $Start, $Limit) {
        $SQL['LIMIT'] = 'LIMIT 0,100';      // по умолчанию ( NB! в буферизировнном гриде указан шаг в 100)

        //if(@$Page >= 0 && @$Start >= 0 && @$Limit >= 0) {
        if( isset($Page) && isset($Start) && isset($Limit) ) {
            $SQL['LIMIT'] = "LIMIT ".$Start.",".$Limit;  // пользователь нажал пагинацию
        }
        $GLOBALS['FirePHP']->info(__FUNCTION__."(): ".$SQL['LIMIT']);
        return $SQL['LIMIT'];
    }

    function GetSqlParamsForGetObjectsList($Params) {
        // Проверяем, настраиваем server-side сортировку
        global $CURRENT_USER;
        $SQL          = array();
        $SQL['ORDER'] = 'ORDER BY o.id DESC';           // сортировка по-умолачнию

        $SQL['Active']= 'o.Active = 1';                 // по-умолчанию показываем только рабочие
        if( isset($_REQUEST['Active']) && $_REQUEST['Active'] == 0 ) {
            $SQL['Active']= 'o.Active = 0';             // Запрос на архивные объекты
            if( !isset($_REQUEST['sort']) ) {
                $SQL['ORDER'] = "ORDER BY o.ArchivedDate DESC"; // во вкладке архив по-умолчанию сортируем по дате удаления
            }
        } else {
            $_REQUEST['Active'] = 1;                    // Грубо фиксируем параметр для подсчета суммы-total объектов
        }
        //// важна последовательность --->

        if( @$_REQUEST['OnlyUserId'] > 0 ) {
            // установлен фильтр на конкретного пользователя (парное правило с Objects-All-ShowOnlyMine)
            $SQL['WHERE'] = "o.OwnerUserId = {$_REQUEST['OnlyUserId']} AND ";
        } else {

            if (CheckMyRule('Objects-All-ShowOnlyMine')) {     // разрешено смотреть только свои объекты (парное правило с OnlyUserId)
                $SQL['WHERE'] = "o.OwnerUserId = {$CURRENT_USER->id} AND ";

            } elseif (CheckMyRule('Objects-LimitByOwnGroup')) {  // разрешено смотреть объекты только моего отдела
                $SQL['WHERE'] = "o.OwnerUserId IN (" . implode(",", $CURRENT_USER->MyGroupUserIdsArr) . ") AND ";
            }

            if ( CheckMyRule('Objects-All-Manage') && CheckMyRule('Objects-LimitByOwnGroup') ) {
                // Приоритет в ограничении всех объектов отдела
                $SQL['WHERE'] = "o.OwnerUserId IN (".implode(",", $CURRENT_USER->MyGroupUserIdsArr).") AND ";

            } else if ( CheckMyRule('Objects-All-Manage') && CheckMyRule('Objects-All-ShowOnlyMine') ) {
                // приоритет во всех
                $SQL['WHERE'] = "";
            }

        }
/*
        if( Chec/kMyRule('Objects-All-Manage') && !CheckMyRule('Objects-LimitByOwnGroup') ) {
            // управление всеми объектами без ограничения отдела
            $SQL['WHERE'] = "";

        } else if ( CheckMyRule('Objects-All-Manage') && CheckMyRule('Objects-LimitByOwnGroup') ) {
            // управление всеми объектами отдела
            $SQL['WHERE'] = "o.OwnerUserId IN (".implode(",", $CURRENT_USER->MyGroupUserIdsArr).") AND ";
        }
*/

        //// <---- важна последовательность

        if( isset($_REQUEST['sort']) ) { // входящий sort выглядит так: [{"property":"Metro","direction":"ASC"}]
            $SortObj = json_decode(@$_REQUEST['sort']); // подготавливаем направление сортрировки //#COLUMNSORTING
            $SortObj = $SortObj[0];
            // TODO преобразованные поля надо очистить ?
            $SQL['ORDER'] = MakeObjectsOrderString($SortObj->property, $SortObj->direction);
        }
        if(isset($Params['NoLimit'])) {
            $SQL['LIMIT'] = '';
        } else {
            $SQL['LIMIT'] = MakeObjectsLimitString(@$_REQUEST['page'], @$_REQUEST['start'], @$_REQUEST['limit']);
        }
        if(isset($Params['RealtyType'])) {
            $SQL['RealtyType'] = $Params['RealtyType'];
        } else {
            echo 'RealtyType musthave!';exit;
        }

        return $SQL;
    }

    function MakeGetObjectsListSql($SQL, $GetCount = false) {
        // TODO перенести сюда COUNT
        if(!isset($SQL['SELECT']))  { $SQL['SELECT'] = ''; }    // init
        if(!isset($SQL['FROM']))    { $SQL['FROM']   = ''; }
        if(!isset($SQL['WHERE']))   { $SQL['WHERE']  = ''; }

        if($SQL['RealtyType'] == 'city') {
            $SQL['SELECT']  .= 'm.StationName AS MetroStation, ';    // метро
            $SQL['SELECT']  .= 'm.StationName AS Metro, ';    // колонка в гриде
            $SQL['FROM']    .= 'MetroStations AS m, ';
            $SQL['WHERE']   .= 'o.MetroStation1Id = m.id AND ';
        } elseif($SQL['RealtyType'] == 'country') {
            $SQL['SELECT']  .= 'hw.DirectionName AS DirectionName, ';
            $SQL['FROM']    .= 'ObjectHighways AS hw, ';
            $SQL['WHERE']   .= 'o.HighwayId = hw.id AND ';
        } elseif($SQL['RealtyType'] == 'commerce') {
            $SQL['SELECT']  .= 'm.StationName AS MetroStation, ';    // метро
            $SQL['SELECT']  .= 'm.StationName AS Metro, ';    // ???
            $SQL['SELECT']  .= 'ot.TypeName AS CommerceObjectTypeName, ';    // ???

            $SQL['FROM']    .= 'MetroStations AS m, ';
            $SQL['WHERE']   .= 'o.MetroStation1Id = m.id AND ';

            //$SQL['SELECT']  .= 'op.ParamValue AS DealTypeName, ';
            //$SQL['FROM']    .= 'ObjectParams AS op, ';          // для названий типа сделки и др параметров.
            //$SQL['WHERE']   .= 'op.id = o.DealType AND ';
            $SQL['SELECT']  .=  'o.DealType AS DealTypeId,                 (SELECT op.ParamValue FROM ObjectParams AS op WHERE op.id = DealTypeId) AS DealTypeName, '.
                                'o.CommerceRoomTypeId AS RoomTypeId,       (SELECT op.ParamValue FROM ObjectParams AS op WHERE op.id = RoomTypeId) AS RoomTypeName, '.
                                'o.CommerceObjectTypeId AS ObjectTypeId,   (SELECT op.ParamValue FROM ObjectParams AS op WHERE op.id = ObjectTypeId) AS ObjectTypeName, '.
                                'o.CommercePricePeriodId AS PricePeriodId, (SELECT op.ParamValue FROM ObjectParams AS op WHERE op.id = PricePeriodId) AS PricePeriodName, '.
                                'o.CommercePriceTypeId AS PriceTypeId,     (SELECT op.ParamValue FROM ObjectParams AS op WHERE op.id = PriceTypeId) AS PriceTypeName, ';

        } else {

        }
/*
        if( @$Params['OnlyUserId'] > 0 ) {
            // установлен фильтр на конкретного пользователя
            $SQL['WHERE'] = "OwnerUserId = {$Params['OnlyUserId']} AND ";
        }
        if( CheckMyRule('Objects-All-ShowOnlyMine') ) {     // разрешено смотреть только свои объекты
            $SQL['WHERE'] = "OwnerUserId = {$CURRENT_USER->id} AND ";

        } elseif( CheckMyRule('Objects-LimitByOwnGroup') ) {  // разрешено смотреть объекты только моего отдела
            $SQL['WHERE'] = "OwnerUserId IN (".implode(",", $CURRENT_USER->MyGroupUserIdsArr).") AND ";
        }
        */

        if($GetCount) {     // Подготовить только сумму
            $Content = "
                    COUNT(o.id) AS TotalCount
            ";
            // очищаем
            $SQL['LIMIT'] = ''; // убираем лимит, т.к. запрошена сумма всех записей. Нужно для листания грида
            $SQL['ORDER'] = '';

        } else {            // Берем содержание
            $Content = "
                    {$SQL['SELECT']}
                    o.*,
                    DATE_FORMAT(o.AddedDate,'%d %M %Y %H:%i') AS AddedDate,
                    o.id AS id,
                    CONCAT_WS('', u.LastName, ' ', u.FirstName) AS FIO,
                    CONCAT_WS('', o.Street, ' ', o.HouseNumber) AS StreetHouse,
                    CONCAT_WS('', o.Floor, '/', o.Floors) AS Floors,
                    CONCAT_WS('', o.SquareAll, '/', o.SquareLiving, '/', o.SquareKitchen) AS Squares,
                    REPLACE(FORMAT(o.Price, 3), '.000', '') AS Price,
                    ot.TypeName AS ObjectTypeName
            ";

        }
        $out = "# MakeGetObjectsListSql()
                SELECT
                    $Content
                FROM
                    {$SQL['FROM']}
                    Objects AS o,
                    Users AS u,
                    ObjectTypes AS ot
                WHERE
                    {$SQL['WHERE']}
                    {$SQL['Active']} AND
                    o.RealtyType = '{$SQL['RealtyType']}' AND
                    u.id = o.OwnerUserId AND
                    o.ObjectType = ot.id
                {$SQL['ORDER']}
                {$SQL['LIMIT']}"; //LIMIT 0,10
        $GLOBALS['FirePHP']->info($out);//echo $out;
        return $out;
    }

    function GetSummOfObjects2($SQLParams) {
        $sql = MakeGetObjectsListSql($SQLParams, true);
        $res = mysql_query($sql);
        $Obj = mysql_fetch_object($res);
        return $Obj->TotalCount;
    }