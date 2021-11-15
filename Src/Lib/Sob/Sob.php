<?php


function LoadJsonSobList( $PostVars, $ExceptLastHours=0) {
    $SQLQueryParts  = array();
    $response       = null;
    $SimpleArr      = array();
    $AllowXlsExport = true; // маркер запрещающий открывать кнопку экспорта в эксель
    $SortObj = json_decode(@$_REQUEST['sort']); // подготавливаем направление сортрировки
    $SortObj = $SortObj[0];

    $page                 = mysql_real_escape_string($PostVars['page'], $GLOBALS['DBConn']['DBAntiposrednik']); // get the requested page
    $limit                = mysql_real_escape_string($PostVars['limit'], $GLOBALS['DBConn']['DBAntiposrednik']); // get how many rows we want to have into the grid
    $PostVars['OrderBy']  = @$SortObj->property;
    $PostVars['ord']      = @$SortObj->direction;
    $PostVars['SobListNeededDate'] = @mysql_real_escape_string($PostVars['SobListNeededDate'], $GLOBALS['DBConn']['DBAntiposrednik']);
    $PostVars['StreetSearchField'] = @mysql_real_escape_string($PostVars['StreetSearchField'], $GLOBALS['DBConn']['DBAntiposrednik']);


    // нужно ли скрыть последние объекты (для бесплатников)
    if($ExceptLastHours > 0) {
        $SQLQueryParts['ExceptHours'] = " AND o.AddedDate < NOW() - INTERVAL $ExceptLastHours HOUR ";

    } else {
        $SQLQueryParts['ExceptHours'] = '';
    }

    // sorting
    if($PostVars['OrderBy'] == 'FlatType') {
        $SQLQueryParts['ORDER'] = 'ORDER BY o.FlatType ';
    } elseif($PostVars['OrderBy'] == 'Metro') {
        $SQLQueryParts['ORDER'] = 'ORDER BY o.Metro ';
    } elseif($PostVars['OrderBy'] == 'Address') {
        $SQLQueryParts['ORDER'] = 'ORDER BY o.Address ';
    } elseif($PostVars['OrderBy'] == 'Floors') {
        $SQLQueryParts['ORDER'] = 'ORDER BY o.Floor ';
    } elseif($PostVars['OrderBy'] == 'Square') {
        $SQLQueryParts['ORDER'] = 'ORDER BY o.Square ';
    } elseif($PostVars['OrderBy'] == 'Price') {
        $SQLQueryParts['ORDER'] = 'ORDER BY o.Price ';
    } elseif($PostVars['OrderBy'] == 'AddedDate') {
        $SQLQueryParts['ORDER'] = 'ORDER BY o.AddedDate ';
    } else {
        $SQLQueryParts['ORDER'] = 'ORDER BY o.Metro '; // default sort
    }
    if($PostVars['ord'] == 'ASC') {
        $SQLQueryParts['ORDER'] .= 'ASC';
    } else {
        $SQLQueryParts['ORDER'] .= 'DESC';
    }

    // detect needed date
    if(preg_match('/\d{4}-\d{2}-\d{2}/', $PostVars['SobListNeededDate'])) {
        $SQLQueryParts['WHERE'] = "DATE(o.AddedDate) = DATE('{$PostVars['SobListNeededDate']}')";
    } elseif($PostVars['SobListNeededDate'] == 1) {
        // за весь период
        $AllowXlsExport = false;
        $SQLQueryParts['WHERE'] = '1';
    } else {
        $SQLQueryParts['WHERE'] = "DATE(o.AddedDate) = CURRENT_DATE() ";
    }

    if(strlen($PostVars['StreetSearchField']) >= 3) {
        $SQLQueryParts['WHERE'] .= " AND Address LIKE '%{$PostVars['StreetSearchField']}%' ";
    }


    ///////////////////////////////////////////////////////////
    // count current results (needed for JQGrid scrolling)
    $sql = "SELECT COUNT(o.id) AS c FROM Objects AS o WHERE {$SQLQueryParts['WHERE']} {$SQLQueryParts['ExceptHours']} AND Active=1";
    $GLOBALS['FirePHP']->info($sql);

    $result = SQLQuery($sql, $GLOBALS['DBConn']['DBAntiposrednik']);
    $Obj = mysql_fetch_object($result);
    $count = $Obj->c;
    if( $count > 0 ) {
        $total_pages = ceil($count/$limit);
    } else {
        $total_pages = 0;
    }
    if ($page > $total_pages) { $page=$total_pages; }
    $start = $limit*$page - $limit; // do not put $limit*($page - 1)
    if(($start*1) < 0) {$start = 0;}
    @$response->page = $page; // no need to init
    @$response->total = $total_pages;
    @$response->records = $count;


    // get real data
    $sql = "SELECT
                  o.*,
                  REPLACE(FORMAT(o.Price, 3),'.000','') AS Price
                FROM
                  Objects AS o
                WHERE
                  {$SQLQueryParts['WHERE']}
                  {$SQLQueryParts['ExceptHours']} AND
                  Active = 1
                {$SQLQueryParts['ORDER']}
                LIMIT {$start} , {$limit}";
    $GLOBALS['FirePHP']->info($sql);
    $res = SQLQuery($sql, $GLOBALS['DBConn']['DBAntiposrednik']);
    $i=0;
    while($Obj = mysql_fetch_object($res)) {
        //$Obj->Phone = preg_replace('/(\d{3})(\d{3})(\d{2})(\d{2})/', "($1) $2-$3-$4", $Obj->Phone);
        $response->rows[$i]['id'] = $Obj->id;
        if(strlen($PostVars['StreetSearchField']) >= 3) { // делаем подсветку при поиске по улицам
            $Obj->Address = preg_replace("/({$PostVars['StreetSearchField']})/iu", "<b>$1</b>", $Obj->Address);
        }
        // подготавливаем телефонные номера
        $Obj->Phone  = FormatPhoneNumber($Obj->Phone);
        if($Obj->Phone2) {
            $Obj->Phone  .= ', ' .FormatPhoneNumber($Obj->Phone2); }
        if($Obj->Phone3) {
            $Obj->Phones  .= ', ' .FormatPhoneNumber($Obj->Phone3);}

        /////
        $element                = array();
        $element['id']          = $Obj->id;
        $element['AddedDate']   = $Obj->AddedDate;
        $element['Color']       = '';
        $element['FlatType']    = $Obj->FlatType;
        $element['Metro']       = $Obj->Metro;
        $element['Address']     = $Obj->Address;
        $element['Floors']      = $Obj->Floor;
        $element['Square']      = $Obj->Square;
        $element['Price']       = $Obj->Price;
        $element['Phone']       = $Obj->Phone;
        //$element['']          = $Obj->;

        // TODO SobReady будем показывать всем?
        array_push($SimpleArr, $element );
        /*$response->rows[$i]['cell'] = array(
            $Obj->id, $Obj->SobReady, $Obj->FlatType, $Obj->Metro, $Obj->Address, $Obj->Floor, $Obj->Square, $Obj->Price, $Obj->Phone,
            $Obj->AddedDate
        );*/
        $i++;
    }
    return array($SimpleArr, $count, $AllowXlsExport);
    //return $response;
}


