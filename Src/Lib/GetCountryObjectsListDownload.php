<?php
    // Список объектов в главной таблице ///////////
    // TODO Как выбирать записи с пустыми (null) ст Метро? - IFNULL!
    // REPLACE(FORMAT - изменить запятую на точку - sql strreplace
    // REPLACE( REPLACE(FORMAT(o.Price, 3),'.000',''), ',', '.') AS Price
    $Params['NoLimit']      = true;
    $Params['RealtyType']   = 'country';
    $Params['OnlyUserId']   = @$_REQUEST['OnlyUserId'];
    $SQLParams  = GetSqlParamsForGetObjectsList($Params);
    $SqlQuery   = MakeGetObjectsListSql($SQLParams);//echo $SqlQuery;
    $res        = mysql_query($SqlQuery);

    $objPHPExcel = new PHPExcel();
    $objPHPExcel->setActiveSheetIndex(0);
    $StrCount= 1;
    $ArrStep = 0;
    $CaptArr = array(   '№', 'Добавлен', 'Тип', 'Шоссе', 'Нас.пункт', 'Улица', 'км от МКАД', 'Участок сот.',
        'Дом м2', 'Цена', 'Выгрузка на сайт', 'Выгрузка в Winner', 'Выгрузка в Cian',
        'Выгрузка в Cian Premium', 'Выгрузка в Avito', 'Выгрузка в Navig', 'Выгрузка в RBC', 'Сотрудник');
    // строим заголовочки (count($CaptArr) не должно превышать 26 )
    for($AsciiCode=65; $AsciiCode <= 65+count($CaptArr)-1; $AsciiCode++) {
        $objPHPExcel->getActiveSheet()->SetCellValue( chr($AsciiCode).$StrCount, $CaptArr[$ArrStep] );
        $ArrStep++;
    }

    while($str  = mysql_fetch_object($res)) {
        $StrCount++;
        $ArrStep = 0;
        $TarifId = GetObjectAdTarifArr($str->id, true); // список порталов куда выгружается объект
        if(isset($TarifId[8])) { $TarifId[8] = 'S';}
        if(isset($TarifId[1])) { $TarifId[1] = 'W';}
        if(isset($TarifId[2])) { $TarifId[2] = 'C';}
        if(isset($TarifId[3])) { $TarifId[3] = 'CP';}
        if(isset($TarifId[5])) { $TarifId[5] = 'A';}
        if(isset($TarifId[7])) { $TarifId[7] = 'N';}
        if(isset($TarifId[9])) { $TarifId[9] = 'R';}
        // в $ValArr места соответствуют местам в $CaptArr
        //$element['Currency']     = GetCurrencyNameById($str->Currency, true);
        //if(strlen($str->PlaceTypeSocr) > 0) {$socr = ' (' . $str->PlaceTypeSocr . ')'; } else {$socr = '';}
        $Prms             = array();
        $Prms['Data']     = $str;
        $Prms['CopyData'] = true;
        $ObjData          = ExtendObjectProperties($Prms);

        $ValArr = array($ObjData->id,   $ObjData->AddedDate,    $ObjData->ObjectTypeName, $ObjData->DirectionName, $ObjData->City,
            $ObjData->Street,     $ObjData->Distance,  $ObjData->LandSquare,   $ObjData->SquareAll,
            $ObjData->Price,
            @$TarifId[8],@$TarifId[1],@$TarifId[2],@$TarifId[3],@$TarifId[5],@$TarifId[7],@$TarifId[9],
            $ObjData->FIO
        );

        for($AsciiCode=65; $AsciiCode <= 65+count($ValArr)-1; $AsciiCode++) {
            $ColumnIndex  = chr($AsciiCode).$StrCount;
            $ColumnLetter = chr($AsciiCode);
            $objPHPExcel->getActiveSheet()->SetCellValue($ColumnIndex , @$ValArr[$ArrStep] );
            $objPHPExcel->getActiveSheet()->getColumnDimension( $ColumnLetter )->setAutoSize(true); // авто ширина колонки
            $ArrStep++;
        }

    }

    $objPHPExcel->getProperties()->setCreator($CONF['XlsCreator']);
    $objPHPExcel->getProperties()->setLastModifiedBy($CONF['XlsCreator']);
    $objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Document");
    $objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Document");
    $objPHPExcel->getProperties()->setDescription("Document for Office 2007 XLSX");

    $objPHPExcel->getActiveSheet()->setTitle('Лист 1');

    $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);

    // вывод данных
    header ( "Expires: Mon, 1 Apr 1974 05:00:00 GMT" );
    header ( "Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT" );
    header ( "Cache-Control: no-cache, must-revalidate" );
    header ( "Pragma: no-cache" );
    header ( "Content-type: application/vnd.ms-excel" );
    header ( "Content-Disposition: attachment; filename=CountryObjects.xls" );

    $objWriter->save('php://output');
