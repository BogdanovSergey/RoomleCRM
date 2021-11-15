<?php
/*
 * Главный файл-сервис для запуска других сервисов?
 * Файл пропиывается в cron
 */
//if( isset($_SERVER['HTTP_HOST']) ) {exit;} // сервисы должны запускаться только в CLI

require(dirname(__FILE__) . '/../Conf/Config.php');
$GLOBALS['FirePHP']->setEnabled(false); // в сервисе не нужен FirePhp

DBConnect();




if(CheckUnparsedEmails()) {         //  Если появились необработанные письма
    // Winner_ReportParser():           обрабатываем отчет от виннера
    //      ObjectsErrorFound()         по каждому объекту с ошибкой
    //          MarkObjectError()       ставим маркер HasErrors и Color
    //          WriteObjectError()      пишем в тбл.ObjectErrors id объекта и текст ошибки
    require(dirname(__FILE__) . '/../Lib/Email/Parsers/Go_Winner.php');



} else {
    echo "All emails are parsed\n";
}