function LoadJsonAutocomleteList($UserId, $PostVars, $ExceptLastHours) {
    $Part = mysql_real_escape_string($PostVars['q'], $GLOBALS['DBConn']['DBAntiposrednik']); // get the requested page

    // нужно ли скрыть последние объекты (для бесплатников)
    if($ExceptLastHours > 0) {
        $SQLQueryParts['ExceptHours'] = " AND o.AddedDate < NOW() - INTERVAL $ExceptLastHours HOUR ";
    } else {
        $SQLQueryParts['ExceptHours'] = '';
    }

    // get real data
    $sql = "SELECT
                  Address
                FROM
                  Objects AS o
                WHERE
                  Address LIKE '%$Part%'
                  {$SQLQueryParts['ExceptHours']} AND
                  Active = 1
                LIMIT 0 , 10";
    $GLOBALS['FirePHP']->info($sql);
    $res = SQLQuery($sql, $GLOBALS['DBConn']['DBAntiposrednik']);
    $i=0;
    while($Obj = mysql_fetch_object($res)) {
        $response->rows[$i]['id'] = $Obj->id;
        if(strlen($PostVars['StreetSearchField']) >= 3) { // делаем подсветку при поиске по улицам
            $Obj->Address = preg_replace("/({$PostVars['StreetSearchField']})/iu", "<b>$1</b>", $Obj->Address);
        }
        $response->rows[$i]['cell'] = array(
            $Obj->id, $Obj->FlatType, $Obj->Metro, $Obj->Address, $Obj->Floor, $Obj->Square, $Obj->Price,
            $Obj->AddedDate
        );
        $i++;
    }
    return $response;
}

