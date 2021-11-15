<?php


    // проверка на наличие папок ///////////////////////////////////////////
    // возможно не нужно запускать проверку каждый раз, подумать об "проверочном режиме, инсталляции"
    $FatalErrorsText    = '';
    $DirsToCheck        = array($CONF['TempDir'],
                                $CONF['Log']['MainLogDir'],
                                $CONF['Log']['CrmCopyLogDir'],
                                $CONF['CrmCopyMailDir']);

    foreach($DirsToCheck as $dir) {
        if(!file_exists($dir)) {
            $FatalErrorsText .= "Directory $dir doesnt exist - ";
            ( mkdir($dir) ) ? $FatalErrorsText .= " CREATED\n" : $FatalErrorsText .= "CANT CREATE\n";
        }
    }


    // проверка на возможность записи в папку/файл ///////////////////////////////////////////
    $WriteCheckArr        = array(  $CONF['SystemPath'] . $CONF['CrmSubDir'] . $CONF['ObjectImagesPath']['big'],
                                    $CONF['SystemPath'] . $CONF['CrmSubDir'] . $CONF['ObjectImagesPath']['thumb'],
                                    $CONF['Log']['MainLogDir']
    );
    foreach($WriteCheckArr as $itm) {
        if (!is_writable($itm)) {
//            $FatalErrorsText .= "Directory or file $itm is not writable1\n";
        }
    }




    /*/ TODO сократить текст, сделать цикл
    if(!file_exists($CONF['Log']['MainLogDir'])) {
        $FatalErrorsText .= "Directory {$CONF['Log']['MainLogDir']} doesnt exist - ";
        ( mkdir($CONF['Log']['MainLogDir']) ) ? $FatalErrorsText .= " CREATED\n" : $FatalErrorsText .= "CANT CREATE\n";
    }
    if(!file_exists($CONF['Log']['CrmCopyLogDir'])) {
        $FatalErrorsText .= "Directory {$CONF['Log']['CrmCopyLogDir']} doesnt exist - ";
        ( mkdir($CONF['Log']['CrmCopyLogDir']) ) ? $FatalErrorsText .= " CREATED\n" : $FatalErrorsText .= "CANT CREATE\n";
    }
    if(!file_exists( $CONF['CrmCopyMailDir'] )) {
        $FatalErrorsText .= "Directory {$CONF['CrmCopyMailDir']} doesnt exist - ";
        ( mkdir($CONF['CrmCopyMailDir']) ) ? $FatalErrorsText .= " CREATED\n" : $FatalErrorsText .= "CANT CREATE\n";
    }
*/


    //////////////

    if(!file_exists($CONF['Log']['MainFatalLog'])) {
        $FatalErrorsText .= "MainFatalLog {$CONF['Log']['MainFatalLog']} doesnt exist!";
        ( file_put_contents($CONF['Log']['MainFatalLog'], ' ') >= 1 ) ? $FatalErrorsText .= " CREATED\n" : $FatalErrorsText .= "CANT CREATE\n";
        @chmod($CONF['Log']['MainFatalLog'], 0666); // TODO разобраться со взаимодействием юзеров
    }
    if(!file_exists($CONF['Log']['MainNoticeLog'])) {
        $FatalErrorsText .= "MainNoticeLog {$CONF['Log']['MainNoticeLog']} doesnt exist!";
        ( file_put_contents($CONF['Log']['MainNoticeLog'], ' ')  >= 1 ) ? $FatalErrorsText .= " CREATED\n" : $FatalErrorsText .= "CANT CREATE\n";
        @chmod($CONF['Log']['MainNoticeLog'], 0666);
    }
    if(!file_exists($CONF['Log']['MainSecureLog'])) {
        $FatalErrorsText .= "MainSecureLog {$CONF['Log']['MainSecureLog']} doesnt exist!";
        ( file_put_contents($CONF['Log']['MainSecureLog'], ' ')  >= 1 ) ? $FatalErrorsText .= " CREATED\n" : $FatalErrorsText .= "CANT CREATE\n";
        @chmod($CONF['Log']['MainSecureLog'], 0666);
    }
    if(!file_exists($CONF['Log']['CrmCopyErrorLog'])) {
        $FatalErrorsText .= "CrmCopyErrorLog {$CONF['Log']['CrmCopyErrorLog']} doesnt exist!";
        ( file_put_contents($CONF['Log']['CrmCopyErrorLog'], ' ')  >= 1 ) ? $FatalErrorsText .= " CREATED\n" : $FatalErrorsText .= "CANT CREATE\n";
        @chmod($CONF['Log']['CrmCopyErrorLog'], 0666);
    }
    if(!file_exists($CONF['Log']['CrmCopyNoticeLog'])) {
        $FatalErrorsText .= "CrmCopyNoticeLog {$CONF['Log']['CrmCopyNoticeLog']} doesnt exist!";
        ( file_put_contents($CONF['Log']['CrmCopyNoticeLog'], ' ')  >= 1 ) ? $FatalErrorsText .= " CREATED\n" : $FatalErrorsText .= "CANT CREATE\n";
        @chmod($CONF['Log']['CrmCopyNoticeLog'], 0666);
    }
    if(!file_exists($CONF['Log']['CrmCopyMailLog'])) {
        $FatalErrorsText .= "CrmCopyMailLog {$CONF['Log']['CrmCopyMailLog']} doesnt exist!";
        ( file_put_contents($CONF['Log']['CrmCopyMailLog'], ' ')  >= 1 ) ? $FatalErrorsText .= " CREATED\n" : $FatalErrorsText .= "CANT CREATE\n";
        @chmod($CONF['Log']['CrmCopyMailLog'], 0666);
    }
    if(!file_exists($CONF['Log']['CrmImportLog'])) {
        $FatalErrorsText .= "CrmImportLog {$CONF['Log']['CrmImportLog']} doesnt exist!";
        ( file_put_contents($CONF['Log']['CrmImportLog'], ' ')  >= 1 ) ? $FatalErrorsText .= " CREATED\n" : $FatalErrorsText .= "CANT CREATE\n";
        @chmod($CONF['Log']['CrmImportLog'], 0666);
    }


    // дополнительные средства необходимые для работы
    /*if(preg_match('/No such file/', system($CONF['UudeviewCmd'])) ) { // TODO как сделать проверку файла Uudeview?
        $FatalErrorsText .= "Need to yum install {$CONF['UudeviewCmd']}!";
    }*/


    if(strlen($FatalErrorsText) > 0) {
        SelfTestExit($FatalErrorsText);
    }

    function SelfTestExit($msg) {
        global $CONF;
        $msg = date('r')." $msg\n";
        echo $msg;
        @file_put_contents( $CONF['Log']['MainFatalLog'], $msg, FILE_APPEND);
        exit;
    }


