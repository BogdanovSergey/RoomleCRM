<?php

    $Params = array();
    ($_REQUEST['Active'] == 1) ? $Params['Active'] = 1 : $Params['Active'] = 0; // активные или архивные?
    if( isset($_REQUEST['sort']) ) { // входящий sort выглядит так: [{"property":"Metro","direction":"ASC"}]
        $SortObj = json_decode(@$_REQUEST['sort']); // подготавливаем направление сортрировки
        $SortObj = $SortObj[0];
        $Params['OrderByField'] = $SortObj->property;
        $Params['OrderByTo']    = $SortObj->direction;
    } else {
        $Params['OrderByField'] = 'LastName';
        $Params['OrderByTo']    = 'DESC';
    }
    $Params['NoHidden']     = true; // не показывать скрытых
    //if( CheckMyRule('Users-LimitByOwnGroup') ) {     // разрешено смотреть сотрудников только своего отдела
    //    $Params['LimitByGroupIdsArr']    = $CURRENT_USER->GroupIdsArr; #USERGROUPARR
    //}
    $Params['OnlyUserId'] = $CURRENT_USER->id; // Смотрим только своих...
    $ClientsArr = GetClientsArr($Params);

    $objPHPExcel = new PHPExcel();
    $objPHPExcel->setActiveSheetIndex(0);
    //Set a default style for the entire workbook
    //$objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    //$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
    $StrCount= 1;
    $ArrStep = 0;
    $CaptArr = array(   '№', 'Добавлен', 'Фамилия', 'Имя','Отчество', 'День рождения', 'Основной моб. номер',
        'Email', 'Альт. моб. №1', 'Альт. моб. №2', 'Адрес объекта', 'Описание');
    // строим заголовочки (count($CaptArr) не должно превышать 26 )
    for($AsciiCode=65; $AsciiCode <= 65+count($CaptArr)-1; $AsciiCode++) {
        $objPHPExcel->getActiveSheet()->SetCellValue( chr($AsciiCode).$StrCount, $CaptArr[$ArrStep] );
        $ArrStep++;
    }

    foreach($ClientsArr as $ClientObj) {
        $StrCount++;
        $ArrStep = 0;
        // в $ValArr места соответствуют местам в $CaptArr
        $Prms             = array();
        $Prms['Data']     = $ClientObj;
        $Prms['CopyData'] = true;
        $ClientData       = ExtendUserProperties($Prms);

        $ValArr = array($ClientData->id,   $ClientData->AddedDate,        $ClientData->LastName,
            $ClientData->FirstName, $ClientData->SurName,  $ClientData->Birthday,      $ClientData->MobilePhone,
            $ClientData->Email,   $ClientData->MobilePhone1,
            $ClientData->MobilePhone2, $ClientData->ObjectLocation, $ClientData->Description
        );

        for($AsciiCode=65; $AsciiCode <= 65+count($ValArr)-1; $AsciiCode++) {
            $ColumnIndex  = chr($AsciiCode).$StrCount;
            $ColumnLetter = chr($AsciiCode);
            $objPHPExcel->getActiveSheet()->SetCellValue($ColumnIndex , @$ValArr[$ArrStep] );
            $objPHPExcel->getActiveSheet()->getColumnDimension( $ColumnLetter )->setAutoSize(true); // авто ширина колонки
            $ArrStep++;
        }
/*
        $element                = array();
        $element['id']          = $ClientObj->id;
        $element['AddedDate']   = $ClientObj->AddedDate;
        $element['ArchivedDate']= $ClientObj->ArchivedDate;
        $element['FirstName']   = $ClientObj->FirstName;
        $element['LastName']    = $ClientObj->LastName;
        $element['MobilePhone'] = $ClientObj->MobilePhone;

        $element['Email']        = $ClientObj->Email;
        $element['MobilePhone1'] = $ClientObj->MobilePhone1;
        $element['MobilePhone2'] = $ClientObj->MobilePhone2;

        array_push($out, $element);*/
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
    header ( "Content-Disposition: attachment; filename=Users.xls" );

    $objWriter->save('php://output');