function SobView($PostVars) {
    $out   = '';
    $SobId = mysql_real_escape_string($PostVars['SobId'], $GLOBALS['DBConn']['DBAntiposrednik']);
    $sql = "SELECT
                  *
                FROM
                  Objects
                WHERE
                  id = {$SobId} AND
                  Active = 1";
    $res = SQLQuery($sql, $GLOBALS['DBConn']['DBAntiposrednik']);
    $Obj = mysql_fetch_object($res);
    $DescrString   = '<div class="OVRow"><div class="OVCol OVW100">'.$Obj->About.'</div></div>';
    $out .= '
                <div class="ObjectViewDiv">
                    <div class="ObjectViewTable">
                        <div class="ui-state-default OVRow">
                            <div class="OVCol">Описание объекта</div>
                        </div>
                        <div class="OVRow">
                            <div class="OVCol OVW10">'.$Obj->Address.'</div>
                        </div>
                    </div>
                    <div class="ObjectViewTable">
                        '.$DescrString.'
                    </div>
                </div>
                <br>
                ';

    return $out;
}

    function GetCommentsListBySobId($SobId) {
        $out = '';
        $sql = "SELECT
                  *,
                  DATE_FORMAT(sc.AddedDate,'%d.%m.%Y %H:%i') AS AddedDate
                FROM
                  SobComments as sc
                WHERE
                  sc.ObjectId = $SobId
                ORDER BY sc.AddedDate DESC";
        $res = SQLQuery($sql, $GLOBALS['DBConn']['CrmDb']);
        $GLOBALS['FirePHP']->info( $sql );
        while($str = @mysql_fetch_object($res)) {
            $userObj = User_GetUserObj($str->UserId);
            $out .= "{$str->AddedDate} {$userObj->LastName}: {$str->CommentText}<br>";
        }
        return $out;
    }


function GetSobObjInJson($SobId) {
    global $CONF;
    global $WORDS;
    global $CURRENT_USER;
    $out        = '';
    $response   = null;
    $SobId      = mysql_real_escape_string($SobId, $GLOBALS['DBConn']['DBAntiposrednik']);
    $sql        = "SELECT
                      *, REPLACE(FORMAT(o.Price, 3),'.000','') AS Price,
                      TO_DAYS(o.UpdatedDate) - TO_DAYS(o.AddedDate) AS DaysPassed
                    FROM
                      Objects AS o
                    WHERE
                      o.id = {$SobId} AND
                      o.Active = 1";
    $res = SQLQuery($sql, $GLOBALS['DBConn']['DBAntiposrednik']);
    $Obj = mysql_fetch_object($res);
    if(strlen($Obj->Metro)>1) { $Metro = "<br>м. {$Obj->Metro}"; } else { $Metro = null; }
    @$response->Address     = $Obj->Address .  $Metro . "<br>Этажность: {$Obj->Floor}, Площадь: {$Obj->Square}<br>Цена: {$Obj->Price}";
    $response->SobReady     = $Obj->SobReady;
    //TODO Нижеследующее выделение нужно делать при сохранении, а не каждый раз при просмотре!
    $response->About        = preg_replace($CONF['SobReadyWordsArr'], '<b>$1</b>', $Obj->About);
    //if(!$response->SobReady) {
    //    $response->About = "В тексте встречается риэлторский лексикон (<b>выделено</b>):<br>" . $response->About;
    //}
    $response->Metro        = $Obj->Metro;
    $response->Phones       = FormatPhoneNumber($Obj->Phone);
    //$response->PhonesPure   = $Obj->Phone; // для просмотра номера в яндексе
    $response->ViewCount    = $Obj->ViewCount;
    $response->CommentsList = GetCommentsListBySobId($SobId);

    //if($CURRENT_USER->tarif == 'baza') {
        // выдача доп инфы платникам
        $response->SourceType   = $Obj->SourceType;
        $response->Link         = $Obj->Link;
        $response->DateAdded    = $Obj->AddedDate;
        if(isset($Obj->UpdatedDate)) {
            $response->DateUpdated  = $Obj->UpdatedDate;
        }
        if($Obj->DaysPassed >= 1) {
            $response->DaysPassed   = $Obj->DaysPassed; // сколько дней прошло с момента добавления в базу до последней рекламы
        }
    /*} else {
        // бесплатники
        $response->Link         = $WORDS['Object']['SourceForFreeUsers'];
    }*/

    if($Obj->Phone2) {
        $response->Phones  .= ', ' .FormatPhoneNumber($Obj->Phone2);
        $response->PhonesPure .= ' ' . $Obj->Phone2; }
    if($Obj->Phone3) {
        $response->Phones  .= ', ' .FormatPhoneNumber($Obj->Phone3);
        $response->PhonesPure .= ' ' . $Obj->Phone3; }

    return $response;
}

