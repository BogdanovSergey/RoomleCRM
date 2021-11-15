<?php


    // общие настройки - поумолчанию
    date_default_timezone_set('Europe/Moscow');
    require_once(dirname(__FILE__) . '/../Mods/FirePHPCore/FirePHP.class.php');
    $GLOBALS['FirePHP'] = FirePHP::getInstance(true);

    $CONF['SystemPath']                = $_SERVER['DOCUMENT_ROOT']; // абсолютный путь к корню системы
    $CONF['ObjectImagesPath']['big']   = '/images/big/';   // внешняя папка где хранятся фотки большие
    $CONF['ObjectImagesPath']['thumb'] = '/images/thumb/'; // внешняя папка где хранятся фотки - превьюшки

    $CONF['UudeviewCmd'] 	           = '/usr/bin/uudeview';
    $CONF['UudeviewParams'] 	       = ' -i +o -p ';
    $CONF['Log']['MainLogDir']         = '/tmp/crm/';   // дира для главных логов и директорий логов копий crm //TODO нехорошо писать в tmp...

    // Персональные реквизиты каждой компании
    if(preg_match("#lefortovo.roomle.ru#", dirname(__FILE__)) ) {
        // индивидуальные настройки каждой компании
        $GLOBALS['FirePHP']->setEnabled(false);                           // выключаем логи
        $CONF['MainSiteUrl']            = '';   // главный домен сайта, без конечного слэша
        $CONF['CrmDb']['host']          = 'localhost';
        $CONF['CrmDb']['name']          = '';
        $CONF['CrmDb']['user']          = '';
        $CONF['CrmDb']['password']      = '';
        $CONF['CrmDb']['charset']       = 'utf8';
        //$CONF['XmlParams']['user']      = 'web'; // кто создает xml файлы ? (в crontab: su web -c "CreateXml.sh ..." )
