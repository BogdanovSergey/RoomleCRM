<?php

//    if( isset($_SERVER['HTTP_HOST']) ) { exit;} // сервисы должны запускаться только из консоли
    require('Conf/Config.php');
    //require('Lib/Sql/Lists.php');
    //connectToDB('CrmAdminDb'); // create new db link: $GLOBALS['DBConn']["CrmAdminDb"]
    DBConnect();
    $SYS['OlimpCorpPhone'] = '84955186635';
    $SYS['OlimpUserIdWithCorpPhone'] = '74';
    $SYS['ObjectCity'] = 'Москва';
    $SYS['ObjectRegion'] = 'Москва';
    $SYS['ObjectPlaceType'] = 'город';
    $SYS['ObjectPlaceTypeSocr'] = 'г';
    //InitAuthentication();

    include 'Mods/PHPExcel/PHPExcel.php';

    //$GLOBALS['DBObj'] = new Database();

    ProcessXlsxFile( './xls.xlsx' );

    function ProcessXlsxFile($FilePath) {
        global $SYS;
        try {
            $objPHPExcel = PHPExcel_IOFactory::load($FilePath);
        } catch(PHPExcel_Reader_Exception $e) {
            $msg = "Error loading file ({$FilePath}): ".$e->getMessage();
            echo $msg;
            die($msg);
        }
        $AddedObjectsCount      = 0;
        $PassedStringsCount     = 0;
        $PassedNonUniqueObjects = 0;
        $BadWorkLog     = '';
        $GoodWorkLog    = '';
        $HeaderSign     = 'Дата' ;
        $FatalError     = null;
        $ColJumper      = 0;    //через сколько столбцов перепрыгнуть (если во вторичке появляются столбцы "Сколько идти/Как идти")

        $objReader = new PHPExcel_Reader_Excel2007();
        //$objReader->setReadDataOnly(false);
        $objPHPExcel = $objReader->load($FilePath);

        $objPHPExcel->setActiveSheetIndex(0);
        $aSheet = $objPHPExcel->getActiveSheet()->toArray('',true,null,null);
        $HeaderRow  = null;
        $CurRowNo   = 0;
        $ObjectType = null; // тип файла с собственниками
        $ColIndex   = array();
        foreach($aSheet as $row) {
            $CurRowNo++;

            if($CurRowNo==1){ continue;} // эта строка - заголовок, перескакиваем на следующую
            //if($CurRowNo>10) { exit; }

            //echo "$CurRowNo\n";
            /*
                ---------- тип файла в_Москве_и_Новой_Москве_продать_11-07-16_16-42-45.xlsx ----------
                $row[0] - кол-во комнат (3)                                     - RoomsCount
                $row[1] - метро (Парк Победы м.) (Говорово д.)                  - MetroStation1Id ???
                $row[2] - мин от метро и тип (5т)                               - MetroWayMinutes  MetroWayType
                $row[3] - улица - (Мосфильмовская ул. Капотня 3-й кв-л)         - Street
                $row[4] - № дома - (70К4)                                      - HouseNumber
                $row[5] - Этаж/этажность Тип дома - (14/20 М-К) (3/4 Стал.)     - Floor/Floors,  BuildingType - тбл ObjectParams:BuildingType->WinnerMark без дефиса, Стал. = 49
                $row[6] - Площадь - (132/104/15) (40/21.2/12.2)                 - SquareAll/SquareLiving/SquareKitchen
                $row[7] - Б (3Л Б)                                              - ObjectParams: Balcon -> WinnerMark
                $row[8] - Т (Т)                                                 - ObjectParams: Telephone -> WinnerMark
                $row[9] - С (3С 2Р)                                             - ObjectParams: Toilet
                $row[10] - П (п/д ЛМ)                                           - ObjectParams: Flooring

                $row[11] - И (- +)                                              - ObjectParams: Mortgage

                $row[12] - Цена в ед. (59 000 000)                              - Price без пробелов
                $row[13] - Ед.цены (руб $ )                                     - Currency руб = 70, $ = 71
                $row[14] - Агент (АН Олимп / АН Олимп, Марина)                  - не нужно |определить по тбл: Users-FirstName
                $row[15] - Отправлено (2016-07-11 10:02:02)
                                было раньше: $row[] - W (<img src="/winner6/img/eye.png" style="cursor:pointer" onclick="openSobCard(this);" guid="085FFAA5-3A41-0003-7C0D-0000127D0000" onmouseover="showCardTooltip(this, event);" longName="Посмотреть на Sob.ru"/>)
                $row[16] - Опубликовано (2016-07-11 10:04:38)
                $row[17] - Продана (продается/арендуется)
                $row[18] - Телефоны (8-495-518-6635, 8-903-201-4710)            - если схож с корп номером AddCorpPhone =1, номер агента ищем в тбл: Users: MobilePhone, MobilePhone1, MobilePhone2
                                                                                  вставляем UserId в OwnerUserId и OwnerPhoneId
                $row[19] - Примечаение                                          - Description

             */

            preg_match_all("/(\d+)(.)/u", $row[2], $arr);
            $MetroWayMinutes = @$arr[1][0];

            $MWT['п'] = 1;
            $MWT['т'] = 2;
            $MetroWayType = @$MWT[ $arr[2][0] ];

            $MetroStation1Id = GetMetroStationIdByName(preg_replace("/ м./", '', $row[1]));

            //$row[5] - Этаж/этажность Тип дома - (14/20 М-К) (3/4 Стал.)     - Floor/Floors,  BuildingType - тбл ObjectParams:BuildingType->WinnerMark без дефиса, Стал. = 49
            preg_match_all('#(\d+)/(\d+)\s(.+)#u', $row[5], $fl);
            $Floor          = $fl[1][0];
            $Floors         = $fl[2][0];
            $BuildingType   = $fl[3][0];
            // параметры для xml не соответствуют тем, которые находятся в экспортном xls. Фильтруем
            $BuildingType   = preg_replace('/-/','',$BuildingType);
            $BuildingType   = preg_replace('/Стал./','С',$BuildingType);
            $BuildingTypeId = GetObjectParamIdByColumn($BuildingType, 'BuildingType', 'WinnerMark');

            //$row[6] - Площадь - (132/104/15) (40/21.2/12.2)                 - SquareAll/SquareLiving/SquareKitchen
            // иногда бывают не заполненны поля
            preg_match_all('#^([\d.]+)/.+#u', $row[6], $square);
            $SquareAll      = @$square[1][0];
            preg_match_all('#.+/([\d.]+)/.+#u', $row[6], $square);
            $SquareLiving   = @$square[1][0];
            preg_match_all('#.+/([\d.]+)$#u', $row[6], $square);
            $SquareKitchen  = @$square[1][0];

            // $row[7] - Б (3Л Б)                                              - ObjectParams: Balcon -> WinnerMark
            $BalconId = GetObjectParamIdByColumn($row[7], 'Balcon', 'WinnerMark');

            //$row[8] - Т (Т)                                                 - ObjectParams: Telephone -> WinnerMark
            $TelephoneId = GetObjectParamIdByColumn($row[8], 'Telephone', 'WinnerMark');

            //$row[9] - С (3С 2Р)                                             - ObjectParams: Toilet
            $ToiletId = GetObjectParamIdByColumn($row[9], 'Toilet', 'WinnerMark');

            //$row[10] - П (п/д ЛМ)                                           - ObjectParams: Flooring
            $FlooringId = GetObjectParamIdByColumn($row[10], 'Flooring', 'WinnerMark');

            //$row[11] - И (- +)                                              - ObjectParams: Mortgage
            $MortgageId = GetObjectParamIdByColumn($row[11], 'Mortgage', 'WinnerMark');

            //$row[12] - Цена в ед. (59 000 000)                              - Price без пробелов
            $price = preg_replace('/[^\d]/u', '', $row[12]);

            //$row[13] - Ед.цены (руб $ )                                     - Currency руб = 70, $ = 71
            $Currency['руб'] = 70;
            $Currency['$']   = 71;

            //$row[18] - Телефоны (8-495-518-6635, 8-903-201-4710)            - если схож с корп номером AddCorpPhone =1, номер агента ищем в тбл: Users: MobilePhone, MobilePhone1, MobilePhone2
            // вставляем UserId в OwnerUserId и OwnerPhoneId
            $phones         = preg_split('/, /u', $row[18]);
            $AddCorpPhone   = 0;
            $UserId         = null;
            $p1 = preg_replace('/[^\d]/u', '', $phones[0]);
            $p2 = @preg_replace('/[^\d]/u', '', $phones[1]);
            if($p1 == $SYS['OlimpCorpPhone']) { $AddCorpPhone=1; } else { $UserId = SearchUserIdByPhone($p1);  } //echo "1UserId: '$UserId' ";
            if($p2 == $SYS['OlimpCorpPhone']) { $AddCorpPhone=1; }
            if(strlen($UserId)<=0) { $UserId = SearchUserIdByPhone($p2);  } // echo "2UserId: '$UserId' ";
            if(strlen($UserId)<=0) { $UserId = $SYS['OlimpUserIdWithCorpPhone']; }

            //$row[19] - Примечаение                                          - Description
            $Description = mysql_real_escape_string( @$row[19] );


            $sql = "INSERT INTO Objects
                    (AddedDate, Color,    RealtyType, ObjectType, ObjectAgeType, DealType, RoomsCount,

                    MetroStation1Id,
                    MetroWayMinutes,
                    MetroWayType,
                    Street,
                    HouseNumber,

                    Region,
                    City,
                    PlaceType,
                    PlaceTypeSocr,

                    Floor,
                    Floors,
                    BuildingType,

                    SquareAll,
                    SquareLiving,
                    SquareKitchen,

                    Balcon,
                    Telephone,
                    Toilet,
                    Flooring,
                    Mortgage,
                    Price,
                    Currency,

                    OwnerUserId,
                    AddCorpPhone,
                    Description
                    )
                  VALUES
                    (NOW(), 'LightYellow',
                    'city',   # городская
                    1,        # квартира
                    56,       # вторичка
                    58,       # прямая продажа
                    '{$row[0]}',   # RoomsCount

                    '$MetroStation1Id',  #MetroStation1Id: {$row[1]}
                    '$MetroWayMinutes',  #MetroWayMinutes: {$row[2]}
                    '$MetroWayType',
                    '{$row[3]}',       # Street
                    '{$row[4]}',       # HouseNumber

                    '{$SYS['ObjectRegion']}',
                    '{$SYS['ObjectCity']}',
                    '{$SYS['ObjectPlaceType']}',
                    '{$SYS['ObjectPlaceTypeSocr']}',

                    '{$Floor}',        #Floor: {$row[5]}
                    '{$Floors}',       #Floors
                    '{$BuildingTypeId}',     #BuildingType: $BuildingType

                    '{$SquareAll}',    # SquareAll: {$row[6]}
                    '{$SquareLiving}',    # SquareLiving
                    '{$SquareKitchen}',    # SquareKitchen

                    '{$BalconId}',     # Balcon: $row[7]
                    '{$TelephoneId}',     # Telephone: $row[8]
                    '{$ToiletId}',     # Toilet: $row[9]
                    '{$FlooringId}',   # FlooringId: $row[10]
                    '{$MortgageId}',   # MortgageId: $row[11]
                    '{$price}',      # Price: $row[12]
                    '{$Currency[$row[13]]}',     # Currency: $row[13]

                    '{$UserId}',     # UserId: Phones: {$row[18]} # $p1 $p2
                    '{$AddCorpPhone}',     # AddCorpPhone: $AddCorpPhone
                    '{$Description}'
                    );

                    ";
            echo $sql;
//            $res = mysql_query($sql) or die(mysql_error());





        } // end of rows in current file
        echo date('r')."<br>\n";
        echo "Processed {$CurRowNo} rows in excel file.\n";


    }


