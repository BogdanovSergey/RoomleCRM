<?php

//старье из юсина!!!!!!!!!!!!!!!
 /*
  * 1. соединяемся с базой
  * 2. если есть непрочитанные письма (тбл. DownloadedEmails) с названием "elistings" и темой /.*№\d$/
  *
  *     обрабатываем размер/масштаб фотки/ок из DownloadedEmailsд
  *
  *     4. добавляем в тбл ImagesListings ссылки на новые пути фоток
  *     обновляем estate - Elisting = yes
  */

    if( isset($_SERVER['HTTP_HOST']) ) { exit;} // сервисы должны запускаться только из консоли

    include(dirname(__FILE__) . "/../../conf/config.php");
    $site = new CSite($db);

    $GLOBALS['DBLink']        = new Database();
    $Emails['LettersFound']   = 0;
    $Emails['FilesFound']     = 0;
    $Emails['UnknownSubject'] = 0;

    if( CheckUnreadEmailsByServiceName('elistings') ) {                          // если есть непрочитанные письма
        $EmailsArr = GetEmailsArrByServiceName('elistings');                     // берем все письма
        for( $i=0; $i<count($EmailsArr);$i++) {  //echo '> '.$EmailsArr[$i]['subject']."\n";                                // для каждого письма
            if( preg_match('/.*истинг\s.?\d+$/iu', $EmailsArr[$i]['subject']) ) { // найдено письмо с правильной темой, обрабатываем приерепленные файлы
                $Emails['LettersFound']++;
                echo '> '.$EmailsArr[$i]['subject']."\n";
                $AttachmentsArr = GetEmailAttachmentsArrByEmailId($EmailsArr[$i]['id']); // берем все аттачи
                for( $f=0; $f<count($AttachmentsArr);$f++) {                              // для каждого файла
                    //input

                    $FileObj['FilePath'] = $CONFIG['SYSEmailAttachementsDir'].$AttachmentsArr[$f]['filename'];
                    list($a,$b,$c) = $site->MakeSmallMiddleBigImage( $FileObj );
                    $Emails['FilesFound']++;
                    //echo "$f\n";
                }
            } else {
                $Emails['UnknownSubject']++; //echo "--\n";
            }
        }

        if($Emails['FilesFound'] > 0) {
            MarkObjectForEListing($ObjectId, $ObjectType);                      // помечаем что у объекта есть фотки листингов (в тбл ImagesListings)
        }
    }
