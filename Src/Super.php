<?php
    require('Conf/Config.php');
    require('Lib/Sql/Lists.php');
    connectToDB('CrmAdminDb'); // create new db link: $GLOBALS['DBConn']["CrmAdminDb"]
    DBConnect();

    InitAuthentication();

    $count  = 0;
    $out    = array();

    switch(@$_REQUEST['Action']) {
        case 'UserLogin' :
            // действие отрабатывается в \Lib\Kernel\Authentication.php:InitAuthentication()
            break;

        case 'UserLogout':
            header("Content-Type: application/json;charset=UTF-8");
            if(LogOut()) {
                echo '{"success":true}';
            } else {
                echo '{"success":false,"message":"system error in LogOut()"}';
            }
            break;

        case 'KladrQuery' :
            // TODO - переделать все под Sphinx!
            if(isset($_REQUEST['KladrRaion'])) {
                // подготовка выборки по району
                /* Район - это: (р-н, мкр) // TODO правильно ли? сверить с ya,cian,win,av ? */
                $text  = mysql_real_escape_string($_REQUEST['KladrRaion']);
                $sql = "SELECT
                            k.*,
                            LOWER(KladrSocr.SOCRNAME) AS SOCRNAME
                        FROM
                            KladrMain AS k,
                            KladrSocr
                        WHERE
                            (k.SOCR = 'р-н' OR k.SOCR = 'мкр' ) AND
                            k.SOCR = KladrSocr.SCNAME AND
                            k.NAME LIKE '{$text}%'
                        GROUP BY k.NAME
                        ORDER BY k.NAME
                        LIMIT 0,50";

                $res = mysql_query($sql);
                $GLOBALS['FirePHP']->info($sql);
                while($str = mysql_fetch_object($res)) {
                    $count++;
                    $element            = array();
                    $element['Name']   = $str->NAME . ' ' . $str->SOCRNAME;
                    array_push($out, $element);
                }
            } elseif(isset($_REQUEST['KladrRegion'])) {
                /* Регион - это: (Респ, Аобл, обл, АО, край) // TODO правильно ли? сверить с ya,cian,win,av ? */
                $text  = mysql_real_escape_string($_REQUEST['KladrRegion']);
                $sql = "SELECT
                            k.*,
                            LOWER(KladrSocr.SOCRNAME) AS SOCRNAME
                        FROM
                            KladrMain AS k,
                            KladrSocr
                        WHERE
                            (k.SOCR = 'Респ' OR k.SOCR = 'Аобл' OR k.SOCR = 'обл' OR k.SOCR = 'АО' OR k.SOCR = 'край') AND
                            k.SOCR = KladrSocr.SCNAME AND
                            k.NAME LIKE '{$text}%'
                        GROUP BY k.CODE
                        ORDER BY k.NAME
                        LIMIT 0,50";

                $res = mysql_query($sql);
                $GLOBALS['FirePHP']->info($sql);
                while($str = mysql_fetch_object($res)) {
                    $count++;
                    $element            = array();
                    $element['Name']   = $str->NAME . ' ' . $str->SOCRNAME;
                    array_push($out, $element);
                }

            } elseif(isset($_REQUEST['KladrStreet'])) {
                // подготовка выборки по улицам
                $text  = mysql_real_escape_string($_REQUEST['KladrStreet']);
                $count = 0;
                $out = array();

                $sql = "SELECT
                            KladrStreet.*, LCASE(KladrSocr.SOCRNAME) AS SOKR
                        FROM
                            KladrStreet,
                            KladrSocr
                        WHERE
                            KladrStreet.NAME LIKE '{$text}%' AND
                            KladrStreet.SOCR = KladrSocr.SCNAME
                        GROUP BY CONCAT(KladrStreet.NAME, KladrStreet.SOCR)
                        LIMIT 0,50";
                $res = mysql_query($sql);
                $GLOBALS['FirePHP']->info($sql);
                while($str = mysql_fetch_object($res)) {
                    $count++;
                    $element            = array();
                    $element['Name']   = $str->NAME . ' ' . $str->SOCR; // . '.'
                    array_push($out, $element);
                }
            } elseif(isset($_REQUEST['KladrCity'])) {
                // подготовка выборки по городам
                $text  = mysql_real_escape_string($_REQUEST['KladrCity']);
                $count = 0;
                $out = array();

                $sql = "
                SELECT
                    k.*,
                    LOWER(KladrSocr.SOCRNAME) AS SOCRNAME,
                    KladrSocr.SCNAME
                FROM
                    KladrMain AS k,
                    KladrSocr
                WHERE
                    k.CODE BETWEEN 0000000000000 AND 9999999999999 AND
                    k.SOCR = KladrSocr.SCNAME AND
                    k.NAME LIKE '{$text}%'
                GROUP BY CONCAT(k.NAME)
                ORDER BY k.NAME
                LIMIT 0,50";
                $res = mysql_query($sql);
                $GLOBALS['FirePHP']->info($sql);
                while($str = mysql_fetch_object($res)) {
                    $count++;
                    $element           = array();
                    $element['Name']   = $str->NAME;
                    $element['Socr']   = $str->SOCRNAME;
                    array_push($out, $element);
                }

            } elseif(isset($_REQUEST['KladrPlaceType'])) {
                // подготовка выборки типов населенных пунктов (без округов, районов и т.д.)
                $text  = mysql_real_escape_string($_REQUEST['KladrPlaceType']);
                $sql = "
                    SELECT
                        DISTINCT(SOCRNAME) AS SOCRNAME
                    FROM
                        KladrSocr
                    WHERE
                        SOCRNAME LIKE '{$text}%' AND
                        CityLike = 1 # сделано вручную
                    ORDER BY SOCRNAME
                    LIMIT 0,50";
                $res = mysql_query($sql);
                $GLOBALS['FirePHP']->info($sql);
                while($str = mysql_fetch_object($res)) {
                    $count++;
                    $element           = array();
                    $element['Name']   = $str->SOCRNAME;
                    array_push($out, $element);
                }
            } else {
                echo "err: empty \$_REQUEST var";
            }
            // вывод данных
            header("Content-Type: application/json;charset=UTF-8");
            echo json_encode($out, JSON_UNESCAPED_UNICODE);
            break;

        case 'CheckCityInRegionInAvitoLib':
            $RegionName  = mysql_real_escape_string($_REQUEST['RegionName']);
            $CityName    = mysql_real_escape_string($_REQUEST['CityName']);
            $MskOblastStr  = 'Московская область';
            if(strlen($RegionName) < 1 && strlen($CityName) < 1) { break; }
            if($RegionName == 'Москва' && $CityName == 'Москва') {
                $result = true;
            } else {
                ( CheckCityInRegionInAvitoLib($RegionName, $CityName) ) ? $result = true : $result = false;
            }
            $element             = array();
            $element['CityExist']= $result;
            header("Content-Type: application/json;charset=UTF-8");
            echo json_encode($element, JSON_UNESCAPED_UNICODE);
            break;

        case 'GetAvitoCitiesArrByRegion':
            $ChosenRegion  = mysql_real_escape_string(@$_REQUEST['ChosenRegion']);
            $ChosenCity    = mysql_real_escape_string(@$_REQUEST['ChosenCity']);
            $MskOblastStr  = 'Московская область';
            if(strlen($ChosenRegion) < 1 && strlen($ChosenCity) < 1) { break; }
            if($ChosenRegion == 'Москва') { $ChosenRegion = $MskOblastStr; } // совместимость с сохраненными объектами

            $Params             = array();
            $Params['InJson']   = true;
            $Params['CityName'] = $ChosenCity;
            if(strlen($ChosenRegion) > 0) {               // указана область
                if($ChosenRegion == $MskOblastStr) {      // запрос по Москве, показываем подмосковные города
                    $out = GetAvitoCitiesArrByRegion($MskOblastStr, $Params);
                } else {                                // не московский регион
                    $out = GetAvitoCitiesArrByRegion($ChosenRegion, $Params);
                }
            } else {                                    // запрос по Москве, показываем подмосковные города
                $out = GetAvitoCitiesArrByRegion($MskOblastStr, $Params);
            }
            header("Content-Type: application/json;charset=UTF-8");
            echo json_encode($out, JSON_UNESCAPED_UNICODE);
            break;

        case 'GetObjectFormParams' :
            if(isset($_REQUEST['GetAgents'])) {
                $Params = array();
                $Params['Active']       = @$_REQUEST['Active'];
                $Params['ActiveObjects']= @$_REQUEST['ActiveObjects'];
                $Params['RealtyType']   = @$_REQUEST['RealtyType'];
                $Params['HideZeroUsers']= @$_REQUEST['HideZeroUsers'];
                $Params['WithSumm']     = false;
                if(CheckMyRule('Objects-All-ShowOnlyMine')) {
                    $Params['OnlyUserId'] = $CURRENT_USER->id; // разрешено смотреть только свои объекты

                } elseif( CheckMyRule('Objects-LimitByOwnGroup') ) {  // разрешено смотреть объекты только моего отдела
                    $Params['LimitByGroupIdsArr']     = $CURRENT_USER->GroupIdsArr;

                }
                if( CheckMyRule('Objects-All-Manage') && !CheckMyRule('Objects-LimitByOwnGroup') ) {
                    // управление всеми объектами без ограничения отдела
                    $Params['OnlyUserId'] = null;
                    $Params['LimitByGroupIdsArr'] = null;

                } else if ( CheckMyRule('Objects-All-Manage') && CheckMyRule('Objects-LimitByOwnGroup') ) {
                    // управление всеми объектами отдела
                    $Params['OnlyUserId'] = null;
                    $Params['LimitByGroupIdsArr']     = $CURRENT_USER->GroupIdsArr;
                }


                $Count                    = '';
                //$Params['OrderByField'] =
                //$Params['OrderByTo']    =
                if(isset($_REQUEST['WithSumm'])) {
                    $Params['WithSumm'] = true;
                }
                $arr = GetAgentsArr($Params);
                foreach($arr as $AgentObj) {
                    $element            = array();
                    $element['id']      = $AgentObj->id;
                    if($Params['WithSumm']) {
                        if( isset($AgentObj->UserObjectsSumm) ) { $Count = " ({$AgentObj->UserObjectsSumm})"; } else { $Count = " (0)"; }
                    }
                    if( isset($_REQUEST['OnlyFio']) ) {
                        $element['VarName'] = $AgentObj->LastName . ' ' . $AgentObj->FirstName . $Count;
                    } else {
                        ($AgentObj->MobilePhone) ? $str = $AgentObj->MobilePhone . ' - ' : $str = '';
                        $element['VarName'] = $str . $AgentObj->LastName . ' ' . $AgentObj->FirstName . $Count;
                    }
                    array_push($out, $element);
                }

            } elseif(isset($_REQUEST['GetObjectType'])) {
                $arr = GetObjectTypesArr(1); // берем только типы из городской недвижимости (тбл ObjectTypes.id = 1)
                foreach($arr as $TypesObj) {
                    $element            = array();
                    $element['id']      = $TypesObj->id;
                    $element['VarName'] = $TypesObj->TypeName;
                    $element['VarData'] = '';
                    array_push($out, $element);
                }

            } elseif(isset($_REQUEST['GetMetroStations'])) {
                $arr = GetMetroStationsArr();
                foreach($arr as $TypesObj) {
                    $element            = array();
                    $element['id']      = $TypesObj->id;
                    $element['VarName'] = $TypesObj->StationName;
                    $element['VarData'] = '';
                    array_push($out, $element);
                }
            } elseif(isset($_REQUEST['GetRealtyTypes'])) {
                $arr = GetRealtyTypesArr();
                foreach($arr as $TypesObj) {
                    $element            = array();
                    $element['id']      = $TypesObj->id;
                    $element['VarName'] = $TypesObj->TypeName;
                    $element['VarData'] = $TypesObj->Description;
                    array_push($out, $element);
                }
            } elseif(isset($_REQUEST['GetCommerceObjectTypeList'])) {
                require("Lib/Objects/CommerceObjectFuncs.php");
                $arr = GetCommerceObjectTypeListArr();

                foreach ($arr as $TypesObj) {
                    $element = array();
                    $element['id'] = $TypesObj->id;
                    $element['VarName'] = $TypesObj->TypeName;
                    $element['VarData'] = '';
                    array_push($out, $element);
                }
            } elseif(isset($_REQUEST['GetObjectOwnerPhones'])) {
                $out = GetObjectOwnerPhonesArr($_REQUEST['ObjectOwnerId']); //@$_REQUEST['ObjectId'];

            } else {

            }

            header("Content-Type: application/json;charset=UTF-8");
            //echo '{"success":true,"data":' . json_encode($out, JSON_UNESCAPED_UNICODE) . '}';
            //echo '[{"id":1,"VarName":"89031245531 - Богданов Сергей"},{"id":2,"VarName":"81234567890 - Иванов Иван"}]';
            echo json_encode($out, JSON_UNESCAPED_UNICODE);
          break;


        case 'GetGroupsList' :
            $Params             = array();
            $Params['Active']   = 1;
            $arr                = GetGroupsArr($Params);
            foreach($arr as $GroupObj) {
                $element            = array();
                $element['id']      = $GroupObj->id;
                $element['GroupName'] = $GroupObj->GroupName;
                //$element['VarData'] = '';
                array_push($out, $element);
            }
            header("Content-Type: application/json;charset=UTF-8");
            echo json_encode($out, JSON_UNESCAPED_UNICODE);
            break;

        case 'GetPositionsList' :
            $Params             = array();
            $Params['Active']   = 1;
            $arr                = GetPositionsOrGroupsObjArr('position', $Params);
            foreach($arr as $PosObj) {
                $element            = array();
                $element['id']      = $PosObj->id;
                $element['PositionName'] = $PosObj->PositionName;
                //$element['VarData'] = '';
                array_push($out, $element);
            }
            header("Content-Type: application/json;charset=UTF-8");
            echo json_encode($out, JSON_UNESCAPED_UNICODE);
            break;

        case 'GetStatusesList' :
            $Params             = array();
            $Params['Active']   = 1;
            $arr                = GetUserStatusesArr(null, $Params);
            $element            = array();
            $element['id']      = "0";
            $element['StatusName'] = ' ';
            array_push($out, $element);
            foreach($arr as $PosObj) {
                $element['id']         = $PosObj->id;
                $element['StatusName'] = $PosObj->StatusName;
                array_push($out, $element);
            }
            header("Content-Type: application/json;charset=UTF-8");
            echo json_encode($out, JSON_UNESCAPED_UNICODE);
            break;

        case 'SaveCityObjectForm':
            if($_REQUEST['LoadedObjectId'] < 1 && CheckMyRule('Objects-All-Create')) {
                // создание объекта
                require("Lib/Objects/Go_SaveCityObjectForm.php");

            } elseif( $_REQUEST['LoadedObjectId'] >= 1 && (CheckMyRule('Objects-All-Manage') || CheckMyRule('Objects-My-Manage') ) ) {
                // Редактирование всех или своих. Перекрывает Ред-е спец полей.
                require("Lib/Objects/Go_SaveCityObjectForm.php");

            } elseif( $_REQUEST['LoadedObjectId'] >= 1 && CheckMyRule('Objects-My-EditSpecial')  && $_REQUEST['EditSpecial']) {
                // редактир-е некоторых полей
                require("Lib/Objects/Go_SaveCityObjectForm.php");

            } else {
                // todo check
                //require("Lib/Objects/Go_SaveCityObjectForm.php");
                DenyRuleAlert(array('Objects-All-Create','Objects-My-Manage','Objects-All-Manage','Objects-My-EditSpecial'));
            }
            break;

        case 'SaveCountryObjectForm' :
            if($_REQUEST['LoadedObjectId'] < 1 && CheckMyRule('Objects-All-Create')) {
                // создание объекта
                require("Lib/Objects/Go_SaveCountryObjectForm.php");

            } elseif( $_REQUEST['LoadedObjectId'] >= 1 && (CheckMyRule('Objects-All-Manage') || CheckMyRule('Objects-My-Manage') ) ) {
                // Редактирование всех или своих. Перекрывает Ред-е спец полей.
                require("Lib/Objects/Go_SaveCountryObjectForm.php");

            } elseif( $_REQUEST['LoadedObjectId'] >= 1 && CheckMyRule('Objects-My-EditSpecial')  && $_REQUEST['EditSpecial']) {
                // редактир-е некоторых полей
                require("Lib/Objects/Go_SaveCountryObjectForm.php");

            } else {
                DenyRuleAlert(array('Objects-All-Create','Objects-My-Manage','Objects-All-Manage','Objects-My-EditSpecial'));
            }
            break;

        case 'SaveCommerceObjectForm':
            if($_REQUEST['LoadedObjectId'] < 1 && CheckMyRule('Objects-All-Create')) {
                // создание объекта
                require("Lib/Objects/Go_SaveCommerceObjectForm.php");

            } elseif( $_REQUEST['LoadedObjectId'] >= 1 && (CheckMyRule('Objects-All-Manage') || CheckMyRule('Objects-My-Manage') ) ) {
                // Редактирование всех или своих. Перекрывает Ред-е спец полей.
                require("Lib/Objects/Go_SaveCommerceObjectForm.php");

            } elseif( $_REQUEST['LoadedObjectId'] >= 1 && CheckMyRule('Objects-My-EditSpecial')  && $_REQUEST['EditSpecial']) {
                // редактир-е некоторых полей
                require("Lib/Objects/Go_SaveCommerceObjectForm.php");

            } else {
                DenyRuleAlert(array('Objects-All-Create','Objects-My-Manage','Objects-All-Manage','Objects-My-EditSpecial'));
            }
            break;

        case 'SaveAdditionsObjectForm':
            if(CheckMyRule('Objects-My-Manage') || CheckMyRule('Objects-All-Manage')) {
                require("Lib/Objects/Go_SaveAdditionsObjectForm.php");
            } else {
                DenyRuleAlert(array('Objects-My-Manage', 'Objects-All-Manage'));
            }
            break;

        case 'GetObjectsList':
            AppendCURRENT_USERWithAdvancedData(); // дополнить CURRENT_USER user_id'шниками со всех моих отделов
            if(@$_REQUEST['DownloadType']) {
                // Create new PHPExcel object
                include('Mods/PHPExcel/PHPExcel.php');
                include('Mods/PHPExcel/PHPExcel/Writer/Excel2007.php');
                // start downloading
                require("Lib/Go_GetObjectsListDownload.php");
            } else {
                require("Lib/Go_GetObjectsList.php");
            }
            break;

        case 'GetCountryObjectsList' :
            AppendCURRENT_USERWithAdvancedData(); // дополнить CURRENT_USER user_id'шниками со всех моих отделов
            if(@$_REQUEST['DownloadType']) {
                // Create new PHPExcel object
                include('Mods/PHPExcel/PHPExcel.php');
                include('Mods/PHPExcel/PHPExcel/Writer/Excel2007.php');
                // start downloading
                require("Lib/Go_GetCountryObjectsListDownload.php");
            } else {
                require("Lib/Go_GetCountryObjectsList.php");
            }
            break;

        case 'GetCommerceObjectsList' :
            AppendCURRENT_USERWithAdvancedData(); // дополнить CURRENT_USER user_id'шниками со всех моих отделов
            if(@$_REQUEST['DownloadType']) {
                // Create new PHPExcel object
                include('Mods/PHPExcel/PHPExcel.php');
                include('Mods/PHPExcel/PHPExcel/Writer/Excel2007.php');
                // start downloading
                require("Lib/Go_GetCommerceObjectsListDownload.php");
            } else {
                require("Lib/Go_GetCommerceObjectsList.php");
            }
            break;

        case 'OpenObject':
            require("Lib/Objects/MakeObjectArrByObj.php"); // MakeObjectArrByObj()
            $AddObj = array();
            $sql = "SELECT
                        *
                    FROM
                        Objects
                    WHERE
                        id = {$_REQUEST['id']}";
            $res = mysql_query($sql);
            $Obj = mysql_fetch_object($res);

            // если объект коммерческий, нужно соединить доп поля
            if($Obj->RealtyType == 'commerce') {
                $sql = "SELECT
                            *
                        FROM
                            ObjectsData
                        WHERE
                            ObjectId = {$_REQUEST['id']}";
                $res = mysql_query($sql);
                $AddObj = (array)mysql_fetch_object($res);
                // убираем наложенные  поля, для дальнейшего совмещения массивов
                //$AddObj['id']        = null;
                //$AddObj['AddedDate'] = null;
            }

            $Arr = MakeObjectArrByObj($Obj, $AddObj);    // назначаем соответствующие поля
        //print_r($Arr);
            $response               = (object) array();
            $response->success      = true;
            //$response->message      = "Loaded data";
            $response->data         = $Arr;
            //$response->total        = 5;

            header("Content-Type: application/json;charset=UTF-8");
            echo json_encode(
                $response,
                JSON_UNESCAPED_UNICODE);

            break;

        case 'OpenObjectAdditions' :
            // берем дополнительную инфу по объекту (пока дублирует "case 'OpenObject' ")
            require("Lib/Objects/MakeObjectArrByObj.php"); // MakeObjectAdditionsArrByObj()
            $sql = "SELECT
                        *
                    FROM
                        Objects
                    WHERE
                        id = {$_REQUEST['id']}";
            $res = mysql_query($sql);
            $Obj = mysql_fetch_object($res);
            $Arr = MakeObjectAdditionsArrByObj($Obj);    // назначаем соответствующие поля
            $response               = (object) array();
            $response->success      = true;
            $response->data         = $Arr;
            header("Content-Type: application/json;charset=UTF-8");
            echo json_encode(
                $response,
                JSON_UNESCAPED_UNICODE);
            break;

        case 'SaveUserForm':
            if(CheckMyRule("Users-All-ReadEditDeleteRestore") ) {
                $_REQUEST['FirstName']  = mysql_real_escape_string( $_REQUEST['FirstName'] );
                $_REQUEST['LastName']   = mysql_real_escape_string( $_REQUEST['LastName'] );
                $_REQUEST['Login']      = mysql_real_escape_string( $_REQUEST['Login'] );
                $_REQUEST['Password1']  = mysql_real_escape_string( $_REQUEST['Password1'] );
                $_REQUEST['Email']      = mysql_real_escape_string( $_REQUEST['Email'] );
                $_REQUEST['MobilePhone']= mysql_real_escape_string( $_REQUEST['MobilePhone'] );

                // проверяем все мобильные номера на совпадение
                $NumberExistObj = User_CheckMobileNumberExist( array($_REQUEST['MobilePhone'], $_REQUEST['MobilePhone1'], $_REQUEST['MobilePhone2']), $_REQUEST['LoadedUserId'] );
                if($NumberExistObj) {
                    $msg = 'Невозможно сохранить, т.к. <b>номер</b> '.$NumberExistObj[2].' уже привязан к: '.$NumberExistObj[1].' (№: '.$NumberExistObj[0].')';
                    echo '{"success":false,"message":"'.$msg.'"}';
                    break;
                }

                $LoginExist = User_CheckLoginExist($_REQUEST['Login'], $_REQUEST['LoadedUserId']);
                if($LoginExist) {
                    $msg = 'Невозможно сохранить, т.к. <b>логин</b> '.$_REQUEST['Login'].' уже привязан к: '.$LoginExist[1].' (№: '.$LoginExist[0].')';
                    echo '{"success":false,"message":"'.$msg.'"}';
                    break;
                }

                $EmailExist = User_CheckEmailExist($_REQUEST['Email'], $_REQUEST['LoadedUserId']);
                if($EmailExist) {
                    $msg = 'Невозможно сохранить, т.к. <b>email</b> '.$_REQUEST['Email'].' уже привязан к: '.$EmailExist[1].' (№: '.$EmailExist[0].')';
                    echo '{"success":false,"message":"'.$msg.'"}';
                    break;
                }

                if($_REQUEST['LoadedUserId']) {
                    // обновление существующего
                    list($Result, $ErrMsg) = User_Update($_REQUEST);
                    if($Result) {
                        $StsRes = AddOrUpdateUserStatus($_REQUEST['LoadedUserId'], $_REQUEST['Status0Id']);

                        $GrpRes = AddOrUpdateUserGroup($_REQUEST['LoadedUserId'], $_REQUEST['Group0Id']);
                        (!$GrpRes) ? $AddMsg = ', но возникла ошибка в назначении группы' : $AddMsg = '';
                        $PosRes = AddOrUpdateUserPosition($_REQUEST['LoadedUserId'], $_REQUEST['Pos0Id']);
                        (!$PosRes) ? $AddMsg .= ', возникла ошибка в назначении должности!' : $AddMsg .= '';

                        $Params['LetterType'] = 'UserUpdate';
                        $Params['LetterData'] = $_REQUEST;
                        $EmailResult = Email_SimpleLetter($Params);
                        if(strlen($EmailResult)>0) {$EmailResult = ". ".$EmailResult; }

                        echo '{"success":true,"message":"Анкета пользователя успешно обновлена'.$AddMsg.$EmailResult.'"}';
                    } else {
                        echo '{"success":false,"message":"'.$ErrMsg.'"}';
                    }

                } else {
                    // новый пользователь
                    list($Result, $SavedUserId, $ErrMsg) = User_Create($_REQUEST);
                    if($Result) {
                        $StsRes = LinkUserIdToStatusId($SavedUserId, $_REQUEST['Status0Id'], 0);

                        $GrpRes = LinkUserIdToGroupId($SavedUserId, $_REQUEST['Group0Id'], 0);
                        (!$GrpRes) ? $AddMsg = ', но возникла ошибка в назначении группы. ' : $AddMsg = '. ';
                        $PosRes = LinkUserIdToPositionId($SavedUserId, $_REQUEST['Pos0Id'], 0);
                        (!$PosRes) ? $AddMsg .= ', возникла ошибка в назначении должности! ' : $AddMsg .= '. ';

                        // Отправляем приглашение на почту
                        $Params['LetterType'] = 'InvitationToNewUser';
                        $Params['LetterData'] = $_REQUEST;
                        $EmailResult = Email_SimpleLetter($Params);

                        echo '{"success":true,"LoadedUserId":' . $SavedUserId . ',"message":"Новый пользователь успешно добавлен'.$AddMsg.$EmailResult.'"}';
                    } else {
                        echo '{"success":false,"message":"'.$ErrMsg.'"}';
                    }
                }
            } else {
                DenyRuleAlert(array('Users-All-ReadEditDeleteRestore'));
            }

            break;

        case 'OpenUser':
            $sql = "SELECT
                        *
                    FROM
                        Users
                    WHERE
                        id = {$_REQUEST['id']}";
            $res = mysql_query($sql);
            $str = mysql_fetch_object($res);
            header("Content-Type: application/json;charset=UTF-8");
            $r   = (array)$str; // TODO загружаютсе все данные, its not good

            $GrpArr = GetUserGroups($_REQUEST['id']);
            $r['Group0Id'] = @$GrpArr[0]->GroupId;
            //$r['Group1Id'] = @$GrpArr[1]->GroupId; //TODO Задействовать доп группы и должности (когда назреет)
            //$r['Group2Id'] = @$GrpArr[2]->GroupId;

            $PosArr = GetUserPositionsArr($_REQUEST['id']);
            $r['Pos0Id'] = @$PosArr[0]->PositionId;

            $StsArr = GetUserStatusesArr($_REQUEST['id']);
            $r['Status0Id'] = @$StsArr[0]->StatusId;

            $r['LoadedUserId']  = $_REQUEST['id'];
            //$r['Password1']   = $str->Password;

            /*$r['FirstName']   = $str->FirstName;
            $r['LastName']    = $str->LastName;
            $r['MobilePhone'] = $str->MobilePhone;
            $r['Email']      = $str->Email;
            $r['Login']      = $str->Login;
            $r['Password1']   = $str->Password;*/

            $response               = (object) array();
            $response->success      = true;
            $response->data         = $r;
            echo json_encode(
                $response,
                JSON_UNESCAPED_UNICODE);

            break;


        case 'UploadFiles':
            require("Lib/Upload.php");
            echo FileUploader($_FILES, $_REQUEST['ObjectId']);

            //file_put_contents( 'c:\temp\1.txt', print_r($_FILES,true), FILE_APPEND );
            //echo 'UploadedPreview';
            break;

        case "GetObjectImages":
            header("Content-Type: application/json;charset=UTF-8");
            $ImagesArr = GetImagesObjByObjectId( $_REQUEST['ObjectId'] );
            $out = '';
            foreach($ImagesArr as $ImgObj) {
                $out .= '{id:'.$ImgObj->id.',"width":"'.$ImgObj->Width.'","height":"'.$ImgObj->Height.'","name":"'.$ImgObj->id.'","size":2476,"lastmod":1418276219000,"url":"'.$ImgObj->PreviewPath.'"},';
            }
            echo '{"images":[' . $out . "]}";

            break;

        case "LoadObjectImageByImageId":
            $ImgObj = LoadObjectImageByImageId( $_REQUEST['ImageId'] );
            header("Content-type: image/gif");
            echo file_get_contents($CONF['SystemPath'] . $ImgObj->FilePath);
            break;

        case "DeleteObjectImageByImageId":
            $Result = DeleteObjectImageByImageId( $_REQUEST['ImageId'] );
            if($Result) {
                echo '{"success":true}';
            } else {
                echo '{"success":false,"message":"' . nl2br(mysql_error()) . '"}';
                // TODO централизовать вывод и протоколирование ошибок
            }
            break;

        case "SetObjectFirstImage":
            $ObjectId = GetObjectIdByImageId( $_REQUEST['ImageId'] );
            ResetObjectImagesPrimarity($ObjectId);
            SetObjectFirstImage($_REQUEST['ImageId']);
            echo '{"success":true}';
            break;


        case "ArchivateObjectById":
            $Result = ArchivateObjectById( $_REQUEST['ObjectId'] );
            ClearAdPortalObjectsForObjectId( $_REQUEST['ObjectId'] ); // убираем отметки о рекламе объекта
            if($Result) {
                echo '{"success":true}';
            } else {
                $Msg    = htmlentities( preg_replace('/\n/', '', mysql_error()) );
                echo '{"success":false,"message":"' . $Msg . '"}';
                // TODO централизовать вывод и протоколирование ошибок
            }
            break;

        case "RestoreObjectById":
            $Result = RestoreObjectById( $_REQUEST['ObjectId'] );
            if($Result) {
                echo '{"success":true}';
            } else {
                $Msg    = htmlentities( preg_replace('/\n/', '', mysql_error()) );
                echo '{"success":false,"message":"' . $Msg . '"}';
                // TODO централизовать вывод и протоколирование ошибок
            }
            break;


        case "UpdateAdTarifObjectState":
            $Result = UpdateAdTarifObjectState( $_REQUEST['ObjectId'], $_REQUEST['TarifShortName'], $_REQUEST['Value'] );
            // TODO сохранять эти действия в "историю объекта"
            if($Result) {
                echo '{"success":true}';
            } else {
                $Msg = htmlentities( preg_replace('/\n/', '', mysql_error()) );
                echo '{"success":false,"message":"' . $Msg . '"}';
                // TODO централизовать вывод и протоколирование ошибок
            }
            break;

        case "GetUsersList":
            if(@$_REQUEST['DownloadType']) {
                // Create new PHPExcel object
                include('Mods/PHPExcel/PHPExcel.php');
                include('Mods/PHPExcel/PHPExcel/Writer/Excel2007.php');
                // start downloading
                require("Lib/Go_GetUsersListDownload.php");
            } else {
                require("Lib/Go_GetUsersList.php");
            }
            break;

        case "GetMailList":
            //require("Lib/Email/Funcs.php");

            $Params = array();
            ($_REQUEST['Active'] == 1) ? $Params['Active']   = 1 : $Params['Active']   = 0; // активные или архивные?
            $Params['OrderByField'] = 'AddedDate';
            $Params['OrderByTo']    = 'DESC';
            $MailsArr = GetMailListArr($Params);

            foreach($MailsArr as $Email) {
                $NewMark = array();
                $element                = array();
                $element['id']          = $Email->id;
                $element['AddedDate']   = $Email->AddedDate;
                $element['ArchivedDate']= $Email->ArchivedDate;
                $element['MailFrom']    = $Email->MailFrom;
                if(!$Email->Opened) {
                    $NewMark[0] = '<b>';$NewMark[1] = '</b>';
                }
                $element['Subject']     = @$NewMark[0] . $Email->Subject . @$NewMark[1];
                array_push($out, $element);
            }
            $response               = (object) array();
            $response->success      = true;
            $response->data         = $out;
            $response->total        = count($MailsArr);

            header("Content-Type: application/json;charset=UTF-8");
            echo json_encode($out, JSON_UNESCAPED_UNICODE);
            break;

        case "ArchivateUserById":
            $Result = ArchivateUserById( $_REQUEST['UserId'] );
            if($Result) {
                echo '{"success":true}';
            } else {
                $Msg    = htmlentities( preg_replace('/\n/', '', mysql_error()) );
                echo '{"success":false,"message":"' . $Msg . '"}';
                // TODO централизовать вывод и протоколирование ошибок
            }
            break;


        case "RestoreUserById":
            $Result = RestoreUserById( $_REQUEST['UserId'] );
            if($Result) {
                echo '{"success":true}';
            } else {
                $Msg    = htmlentities( preg_replace('/\n/', '', mysql_error()) );
                echo '{"success":false,"message":"' . $Msg . '"}';
                // TODO централизовать вывод и протоколирование ошибок
            }
            break;

        case "OpenMailById":


            $EmailObj = GetEmailObjById($_REQUEST['EmailId']);
            MarkEmailAsRead($_REQUEST['EmailId']);
            $response               = (object) array();
            $response->success      = true;
            if(strlen($EmailObj->DecodedBody) <=3 ) {
                // текста нет, показываем первый файл (cian)
                $FilesArr = GetFilesArrByEmailId($_REQUEST['EmailId']);
                $f = @file_get_contents( $CONF['CrmCopyMailDir'] . $FilesArr[0]->filename ); // открываем первый аттач этого письма
                if($f) {
                    $response->message = $f;
                } else {
                    $msg = 'error: cant open file';
                    $response->message = $msg;
                    $GLOBALS['FirePHP']->error($msg);
                }

                //$response->message      = file_get_contents( '/tmp/crm/crm_lefortovo/Mail/14Nov2015-101003_file' );
                $response->message      = quoted_printable_decode($response->message);
                //$response->message = strlen($EmailObj->DecodedBody);

            } else {
                $response->message      = '<pre>'.$EmailObj->DecodedBody.'</pre>';
            }

            //iconv("ASCII//TRANSLIT","UTF-8", $response->message );

            //$response->total      = count($MailsArr);

            header("Content-Type: application/json;charset=UTF-8");
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            break;

        case "QuickObjectQueryById":
            $Obj        = QuickObjectQueryById( $_REQUEST['ObjectId'] );
            $response               = (object) array();
            $response->success      = true;
            $response->RealtyType   = $Obj->RealtyType;
            $response->Active       = $Obj->Active;

            header("Content-Type: application/json;charset=UTF-8");
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            break;

        case "GeoWin":
            echo DrawGeoWinTemplate(@$_REQUEST['ObjectId']);
            break;

        case "LoadJsonSobList":
            // Главный поиск собов - показываем список объектов
            connectToDB('DBAntiposrednik');
            require('Lib/Sob/Sob.php');

            if(@$_REQUEST['DownloadType']) {
                $Params['RealtyType'] = 'owners';

                list($d, $total, $AllowXlsExport) = LoadJsonSobList($_REQUEST);
                // Create new PHPExcel object
                require('Mods/PHPExcel/PHPExcel.php');
                require('Mods/PHPExcel/PHPExcel/Writer/Excel2007.php');
                require('Lib/Sob/LoadJsonSobListDownload.php');
                LoadJsonSobListDownload($d);

            } else {
                $Params['RealtyType'] = 'owners';
                list($d, $total, $AllowXlsExport) = LoadJsonSobList($_REQUEST);
                $response               = (object) array();
                $response->success      = true;
                $response->data         = $d;
                $response->total        = $total;
                $response->AllowXlsExport = $AllowXlsExport;
                header("Content-Type: application/json;charset=UTF-8");
                echo json_encode($response, JSON_UNESCAPED_UNICODE);
            }


            break;

        case 'OwnersGetObject':
            connectToDB('DBAntiposrednik');
            require('Lib/Sob/Sob.php');

            $UserId = 0;
            ClickSobObject($UserId, $_REQUEST['ObjectId'], 'click');
            header("Content-Type: application/json;charset=UTF-8");
            echo json_encode(
                GetSobObjInJson($_REQUEST['ObjectId']),
                JSON_UNESCAPED_UNICODE);
            break;

        case 'OwnersGetComments':
            connectToDB('DBAntiposrednik');
            require('Lib/Sob/Sob.php');
            $response = (object)[];
            $response->CommentsList = GetCommentsListBySobId($_REQUEST['ObjectId']);

            header("Content-Type: application/json;charset=UTF-8");
            echo json_encode(
                $response,
                JSON_UNESCAPED_UNICODE);
            break;

        case 'OwnersSaveComment':
            require('Lib/Sob/Sob.php');
            $response = (object)[];
            $response->success = OwnersSaveComment($_REQUEST['ObjectId'], $CURRENT_USER->id, htmlentities($_REQUEST['CommentsText']) );
            echo json_encode(
                $response,
                JSON_UNESCAPED_UNICODE);
            break;

        case 'OwnersDate':
            connectToDB('DBAntiposrednik');
            require('Lib/Sob/Sob.php');

            header("Content-Type: application/json;charset=UTF-8");
            echo json_encode(
                OwnersDate(),
                JSON_UNESCAPED_UNICODE);
            break;

        case 'SaveSettingsForm':
            $ResultMsg = SaveSettingsForm($_REQUEST);
            if(!$ResultMsg) {
                echo '{"success":true,"message":"Настройки успешно обновлены"}';
            } else {
                echo '{"success":false,"message":"'.$ResultMsg.'"}';
            }
            break;

        case 'LoadSettingsForm' :
            $result = LoadSettingsForm();
            header("Content-Type: application/json;charset=UTF-8");
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
            break;

        case 'GetFullUserInfo' :
            //$response   = (object)[];
            $DataToSend                 = $CURRENT_USER;
            $DataToSend->UserAccessRules= ArrToJson($CURRENT_USER->AccessRulesArr, true);
            unset($DataToSend->Password);// подчищаем вывод
            unset($DataToSend->PwSalt);
            unset($DataToSend->WorkSessionKey);
            unset($DataToSend->AccessRulesArr);

            echo json_encode(
                $DataToSend,
                JSON_UNESCAPED_UNICODE);
            break;

        case 'GetSysParams':
 /*           $DataToSend                 = $CURRENT_USER;
            $DataToSend->UserAccessRules= ArrToJson($CURRENT_USER->AccessRulesArr, true);
            unset($DataToSend->Password);// подчищаем вывод
            unset($DataToSend->PwSalt);
            unset($DataToSend->WorkSessionKey);
            unset($DataToSend->AccessRulesArr);
*/
            echo json_encode(
                $CURRENT_SYS_PARAMS,      //Lib\Kernel\Authentication.php : InitAuthentication(), Lib\Kernel\DataBase.php : GetPublicSysParams()
                JSON_UNESCAPED_UNICODE);
            break;


        case 'GetAccessRulesStructure':
            // Загрузить и раскрыть все права поьзователя или сделать предварительный просмотр прав на должность/отдел
            // Если передаются PositionId и GroupId - это предварительный просмотр
            $out                     = array();
            $Params['Preview']       = null; // тип запроса: права пользователя или превью прав по выбранной должности,отделу
            $Params['Structure']     = true;

            $Params['PositionId']    = @$_REQUEST['PositionId'];
            $Params['GroupId']       = @$_REQUEST['GroupId'];
            //if($Params['PositionId'] || $Params['GroupId']) {
                // это предварительный запрос
                $Params['ForCurrentUser']   = false;
                $Params['Preview']          = true;
                $UserId                     = $_REQUEST['UserId'];

            //} else {
                // мой личный список
            //    $Params['ForCurrentUser']   = true;
            //    $Params['Preview']          = false;
            //    $UserId                     = $CURRENT_USER->id;
            //}
            list($UserGroupsArr, $UserPositionsArr, $AccessRuleObjArr, $UserAccessRuleObj, $UserAccessRuleIds, $PropObj) = GetUserAccessParamsArr( $UserId, $Params );
            // todo слишком много всего: переименовать, прокомментировать
            //$p=100;
            foreach($AccessRuleObjArr as $RuleObj) {
                $NewMark = array();
                /*$element = (object)[];//$RuleObj;
                $element->id = $p;//$RuleObj->id;
                $element->Description = $RuleObj->Description;
                array_push($out, (array)$element);
                $p++;*/
                $element = $RuleObj;
                if($RuleObj->TargetType != 'user') {
                    // чтобы grid не сломался из-за совпадающих id'шников
                    $element->id = $RuleObj->id . $RuleObj->TargetType;
                    $element->{'Наследование'} = $WORDS['RightsFrom'][ $RuleObj->TargetType ];//GridGroup //Наследование// $WORDS
                } else {
                    $element->{'Наследование'} = $WORDS['RightsFrom'][ $RuleObj->TargetType ];
                }


                array_push($out, (array)$element);
            }//print_r($AccessRuleObjArr);
            $response               = (object) array();
            $response->success      = true;
            $response->preview      = $Params['Preview'];
            if(@$PropObj->UserGroupsNames) {
                $response->GroupNames    = $PropObj->UserGroupsNames; }
            if(@$PropObj->UserPositionsNames) {
                $response->PositionNames    = $PropObj->UserPositionsNames; }
            $response->data         = $out;
            header("Content-Type: application/json;charset=UTF-8");
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            break;

        case 'GetAccessRulesForAddition':
            $out    = array();
            $total  = 0;
            $Params['ExceptRulesForUserId']     = $_REQUEST['UserId']; // исключить существующие права для userId
            $Params['ExceptRulesForPositionId'] = @$_REQUEST['PositionId'];
            $Params['ExceptRulesForGroupId']    = @$_REQUEST['GroupId'];
            $GetAccessRulesObjArr = GetAccessRulesObjArr($Params);
            foreach($GetAccessRulesObjArr as $RuleObj) {
                $element = (array)$RuleObj;
                array_push($out, $element);
                $total++;
            }
            $response               = (object) array();
            $response->success      = true;
            $response->data         = $out;
            $response->total        = $total;
            header("Content-Type: application/json;charset=UTF-8");
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            break;

        case 'AddAccessRuleIdForUserId':
            $Result = AddAccessRuleIdForUserId( $_REQUEST['RuleId'], $_REQUEST['UserId']);

            $response               = (object) array();
            $response->success      = $Result;
            header("Content-Type: application/json;charset=UTF-8");
            echo json_encode($response, JSON_UNESCAPED_UNICODE);

            break;

        case 'DeleteAccessRuleIdForUserId':
            $Result = DeleteAccessRuleIdForUserId( $_REQUEST['RuleId'], $_REQUEST['UserId']);

            $response               = (object) array();
            $response->success      = $Result;
            header("Content-Type: application/json;charset=UTF-8");
            echo json_encode($response, JSON_UNESCAPED_UNICODE);

            break;

        case 'GetPositionsAndRightsStructure':
            $Params['ExpandItemId'] = @$_REQUEST['ExpandItemId'];
            $structure              = GetItemsAndRightsStructure('position', $Params);
            $response               = (object)array();
            $response->success      = true;
            $response->children     = $structure;
            header("Content-Type: application/json;charset=UTF-8");
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            break;

        case 'GetGroupsAndRightsStructure':
            $Params['ExpandItemId'] = @$_REQUEST['ExpandItemId'];
            $structure              = GetItemsAndRightsStructure('group', $Params);
            $response               = (object)array();
            $response->success      = true;
            $response->children     = $structure;
            header("Content-Type: application/json;charset=UTF-8");
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            break;

        case 'GetStatusesStructure':
            $Params['Active'] = 1;
            $Arr = GetUserStatusesArr(null, $Params);

            foreach($Arr as $StObj) {
                $element              = array();
                $element['id']        = $StObj->id;
                $element['ItemName']  = $StObj->StatusName;
                $element['ItemType']  = 'Status';
                $element['iconCls']   = 'StatusCls';
                $element['leaf']      = true;
                array_push($out, $element);
            }
            $response               = (object)array();
            $response->success      = true;
            $response->children     = $out;
            header("Content-Type: application/json;charset=UTF-8");
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            break;

        case 'AddNewPosition':
            $PositionName = @$_REQUEST['name'];
            list($result, $ResultMsg) = AddNewPosition( $PositionName );
            header("Content-Type: application/json;charset=UTF-8");
            if(!$result) {
                echo '{"success":false,"message":"Возникла ошибка: '.$ResultMsg.'"}';
            } else {
                echo '{"success":true,"message":"Должность \"'.$PositionName.'\" успешно создана"}';
            }
            break;

        case 'AddNewGroup':
            $GroupName = @$_REQUEST['name'];
            list($result, $ResultMsg) = AddNewGroup( $GroupName );
            header("Content-Type: application/json;charset=UTF-8");
            if(!$result) {
                echo '{"success":false,"message":"Возникла ошибка: '.$ResultMsg.'"}';
            } else {
                echo '{"success":true,"message":"Отдел \"'.$GroupName.'\" успешно создан"}';
            }
            break;

        case 'AddNewStatus':
            $StatusName = @$_REQUEST['name'];
            list($result, $ResultMsg) = AddNewStatus( $StatusName );
            header("Content-Type: application/json;charset=UTF-8");
            if(!$result) {
                echo '{"success":false,"message":"Возникла ошибка: '.$ResultMsg.'"}';
            } else {
                echo '{"success":true,"message":"Статус \"'.$StatusName.'\" успешно создан"}';
            }
            break;

        case 'RemoveStructureItem':
            if($_REQUEST['ItemType'] == 'Position') {
                // удаляем должность совсем. Удаляем все права прикрепленые к этой должности.
                // TODO Что с GroupId у пользователей?
                ClearAccessLinks('ByTarget', $_REQUEST['ItemType'], $_REQUEST['ItemId']);
                $result = DeletePosition($_REQUEST['ItemId']);
                $msg    = "Должность успешно удалена";

            } elseif($_REQUEST['ItemType'] == 'Group') {
                // удаляем должность совсем. Удаляем все права прикрепленые к этой должности.
                // TODO Что с GroupId у пользователей?
                ClearAccessLinks('ByTarget', $_REQUEST['ItemType'], $_REQUEST['ItemId']);
                $result = DeleteGroup($_REQUEST['ItemId']);
                $msg    = "Отдел успешно удален";

            } elseif($_REQUEST['ItemType'] == 'Status') {
                $result = DeleteStatus($_REQUEST['ItemId']);
                $msg    = "Статус успешно удален";

            } elseif($_REQUEST['ItemType'] == 'Right') {
                // открепляем право доступа от конкретной определенной должности
                // убираем префикс добавленный в GetItemsAndRightsStructure()
                // id поступает в виде "r12p9"
                list($_REQUEST['ItemId'], $BindedPositionId) = explode("p", str_replace('r', '', $_REQUEST['ItemId']) );
                $result = ClearAccessLinks('ByRule', $_REQUEST['BindToType'], $_REQUEST['ItemId'], $_REQUEST['BindToItemId']);
                $msg    = "Право доступа успешно откреплено";

            } else {
                $msg = "Fatal error: ItemType unknown";
                MainFatalLog($msg);
            }
            if(!$result) {
                echo '{"success":false,"message":"Возникла ошибка"}';
            } else {
                echo '{"success":true,"message":"'.$msg.'"}';
            }
            break;

        case 'GetPositionsArr':
            $PosObjList = GetPositionsOrGroupsObjArr('position');
            foreach($PosObjList as $PosObj) {
                $element            = array();
                $element['id']      = $PosObj->id;
                $element['VarName'] = $PosObj->PositionName;
                //$element['VarData'] = '';
                array_push($out, $element);
            }
            header("Content-Type: application/json;charset=UTF-8");
            echo json_encode($out, JSON_UNESCAPED_UNICODE);
            break;

        case 'GetGroupsArr':
            $GrpObjList = GetPositionsOrGroupsObjArr('group');
            foreach($GrpObjList as $GrpObj) {
                $element            = array();
                $element['id']      = $GrpObj->id;
                $element['VarName'] = $GrpObj->GroupName;
                //$element['VarData'] = '';
                array_push($out, $element);
            }
            header("Content-Type: application/json;charset=UTF-8");
            echo json_encode($out, JSON_UNESCAPED_UNICODE);
            break;

        case 'GetAccessRulesArr':
            $Params0['OnlyIds']              = true;
            $ExceptionRuleIds                = GetAccessRulesByTarget($Params0, $_REQUEST['ChosenItemType'], $_REQUEST['ChosenItemId']);
            $Params2['ExceptRulesForItemId'] = $_REQUEST['ChosenItemId'];
            $Params2['ExceptionRuleIds']     = $ExceptionRuleIds;
            $RulesObjList                    = GetAccessRulesObjArr($Params2);
            foreach($RulesObjList as $PosObj) {
                $element            = array();
                $element['id']      = $PosObj->id;
                $element['VarName'] = $PosObj->Description;
                //$element['VarData'] = '';
                array_push($out, $element);
            }
            header("Content-Type: application/json;charset=UTF-8");
            echo json_encode($out, JSON_UNESCAPED_UNICODE);
            break;

        case 'AttachRuleToItem':
            $result = AttachRuleToItem($_REQUEST['ItemType'], $_REQUEST['ItemId'], $_REQUEST['RuleId']);
            if(!$result) {
                echo '{"success":false,"message":"Возникла ошибка"}';
            } else {
                $Params['OnlyNames'] = true;
                $Params['InString']  = true;
                if($_REQUEST['ItemType'] == 'position') {
                    $ItemName = GetPositionsNamesById($_REQUEST['ItemId'], $Params);
                    echo '{"success":true,"message":"Право доступа успешно прикреплено к должности <b>'.$ItemName.'</b>"}';
                } elseif($_REQUEST['ItemType'] == 'group') {
                    $ItemName = GetGroupsNamesById($_REQUEST['ItemId'], $Params);
                    echo '{"success":true,"message":"Право доступа успешно прикреплено к отделу <b>'.$ItemName.'</b>"}';
                } else {
                    MainFatalLog('AttachRuleToItem: ItemType unknown');
                    SystemExit();
                }
            }
            break;

        case 'RenameStrucItem':
            $result = RenameStrucItem($_REQUEST['ItemType'], $_REQUEST['ItemId'], $_REQUEST['ItemNewName']);
            if(!$result) {
                echo '{"success":false,"message":"Возникла ошибка"}';
            } else {
                echo '{"success":true,"message":"' . $WORDS['Structure'][ $_REQUEST['ItemType'] ] . ' успешно переименована с \"'.$_REQUEST['ItemOldName'].'\" на \"<b>'.$_REQUEST['ItemNewName'].'</b>\""}';
            }
            break;

        case 'AdSummRecount':
            // Ручной "пересчетчик"
            // Взять все пометки к выгрузке их OwnerUserId и кол-во дней до сегодня
            // предварительно "delete  FROM `AdPortalObjects` WHERE TarifId IS NULL"

            $GLOBALS['FirePHP']->setEnabled(false);
            require(dirname(__FILE__) . '/Lib/Billing/BillingFuncs.php');
            // DATEDIFF(CURRENT_DATE(), apo.AddedDate)
            // CURRENT_DATE()      '2016-05-16'
            // DATEDIFF('2016-05-16', apo.AddedDate)
            // DATEDIFF(CURRENT_DATE(), '2016-05-16')
            $sql = "SELECT
                      apo.id,
                      apo.ObjectId, apo.TarifId, DATEDIFF(CURRENT_DATE(), apo.AddedDate) AS ObjectLifeInDays,
                      (SELECT o.OwnerUserId FROM Objects AS o WHERE o.id=apo.ObjectId) AS UserId,
                      (SELECT t.TarifName FROM BillAdTarifs AS t WHERE t.id=apo.TarifId) AS TarifName
                    FROM
                        AdPortalObjects AS apo

                    ORDER BY apo.ObjectId";
            $res = mysql_query($sql);
            $i=0;
            while($apo = mysql_fetch_object($res)) {
                if($apo->UserId && $apo->TarifId && $apo->ObjectId) { // по каждому активному объекту
                    $TarifPrice = GetPriceByTarifId($apo->TarifId);
                    $AccountId = CheckOrCreateUserBillAccount($apo->UserId);
                    $AllDaysSumm = $TarifPrice * $apo->ObjectLifeInDays; //цену умножаем на кол-во дней
                    $i++;
                    $sql = "
                        INSERT INTO
                            BillOperations (
                            OperationDate, OperationType, OperationSumm, AccountId, ExpenseTypeId,
                            TargetType, TargetId, TarifId, OperationAuthorId, OperationComment)
                        VALUES (
                            CURRENT_TIMESTAMP, 'expense', $AllDaysSumm, $AccountId, 1,
                            'object', $apo->ObjectId, $apo->TarifId, '0', '\"{$apo->TarifName}\": сумма ({$AllDaysSumm}) = $TarifPrice (цена за сутки) * {$apo->ObjectLifeInDays} (дней)');
                    ";
                    $qres = mysql_query($sql);
                    if($qres) { CrmCopyErrorLog(mysql_error()); }
                    //echo "$sql\n\n";
                } else {
                    $msg = "Cant add expense operation: AdPortalObjects.id: {$apo->id}, UserId: {$apo->UserId}, TarifId: {$apo->TarifId}, ObjectId: {$apo->ObjectId} (db was manually edited?)";
                    echo "$msg\n";
                    CrmCopyErrorLog($msg);
                }
            }
            echo "processed $i\n";
            exit;
            /*
            по каждому объекту из (AdPortalObjects) {
                SELECT DATEDIFF(CURRENT_DATE(),'2014-11-29') AS DiffDate    =    кол-во дней объекта (D)

                вставить каждый день отдельной транзакцией в BillOperations: взять заранее: AccountId, TarifId,

            }
            */
            break;

        case 'LoadAdPricesForm':
            require(dirname(__FILE__) . '/Lib/Billing/AdPrices.php');
            $Params['Actual']   = true;

            $response           = (object) array();
            $response->success  = true;
            $response->data     = GetTarifPricesArr($Params);

            header("Content-Type: application/json;charset=UTF-8");
            echo json_encode(
                $response,
                JSON_UNESCAPED_UNICODE);
            break;

        case 'SaveAdPricesForm':
            require(dirname(__FILE__) . '/Lib/Billing/AdPrices.php');
            $msg = UpdateAdPrices($_REQUEST);
            UpdateBillAdTarifsActivity($_REQUEST);
            echo '{"success":true,"message":"Параметры рекламы обновлены!'.$msg.'"}';
            break;


        case 'DataRequest':
            $data    = array();
            $success = false;
            require(dirname(__FILE__) . '/Lib/Billing/AdPrices.php');


            switch ($_REQUEST['DataType']) {
                // готовим ответ на запрос
                case 'AdPortalsInfo':
                    $success = true;
/*
                    $element                    = array();
                    $element['PortalName']      = 'navig';
                    $element['PortalId']      = 'navigId';
                    $element['PortalImg']       = 'images/sites/navigator.gif';
                    $element['PortalStatus']    = 'подключен';
                    $element['PortalDescription']      = 'navigdescr';
                    array_push($data, $element);

                    $element                    = array();
                    $element['PortalName']      = 'winner';
                    $element['PortalId']      = 'winnerId';
                    $element['PortalImg']       = 'images/sites/winner.gif';
                    $element['PortalStatus']    = 'winnerStatus2';
                    $element['PortalDescription']  = 'winnerDescr';
                    array_push($data, $element);*/

                    $Params = array();
                    $data = GetContragentsList($Params);

                    break;

                case 'BriefNews':
                    $success = true;
                    $Params = array();
                    $Params['Count'] = $_REQUEST['Count'];
                    $data = GetNews($Params);
                    break;

               case 'PaymentInfo':
                   //GetCurrentCompanyInfo();
                   $success = true;
                   $Params  = array();

                   $data               = array();
                   if($CURRENT_COMPANY->DaysRemain > 0) {
                       $MsgColor = "green";
                       $Msg = '';
                   } else {
                       $MsgColor = "red";
                       $Msg = 'Пожалуйста внесите оплату!<br>Yandex кошелек: 123456789900875<br>MasterCard: 1234 1234 1234 1234';
                   }
                   $data['PayedTill']  = "<span style='font-weight: bold;color:$MsgColor'>Оплачено до: ".$CURRENT_COMPANY->PayedTill ." ".$Msg."</span>";
                   //array_push($data, $element);
                   break;

                case 'GetSystemVars':
                    $success = true;

                    $element                = array();
                    $element['VarKey']     = 'kkk';
                    $element['VarValue']   = 'vvv';
                    array_push($data, $element);

                    $element               = array();
                    $element['VarKey2']   = 'VarValue2';
                    array_push($data, $element);

                    $element               = array();
                    $element['33']         = 'kkkk';
                    array_push($data, $element);

                    break;

                default:

                    echo "DataType unknown";
            }

            // отправляем данные
            $response           = (object) array();
            $response->success  = $success;
            $response->data     = $data;
            $response->total        = count($data);

            header("Content-Type: application/json;charset=UTF-8");
            echo json_encode(
                $response,
                JSON_UNESCAPED_UNICODE);


            break;

        case 'GetClientsList':
            if(@$_REQUEST['DownloadType']) {
                // Create new PHPExcel object
                include('Mods/PHPExcel/PHPExcel.php');
                include('Mods/PHPExcel/PHPExcel/Writer/Excel2007.php');
                // start downloading
                require("Lib/Clients/GetClientsListDownload.php");
            } else {
                require("Lib/Clients/GetClientsList.php");
            }
            break;

        case 'OpenClient': // TODO добавить проверку права доступа
            $sql = "SELECT
                        *, IF(Birthday = '0000-00-00', '', Birthday) AS Birthday
                    FROM
                        Clients
                    WHERE
                        id = {$_REQUEST['id']}";
            $res = mysql_query($sql);
            $str = mysql_fetch_object($res);
            header("Content-Type: application/json;charset=UTF-8");
            $r   = (array)$str; // TODO загружаютсе все данные, its not good

            $r['LoadedClientId']  = $_REQUEST['id'];


            $response               = (object) array();
            $response->success      = true;
            $response->data         = $r;
            echo json_encode(
                $response,
                JSON_UNESCAPED_UNICODE);

            break;

        case 'SaveClientForm':
            $_REQUEST['Description']  = mysql_real_escape_string( $_REQUEST['Description'] );
            // проверяем все мобильные номера на совпадение
            $NumberExistObj = Client_CheckMobileNumberExist( array($_REQUEST['MobilePhone'], $_REQUEST['MobilePhone1'], $_REQUEST['MobilePhone2']), @$_REQUEST['LoadedClientId'] );
            if($NumberExistObj) {
                $OwnerObj = User_GetUserObj($NumberExistObj[3]); // Clients.OwnerUserId
                $msg = 'Невозможно сохранить, т.к. номер <b>'.$NumberExistObj[2].'</b> уже привязан к клиенту: '.$NumberExistObj[1].', работающему с агентом: '.$OwnerObj->LastName.' '.$OwnerObj->FirstName.', тел: '.$OwnerObj->MobilePhone.')';
                echo '{"success":false,"message":"'.$msg.'"}';
                break;
            } else {
                if($_REQUEST['LoadedClientId']) {
                    // обновление существующего
                    list($Result, $SavedUserId, $ErrMsg) = Client_Update($_REQUEST);
                    if ($Result) {
                        echo '{"success":true,"LoadedClientId":' . $SavedUserId . ',"message":"Данные клиента успешно обновлены. "}';
                    } else {
                        echo '{"success":false,"message":"' . $ErrMsg . '"}';
                    }
                } else {
                    // новый клиент
                    list($Result, $SavedUserId, $ErrMsg) = Client_Create($_REQUEST);
                    if ($Result) {
                        echo '{"success":true,"LoadedClientId":' . $SavedUserId . ',"message":"Новый клиент успешно добавлен. "}';
                    } else {
                        echo '{"success":false,"message":"' . $ErrMsg . '"}';
                    }

                }
            }

            break;



        case 'SaveClientForm':

            if($_REQUEST['LoadedClientId']) {
                // обновление существующего
                list($Result, $ErrMsg) = User_Update($_REQUEST);
                if($Result) {
                    $StsRes = AddOrUpdateUserStatus($_REQUEST['LoadedUserId'], $_REQUEST['Status0Id']);

                    $GrpRes = AddOrUpdateUserGroup($_REQUEST['LoadedUserId'], $_REQUEST['Group0Id']);
                    (!$GrpRes) ? $AddMsg = ', но возникла ошибка в назначении группы' : $AddMsg = '';
                    $PosRes = AddOrUpdateUserPosition($_REQUEST['LoadedUserId'], $_REQUEST['Pos0Id']);
                    (!$PosRes) ? $AddMsg .= ', возникла ошибка в назначении должности!' : $AddMsg .= '';

                    $Params['LetterType'] = 'UserUpdate';
                    $Params['LetterData'] = $_REQUEST;
                    $EmailResult = Email_SimpleLetter($Params);
                    if(strlen($EmailResult)>0) {$EmailResult = ". ".$EmailResult; }

                    echo '{"success":true,"message":"Анкета пользователя успешно обновлена'.$AddMsg.$EmailResult.'"}';
                } else {
                    echo '{"success":false,"message":"'.$ErrMsg.'"}';
                }

            } else {
                // новый клиент
                list($Result, $SavedUserId, $ErrMsg) = User_Create($_REQUEST);
                if($Result) {
                    $StsRes = LinkUserIdToStatusId($SavedUserId, $_REQUEST['Status0Id'], 0);

                    $GrpRes = LinkUserIdToGroupId($SavedUserId, $_REQUEST['Group0Id'], 0);
                    (!$GrpRes) ? $AddMsg = ', но возникла ошибка в назначении группы. ' : $AddMsg = '. ';
                    $PosRes = LinkUserIdToPositionId($SavedUserId, $_REQUEST['Pos0Id'], 0);
                    (!$PosRes) ? $AddMsg .= ', возникла ошибка в назначении должности! ' : $AddMsg .= '. ';

                    // Отправляем приглашение на почту
                    $Params['LetterType'] = 'InvitationToNewUser';
                    $Params['LetterData'] = $_REQUEST;
                    $EmailResult = Email_SimpleLetter($Params);

                    echo '{"success":true,"LoadedUserId":' . $SavedUserId . ',"message":"Новый пользователь успешно добавлен'.$AddMsg.$EmailResult.'"}';
                } else {
                    echo '{"success":false,"message":"'.$ErrMsg.'"}';
                }
            }
            break;


        case "ArchivateClientById":
            $Result = ArchivateClientById( $_REQUEST['ClientId'] );
            if($Result) {
                echo '{"success":true}';
            } else {
                $Msg    = htmlentities( preg_replace('/\n/', '', mysql_error()) );
                echo '{"success":false,"message":"' . $Msg . '"}';
                // TODO централизовать вывод и протоколирование ошибок
            }
            break;


        case "RestoreClientById":
            $Result = RestoreClientById( $_REQUEST['ClientId'] );
            if($Result) {
                echo '{"success":true}';
            } else {
                $Msg    = htmlentities( preg_replace('/\n/', '', mysql_error()) );
                echo '{"success":false,"message":"' . $Msg . '"}';
                // TODO централизовать вывод и протоколирование ошибок
            }
            break;


        case 'GetMyClients':
            $Params['OrderByField'] = 'FirstName';
            $Params['OrderByTo']    = 'ASC';
            $Params['OnlyUserId'] = $CURRENT_USER->id;
            $arr = GetClientsArr($Params);
            foreach($arr as $TypesObj) {
                $element            = array();
                $element['id']      = $TypesObj->id;
                $element['VarName'] = $TypesObj->FirstName.' '.$TypesObj->SurName.' '.$TypesObj->LastName . " ({$TypesObj->MobilePhone})";
                $element['VarData'] = '';
                array_push($out, $element);
            }
            header("Content-Type: application/json;charset=UTF-8");
            echo json_encode($out, JSON_UNESCAPED_UNICODE);
            break;

        case 'ObjectErrorFixed':
            MarkObjectClear($_REQUEST['ObjectId']);
            header("Content-Type: application/json;charset=UTF-8");
            echo '{"success":true,"message":""}';
            break;

        case 'LoadObjectHistory':
            $Params              = array();
            $Params['Period']    = @$_REQUEST['Period'];
            $Params['Format']    = @$_REQUEST['Format'];
            $Params['EventType'] = @$_REQUEST['EventType'];
            $out = GetObjectHistory($_REQUEST['ObjectId'], $Params);
            //$out = nl2br($out);
            //echo $out;exit;
            if( !strlen($out) ) {
                $out = "Данные об ошибках устарели, пожалуйста, проверьте историю объекта";
            }

            $response               = (object) array();
            $response->success      = true;
            $response->data         = $out;

            header("Content-Type: application/json;charset=UTF-8");
            echo json_encode(
                $response,
                JSON_UNESCAPED_UNICODE);
            break;

        default:
            //TODO кто-то гадает - запротоколировать!


    }

