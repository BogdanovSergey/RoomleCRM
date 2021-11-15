<?php
/*
 * ПРОТОКОЛИРОВАНИЕ:
 *
 *  Файлы:
 *      /var/log/crm/                   -   директория с главными файлами протоколов и директориями для логов других копий crm;
 *      /var/log/crm/MainFatal.log      -   файл фатальных ошибок, после которых работа системы останавливается.    MainFatalLog()
 *      /var/log/crm/MainNotice.log     -   файл сообщений о некорректной работе системы.                           MainNoticeLog()
 *      /var/log/crm/MainSecure.log     -   файл подозрительных действий.                                           MainSecureLog()
 *
 *      /var/log/crm/somecompany/             - директория с ошибками данных в конкретной копии crm;
 *      /var/log/crm/somecompany/Error.log    - ошибки не дающие успешно завершить какой-либо процесс;      CrmCopyErrorLog()
 *      /var/log/crm/somecompany/Notice.log   - предупреждения о некорректной обработке данных;             CrmCopyNoticeLog()
 *
 * База данных:
 *      тбл: SysErrors
 *
 */


    function CoreLog($msg) {
        MainFatalLog($msg);
    }

    function SimpleLog($LogFilePath, $msg) {
        global $CONF;
        if( !@file_put_contents($LogFilePath, $msg, FILE_APPEND) ) { echo __FUNCTION__."(): cant write to $LogFilePath"; }
    }

    function MainFatalLog($msg) {
        global $CONF;
        $msg = date('r')." $msg\n";
        if( !@file_put_contents($CONF['Log']['MainFatalLog'], $msg, FILE_APPEND) ) { echo __FUNCTION__."(): cant write to {$CONF['Log']['MainFatalLog']}";}
    }

    function MainNoticeLog($msg, $ParamsArr=array()) {
        global $CONF;
        if(@$ParamsArr['OnlyMsg']) {
            $msg = "\t$msg\n";
        } else {
            $msg = date('r')."\n".
                "\tPHP_SELF: ".@$_SERVER['PHP_SELF'].", QUERY_STRING: ".@$_SERVER['QUERY_STRING']."\n".
                "\tget_current_user(): ".get_current_user().", _SERVER['USER']: ".@$_SERVER["USER"]."\n".
                "\t$msg\n";
        }

        if( !@file_put_contents($CONF['Log']['MainNoticeLog'], $msg, FILE_APPEND) ) { echo __FUNCTION__."(): cant write to {$CONF['Log']['MainNoticeLog']}";}
    }

    function MainSecureLog($msg) {
        global $CONF;
        $msg = date('r')."\n".
            "\tIP: : {$_SERVER['REMOTE_ADDR']}, PHP_SELF: {$_SERVER['PHP_SELF']}, QUERY_STRING: {$_SERVER['QUERY_STRING']}\n".
            "\tget_current_user: ".get_current_user()."\n".
            "\t_SERVER['USER']: ".@$_SERVER["USER"]."\n".
            " $msg\n";
        if( !@file_put_contents($CONF['Log']['MainSecureLog'], $msg, FILE_APPEND) ) { echo __FUNCTION__."(): cant write to {$CONF['Log']['MainSecureLog']}";}
    }

    function CrmCopyNoticeLog($msg, $ParamsArr=array()) {
        global $CONF;
        if(@$ParamsArr['OnlyMsg']) {
            $msg = "\t$msg\n";
        } else {
            $msg = date('r')."\n".
                "\tPHP_SELF: ".@$_SERVER['PHP_SELF'].", QUERY_STRING: ".@$_SERVER['QUERY_STRING']."\n".
                "\tget_current_user(): ".get_current_user().", _SERVER['USER']: ".@$_SERVER["USER"]."\n".
                "\t$msg\n";
        }
        if( !@file_put_contents($CONF['Log']['CrmCopyNoticeLog'], $msg, FILE_APPEND) ) { echo __FUNCTION__."(): cant write to {$CONF['Log']['CrmCopyNoticeLog']}";}
    }
    function CrmCopyErrorLog($msg, $ParamsArr=array()) {
        global $CONF;
        if(@$ParamsArr['OnlyMsg']) {
            $msg = "\t$msg\n";
        } else {
            $msg = date('r')."\n".
                "\tPHP_SELF: ".@$_SERVER['PHP_SELF'].", QUERY_STRING: ".@$_SERVER['QUERY_STRING']."\n".
                "\tget_current_user(): ".get_current_user().", _SERVER['USER']: ".@$_SERVER["USER"]."\n".
                "\t$msg\n";
        }
        if( !@file_put_contents($CONF['Log']['CrmCopyErrorLog'], $msg, FILE_APPEND) ) { echo __FUNCTION__."(): cant write to {$CONF['Log']['CrmCopyErrorLog']}";}
    }