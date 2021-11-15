<?php

    $SQL['WinnerEmail'] = 'info@baza-winner.ru';

    $sql = "SELECT
                *
            FROM
                DownloadedEmails
            WHERE
                MailFrom LIKE '{$SQL['WinnerEmail']}' AND
                ParsedAt IS NULL AND
                ParsingNow IS NULL
            ORDER BY id";

    $res = mysql_query($sql);
    $Count = 0;
    while($Email = mysql_fetch_object($res)) {
        $Count++;
        echo "Parsing EmailId: $Email->id\n";
        EmailParsingStart($Email->id);

        $Params['ContragentSysName'] = 'winner';    // 'winner' взят из тбл. BillContragents:SysName
        $Params['ContragentId']      = GetContragentIdBySysName( $Params['ContragentSysName'] );
        $Params['EmailId']           = $Email->id;
        $Params['ErrorText']         = '';         // будет заполнено по ходу дела

        Winner_ReportParser($Email->DecodedBody, $Params);

        EmailParsingFinished($Email->id);

    }

    echo "Parsed emails: $Count\n";


    function Winner_ReportParser($EmailText, $Params) {
        // выводит 2 массива (id'шники объектов и ассоц с текстами ошибок по id)
        // ((123,124,125), [123]=['лала1, лала2'], [124]=['лала1, лала2'], [125]=['лала1, лала2'])
        /* Как письмо выглядит сейчас:

        URL: http://xxx.roomle.ru/xml/winner_flats.xml
        Всего объявлений: 80
        Прошли на публикацию: 74
        Объявлений с ошибками: 6

        Поле 'Номер дома' не заполнено 2 с id:
        459, 478
        Поле 'Улица' не заполнено 2 с id:
        468, 478

        */
        global $CONF;
        $IdsArr         = array();
        $ErrorsCount    = 0;
        $ObjectErrors   = array();
        $LogParams['OnlyMsg'] = false;                           // для кратких логов
        $StringsArr     = preg_split("/\n/", $EmailText);
        $StartString    = 5;                                       // с какой строки начинать

        for($i=$StartString; $i<count($StringsArr);$i=$i+2) {   // пробегаем через строчку
            if(!isset($StringsArr[$i]) || !isset($StringsArr[$i+1])) {break;}
            //echo "text: ".$StringsArr[$i]."\n";
            //echo "ids: ".$StringsArr[$i+1]."\n";
            $ErrFullText = $StringsArr[$i];
            //$ErrFullText = htmlspecialchars($ErrFullText); // не раб из-за кодировки
            $ErrFullText  = preg_replace('/[\'\"]/u','',$ErrFullText);          // чистим

            $ObjectIdsArr = preg_split("/, /u", $StringsArr[$i+1]);            // берем id объектов

            if(Winner_CheckErrTextTailWithIds($ErrFullText, $ObjectIdsArr) ) {
                // со строчками все в порядке
                $ErrText             = preg_replace($CONF['ParserPrms']['WinnerErrTailRegexp'], '', $ErrFullText); // убираем хвост
                $Params['ErrorText'] = $ErrText;
                ObjectsErrorFound($ObjectIdsArr, $Params); // для кажд объекта поставить маркер ошибки, историю
                foreach($ObjectIdsArr as $ObjectId) {
                    // накопить текст ошибок для каждого объекта
                    if( !isset($ObjectErrors[ $ObjectId ]) ) { $ObjectErrors[ $ObjectId ] = "При размещении рекламы в базе Winner были обнаружены следующие ошибки: \n\n"; }
                    $ObjectErrors[ $ObjectId ] .= "Ошибка в объекте №$ObjectId: " . $Params['ErrorText'] . "\n"; // TODO сделать ссылку на объект (чтоб сразу открывался)
                    $ErrorsCount++;
                    // TODO дополнить описание объекта: тип и улица с домом
                }

            } else {
                $msg =  "EmailId: {$Params['EmailId']}\n".
                        "Winner_CheckErrTextTailWithIds(): необходимо исправить парсинг отчета (ErrText, Ids count):\n".
                        "$ErrFullText, ".count($ObjectIdsArr);
                echo "$msg\n";
                MainNoticeLog($msg, $LogParams);
            }
        }
        echo "В отчете ".$Params['ContragentSysName']." обнаружено $ErrorsCount объектов с ошибками\n";
        PutEmailsByObjectIds($ObjectErrors); // ставим в очередь на рассылку

    }

    function Winner_CheckErrTextTailWithIds($ErrFullText, $IdsArr) {
        // сверяем кол-во эл-ов массива со строкой, правильно ли прошел парсинг по прописанному числу id'шников в тексте ошибок:
        //
        // Поле 'Номер дома' не заполнено 2 с id:
        //                               /\
        global $CONF;
        $out = false;
        preg_match($CONF['ParserPrms']['WinnerErrTailRegexp'], $ErrFullText, $Fetch); //
        $IdsSummFromText =  (integer) $Fetch[1];
        echo "В отчете указано $IdsSummFromText, отпарсено " .count($IdsArr). " ошибок.\n";
        if( $IdsSummFromText == count($IdsArr) ) {
            $out = true;
        }
        return $out;
    }