function TabSobDateChooser($ExceptLastHours) {
    $out   = '';
    // нужно ли скрыть последние объекты (для бесплатников)
    if($ExceptLastHours > 0) {
        $SQLQueryParts['ExceptHours'] = "o.AddedDate < NOW() - INTERVAL $ExceptLastHours HOUR AND ";

    } else {
        $SQLQueryParts['ExceptHours'] = '';
    }
    $sql = "SELECT
                    DATE(AddedDate) AS D, COUNT(id) AS Sobs
                FROM
                    Objects AS o
                WHERE
                    {$SQLQueryParts['ExceptHours']}
                    Active = 1 AND
                    AddedDate BETWEEN
                      DATE_SUB(DATE(NOW()), INTERVAL 7 DAY)
                    AND
                      DATE_ADD(DATE(NOW()), INTERVAL 1 DAY)
                GROUP BY DATE(AddedDate)
                ORDER BY AddedDate DESC";
    $res = SQLQuery($sql, $GLOBALS['DBConn']['DBAntiposrednik']);

    while($str = mysql_fetch_object($res)) {
        $out .= "<option value=\"{$str->D}\">{$str->D} ({$str->Sobs})</option>";

    }
    $AllPeriod = GetTotalSobs();
    $out = "<select id=\"TabSobDateSelector\" class=\"ui-widget ui-state-default ui-corner-all\">
                <option value=\"0\">за сегодня</option>
                <option value=\"1\">за всё время ({$AllPeriod})</option>{$out}</select>";
    return $out;
}


function OwnersDate() {
    $SimpleArr = array();
    $sql = "SELECT
                DATE(AddedDate) AS D, COUNT(id) AS Sobs
            FROM
                Objects AS o
            WHERE
                Active = 1 AND
                AddedDate BETWEEN
                  DATE_SUB(DATE(NOW()), INTERVAL 7 DAY)
                AND
                  DATE_ADD(DATE(NOW()), INTERVAL 1 DAY)
            GROUP BY DATE(AddedDate)
            ORDER BY AddedDate DESC";
    $res = SQLQuery($sql, $GLOBALS['DBConn']['DBAntiposrednik']);

    while($str = mysql_fetch_object($res)) {
        //$out .= "<option value=\"{$str->D}\">{$str->D} ({$str->Sobs})</option>";


        $element               = array();
        $element['Date']       = $str->D;
        $element['Text']       = "{$str->D} ({$str->Sobs})";

        // TODO SobReady будем показывать всем?
        array_push($SimpleArr, $element );

    }
    $AllPeriod = GetTotalSobs(1);
    $element               = array();
    $element['Date']       = '1';
    $element['Text']       = "за весь период ($AllPeriod)";
    array_push($SimpleArr, $element );
    /*or\" class=\"ui-widget ui-state-default ui-corner-all\">
                <option value=\"0\">за сегодня</option>
                <option value=\"1\">за всё время ({$AllPeriod})</option>{$out}</select>";*/
    return $SimpleArr;
}

