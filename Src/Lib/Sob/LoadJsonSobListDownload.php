<?php

    function LoadJsonSobListDownload($SobObjArr) {
        global $CONF;
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        //Set a default style for the entire workbook
        //$objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $StrCount= 1;
        $ArrStep = 0;
        $CaptArr = array(   'AddedDate', 'FlatType', 'Metro', 'Address', 'Floors', 'Square', 'Price', 'Phone');
        // строим заголовочки (count($CaptArr) не должно превышать 26 )
        for($AsciiCode=65; $AsciiCode <= 65+count($CaptArr)-1; $AsciiCode++) {
            $objPHPExcel->getActiveSheet()->SetCellValue( chr($AsciiCode).$StrCount, $CaptArr[$ArrStep] );
            $ArrStep++;
        }

        foreach($SobObjArr as $SobObj) {
            $StrCount++;
            $ArrStep = 0;
            $ValArr = array($SobObj['AddedDate'], $SobObj['FlatType'],
                $SobObj['Metro'],         $SobObj['Address'],
                $SobObj['Floors'],   $SobObj['Square'],
                $SobObj['Price'],   $SobObj['Phone']
            );

            for($AsciiCode=65; $AsciiCode <= 65+count($ValArr)-1; $AsciiCode++) { //echo $ValArr[$ArrStep];
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
        header ( "Content-Disposition: attachment; filename=Sob.xls" );

        $objWriter->save('php://output');

    }
