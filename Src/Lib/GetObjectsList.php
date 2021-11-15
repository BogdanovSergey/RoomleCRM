<?php
    // Список объектов в главной таблице ///////////
    // TODO Как выбирать записи с пустыми (null) ст Метро? - IFNULL!
    // REPLACE(FORMAT - изменить запятую на точку - sql strreplace
    // REPLACE( REPLACE(FORMAT(o.Price, 3),'.000',''), ',', '.') AS Price
    $Params['RealtyType'] = 'city';
    $Params['OnlyUserId'] = @$_REQUEST['OnlyUserId'];

    $SQLParams  = GetSqlParamsForGetObjectsList($Params); // NB! GetSummOfObjects() - Ниже
    $SqlQuery   = MakeGetObjectsListSql($SQLParams);
    $res        = mysql_query($SqlQuery);
    $CycleCount = 0; // контрольный счетчик для проверки соответствия между "select query" объектов и "select count query"
    while($str = mysql_fetch_object($res)) {
        // High Load, Watch out!
        $CycleCount++;
        $Prms             = array();
        $Prms['Data']     = $str;
        $Prms['CopyData'] = true;
        $Prms['InArray']  = true;
        $ObjData          = ExtendObjectProperties($Prms);
        $element          = $ObjData;
        $element['checkbox'] = 0;
        array_push($out, $element);
    }
// TODO не совсем правильно отдавать всегда все поля! например, при просмотре списка, description не нужен.
    $response               = (object) array();
    $response->success      = true;
    //$response->message      = "Loaded data";
    $response->data         = $out;
    $Prms['ActiveType']     = $_REQUEST['Active'];
    $Prms['RealtyType']     = $Params['RealtyType']; // тип взят из статической тбл RealtyTypes
    $Prms['OnlyUserId']     = $Params['OnlyUserId'];
    $TotalCount             = GetSummOfObjects($Prms); // все виды объектов, [не]активные
    $response->total        = $TotalCount;

    // проверка правильности вывода селектов (при больших объемах $CycleCount разделяется на порции и != Total)
    if( ( $CycleCount > $TotalCount ) || ($CycleCount < 10 && $TotalCount > 10) ) {
        $msg = __FILE__." Несоответствие в выборке объектов между 'SELECT *' и 'SELECT COUNT(): \$CycleCount=$CycleCount, \$TotalCount = $TotalCount";
        $GLOBALS['FirePHP']->error($msg);
        MainFatalLog($msg);
    }
    // вывод данных
    header("Content-Type: application/json;charset=UTF-8");
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
