<?php
/*
 * Загрузка объектов из SmartAgent-YandexXml (только добавление, без обновления)
 *
 * 1. открываем smart xml
 * 2. по каждому объекту:
 *    если id в бд нет - добавляем,
 *    если id есть - пропускаем,
 * 3. обработка при добавлении:
 *    выясняем тип недвижимости, тип сделки, берем все поля, хэш не делаем;
 */
require(dirname(__FILE__) . '/../../Conf/Config.php');
ExitOnWebAccess(); // сервисы должны запускаться только в CLI
$GLOBALS['FirePHP']->setEnabled(false); // web лог не нужен в консоли
require(dirname(__FILE__) . "/../../Lib/Upload.php");

$ObjectsInFile = 0;
$SavedObjects  = 0;
$SourceFile   = $CONF['TempDir'] . 'SmartAgentTmpFile' . rand(1,1000) . '.xml';
DBConnect();
// Загружаем индивидуальные настройки клиента $CONF['SysParams'][xxx] (БД тбл: SysParams)
//LoadSysParams(); //$CONF['SysParams']['ImportObjectsUrl']




echo date('r')."\nLoading {$CONF['SysParams']['ImportObjectsUrl']}\n";              // берем текст внешнего xml
$RemoteXmlText = file_get_contents( $CONF['SysParams']['ImportObjectsUrl'] ) or die("can't get {$CONF['SysParams']['ImportObjectsUrl']}");
$FLength = strlen( $RemoteXmlText );
if(!file_put_contents($SourceFile, $RemoteXmlText)) {                  // сохраняем во временный файл
    die('ERROR: Cant save external source to '.$SourceFile." (length: $FLength)\n");
} else {
    echo "External source saved to: ".$SourceFile." (length: $FLength)\n";
}

$SimpleXmlObj = simplexml_load_file( $SourceFile ) or die("Error: Cannot create object"); // TODO проверку на вес файла


// TODO сделать автоматическое определение формата (yandex/cian/winner ?)


foreach($SimpleXmlObj->offer as $obj) {                     // обрабатываем каждый объект
    $InternalId = $obj->attributes()['internal-id'];
    $ObjectExistInDb = Import_CheckImportId( $InternalId );
    if( !$ObjectExistInDb ) {
        Import_YandexObjectFieldsToArr( $obj );             // главный механизм парсера и сохранения
    } else {
        $msg = "Object No:".$InternalId . " already exist\n";
        echo "$msg";
        SimpleLog($CONF['Log']['CrmImportLog'], $msg);
    }
}

if( !unlink($SourceFile) ) {SimpleLog($CONF['Log']['CrmImportLog'], "cant unlink " . $SourceFile);}