function GetTotalSobs($Date = false) {
    $GLOBALS['FirePHP']->info(__FUNCTION__."($Date)");
    if($Date == 1) {
        // запрос на ВСЕ записи
        $sql = "SELECT
                    COUNT(Objects.id) AS c
                FROM
                    Objects
                WHERE Active=1";
    } elseif($Date) {
        // запрос на конкретную дату
        $sql = "SELECT
                    COUNT(Objects.id) AS c
                FROM
                    Objects
                WHERE
                  DATE(Objects.AddedDate) = DATE('{$Date}') AND
                  Active=1
                ";
    } else {
        //ничего - показываем сегодня
        $sql = "SELECT
                    COUNT(Objects.id) AS c
                FROM
                    Objects
                WHERE
                    DATE(Objects.AddedDate) = CURRENT_DATE() AND
                    Active=1
                ";
    }
    $GLOBALS['FirePHP']->info($sql);
    $res = SQLQuery($sql, $GLOBALS['DBConn']["DBAntiposrednik"] );
    $row = mysql_fetch_object($res);
    return $row->c;
}

function GetSobsArrForLastDay($Limit = 100) {
    $SobArr     = array();
    $LimitPatch = '';
    if($Limit > 0) {
        $LimitPatch = "LIMIT 0, $Limit";
    }
    $sql = "SELECT
                AddedDate,Address,FlatType,Metro,Square,Floor,About,Phone,
                REPLACE(FORMAT(Objects.Price, 3),'.000','') AS Price
            FROM
                Objects
            WHERE
                AddedDate >= NOW() - INTERVAL 1 DAY AND
                Active = 1
            ORDER BY FlatType,Metro ASC
            $LimitPatch";
    $res = SQLQuery($sql, $GLOBALS['DBConn']['DBAntiposrednik']);

    //$i=0;
    while($str = mysql_fetch_object($res)) {
        $SobArr[] = $str;
        //$i++;
    }
    return $SobArr;
}

function OwnersSaveComment($SobObjectId, $UserId, $CommentText) {
    $out = false;
    if($SobObjectId > 0 && $UserId>0 && strlen($CommentText)>0) {
        $CommentText = mysql_real_escape_string($CommentText);
        $sql = "INSERT INTO
                    SobComments (AddedDate, ObjectId, UserId, CommentText)
                VALUES
                    (NOW(), {$SobObjectId}, {$UserId}, '{$CommentText}')";
        $GLOBALS['FirePHP']->info($sql);
        SQLQuery($sql, $GLOBALS['DBConn']['CrmDb']);
        $out = true;
    }
    return $out;
}

function ClickSobObject($UserId, $ObjectId, $ViewType) {
    // фиксируем просмотр
    $sql = "INSERT INTO
                    ObjectsViewCount
                    (ViewDate, UserId, ObjectId, ViewType)
                VALUES
                    (NOW(), {$UserId}, {$ObjectId}, '{$ViewType}')";
    SQLQuery($sql, $GLOBALS['DBConn']['DBAntiposrednik']);

    // инкрементируем просмотр в самом объекте
    $sql = "UPDATE
                    Objects
                SET
                    ViewCount = ViewCount + 1
                WHERE
                    id = {$ObjectId}";
    SQLQuery($sql, $GLOBALS['DBConn']['DBAntiposrednik']);

    /*/ инкрементируем просмотр у пользователя
    $sql = "UPDATE
                    Clients
                SET
                    ObjectsViewCount = ObjectsViewCount + 1
                WHERE
                    id = {$UserId}";
    SQLQuery($sql, $GLOBALS['DBConn']['DBClients']);*/
}

function FormatPhoneNumber($PhoneNumber) {
    $out = $PhoneNumber;
    if(strlen($PhoneNumber) == 10) {
        $out = preg_replace("/(\d{3})(\d{3})(\d{2})(\d{2})/","($1) $2-$3-$4",$PhoneNumber);
    }
    return $out;
}