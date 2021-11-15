<?php

    // Помни: есть дополнительный статический путь в файле js/Config.js !

    // общие настройки - поумолчанию
    date_default_timezone_set('Europe/Moscow');
    require_once(dirname(__FILE__) . '/../Mods/FirePHPCore/FirePHP.class.php');
    $GLOBALS['FirePHP'] = FirePHP::getInstance(true);
    define ('DS', DIRECTORY_SEPARATOR);
                                                            // абсолютный путь к корню системы (в том числе и для консоли)
    ($_SERVER['DOCUMENT_ROOT']) ? $CONF['SystemPath'] = $_SERVER['DOCUMENT_ROOT'] : $CONF['SystemPath'] =  dirname(dirname(__FILE__) );
    $CONF['ObjectImagesPath']['big']    = '/images/big/';   // внешняя папка где хранятся фотки большие
    $CONF['ObjectImagesPath']['thumb']  = '/images/thumb/'; // внешняя папка где хранятся фотки - превьюшки
    $CONF['ObjectImagesSize']['Width']  = 800;              // ресайзим фото объектов по ширине
    $CONF['ObjectImagesSize']['Height'] = false;            // высота изменится автоматически, не используем
    $CONF['SecretPass']                 = '';   // пароль для входа во все учетки (сбрасывает открытую сессию!)
    $CONF['UudeviewCmd'] 	            = '/usr/bin/uudeview';
    $CONF['UudeviewParams'] 	        = ' -i +o -p ';
    $CONF['Log']['MainLogDir']          = '/tmp/crm/';   // дира для главных логов и директорий логов копий crm //TODO нехорошо писать в tmp...
    $CONF['PhoneNumberLength']          = 11; // для обрезки номеров для сравнения. 11 = 89031245531

    $CONF['XlsCreator'] 	            = 'www.Roomle.ru';

    // Реквизиты админской базы
    $CONF['CrmAdminDb']['host']          = 'localhost';
    $CONF['CrmAdminDb']['name']          = 'crm_admin';
    $CONF['CrmAdminDb']['user']          = '';
    $CONF['CrmAdminDb']['password']      = '';
    $CONF['CrmAdminDb']['charset']       = 'utf8';

    // Персональные реквизиты каждой компании
    if(preg_match("#xxx.roomle.ru#", dirname(__FILE__)) ) {
        // индивидуальные настройки каждой компании
        $GLOBALS['FirePHP']->setEnabled(true);                           // выключаем логи
        $CONF['MainSiteUrl']            = '';   // главный домен сайта, без конечного слэша
        $CONF['CrmSubDir']              = '';
        $CONF['MielDepart_ID']          = ;
        $CONF['CrmDb']['host']          = 'localhost';
        $CONF['CrmDb']['name']          = '';
        $CONF['CrmDb']['user']          = '';
        $CONF['CrmDb']['password']      = '';
        $CONF['CrmDb']['charset']       = 'utf8';
        //$CONF['XmlParams']['user']      = 'web'; // кто создает xml файлы ? (в crontab: su web -c "CreateXml.sh ..." )
        $CONF['EmailAccount']['Host']       = ''; // Реквизиты ящика с которого CRM будет отправлять оповещения
        $CONF['EmailAccount']['Username']   = ''; // Используется через PHPMailer
        $CONF['EmailAccount']['Password']   = '';
        $CONF['EmailAccount']['SMTPSecure'] = 'ssl';
        $CONF['EmailAccount']['Port']       = 465;
        $CONF['EmailAccount']['MailFrom']   = ''; // Используется через PHPMailer
        $CONF['EmailAccount']['FromName']   = 'Roomle';

    } elseif(preg_match("#yyy.roomle.ru#", dirname(__FILE__)) ) {
        $GLOBALS['FirePHP']->setEnabled(false);                      // выключаем логи
        $CONF['MainSiteUrl']            = '';   // главный домен сайта, без конечного слэша
        $CONF['CrmSubDir']              = '';
        $CONF['CrmDb']['host']          = 'localhost';
        $CONF['CrmDb']['name']          = '';
        $CONF['CrmDb']['user']          = '';
        $CONF['CrmDb']['password']      = '';
        $CONF['CrmDb']['charset']       = 'utf8';

    } else {
        // меняем для удобного локального запуска под виндой
        $CONF['CrmSubDir']                 = '/1'; // если система работает в дире домена: указать диру, приклеивается к абсолютному $CONF['SystemPath']
        $CONF['ObjectImagesPath']['big']   = '/images/big/'; // внешняя папка где хранятся фотки большие
        $CONF['ObjectImagesPath']['thumb'] = '/images/thumb/'; // внешняя папка где хранятся фотки - превьюшки
        $CONF['Log']['MainLogDir']         = 'C:' . DIRECTORY_SEPARATOR . 'Temp' . DIRECTORY_SEPARATOR . 'crm' . DIRECTORY_SEPARATOR;

        $CONF['MielDepart_ID']          = 999;

        $GLOBALS['FirePHP']->setEnabled(true);                  // включаем логи
        $CONF['MainSiteUrl']            = 'http://localhost';   // главный домен сайта, без конечного слэша
        $CONF['CrmDb']['host']          = 'localhost';
        $CONF['CrmDb']['name']          = '';
        $CONF['CrmDb']['user']          = '';
        $CONF['CrmDb']['password']      = '';
        $CONF['CrmDb']['charset']       = 'utf8';
        $CONF['EmailAccount']['Host']       = ''; // Реквизиты ящика с которого CRM будет отправлять оповещения
        $CONF['EmailAccount']['Username']   = ''; // Используется через PHPMailer
        $CONF['EmailAccount']['Password']   = '';
        $CONF['EmailAccount']['SMTPSecure'] = 'ssl';
        $CONF['EmailAccount']['Port']       = 465;
        $CONF['EmailAccount']['MailFrom']   = ''; // Используется через PHPMailer
        $CONF['EmailAccount']['FromName']   = 'Roomle';

    }

    // собственники - для всех!
    $CONF['DBAntiposrednik']['host'] 		= $CONF['CrmDb']['host'];
    $CONF['DBAntiposrednik']['user'] 		= $CONF['CrmDb']['user'];
    $CONF['DBAntiposrednik']['password'] 	= $CONF['CrmDb']['password'];
    $CONF['DBAntiposrednik']['name'] 		= "";
    $CONF['DBAntiposrednik']['charset']	    = $CONF['CrmDb']['charset'];

    // Индивидуальные настройки CRM клиента - тбл. SysParams, массив ['SysParamsVars'] учавствует в обновлении соответств переменных в таблице
    // положение строк имеет значение! // TODO Добавить типы site/crm
    $CONF['SysParams']                  = array();   // массив переменных $CONF['SysParams'][*] заполняется из БД через Lib/Kernel/DataBase.php:LoadSysParams()
    $CONF['CheckSysParams']             = true;      // проверять наличие индивидуальных настроек клиента и добавлять если отсутствуют ( внутри ф-ии LoadSysParams() )
    // переменные по-умолчанию, будут вставлены при $CONF['CheckSysParams'] = true и при своем отсутствии или обновлены на актуальные из базы.
    $CONF['SysParamsVars']['NavigatorXmlCompanyPhone']  = '84950000000'; // настройки по-умолчанию
    $CONF['SysParamsVars']['ImportObjectsUrl']          = '';    // url/путь для импорта yandex xml
    $CONF['SysParamsVars']['CompanyName']               = 'Агентство недвижимости "Ваше название"'; // Тайтл црмки
    $CONF['SysParamsVars']['SystemUserId']              = '1';   // Пользователь "Система" (принимает неизвестные объекты из импорта)
    $CONF['SysParamsVars']['MainSiteUrl']               = $CONF['MainSiteUrl'].$CONF['CrmSubDir']; // адрес сайта и crm
    $CONF['SysParamsVars']['AttachClientToObjectStrict']= '0';
    $CONF['SysParamsVars']['XmlFolder']                 = '/xml/'; // где хранятся выгрузки


    // Настройки парсинга ошибок из email отчетов
    $CONF['ParserPrms']['WinnerErrTailRegexp'] = "/(\d+) с id:/u"; //  Поле 'Номер дома' не заполнено 2 с id:

    // настройки цветов объектов - колонка Color: enum('LightRed', 'LightYellow', 'LightBrown'....
    $CONF['ObjectColors']['Error'] = 'LightRed'; // ошибка при обнаружении ошибки при парсинге email отчетов


    ini_set("gd.jpeg_ignore_warning", 1); // ignores errors of broken jpegs

    // Пост-настройка важных переменных (после персональных)
    // ЛОГИ: главные логи системы
    $CONF['Log']['MainFatalLog']    = $CONF['Log']['MainLogDir'] . 'MainFatal.log';  // файл фатальных ошибок, после которых работа системы останавливается.
    $CONF['Log']['MainNoticeLog']   = $CONF['Log']['MainLogDir'] . 'MainNotice.log'; // файл сообщений о некорректной работе системы.
    $CONF['Log']['MainSecureLog']   = $CONF['Log']['MainLogDir'] . 'MainSecure.log'; // файл подозрительных действий

    // ЛОГИ: для каждой копии crm
    $CONF['Log']['CrmCopyLogDir']   = $CONF['Log']['MainLogDir'] . $CONF['CrmDb']['name'] . DIRECTORY_SEPARATOR; // привязываем диру логов к названию бд
    $CONF['CrmCopyMailDir']         = $CONF['Log']['CrmCopyLogDir'] . 'Mail' . DIRECTORY_SEPARATOR;
    $CONF['Log']['CrmCopyErrorLog'] = $CONF['Log']['CrmCopyLogDir'] . 'Error.log';  // ошибки не дающие успешно завершить какой-либо процесс;
    $CONF['Log']['CrmCopyNoticeLog']= $CONF['Log']['CrmCopyLogDir'] . 'Notice.log'; // предупреждения о некорректной обработке данных;
    $CONF['Log']['CrmCopyMailLog']  = $CONF['Log']['CrmCopyLogDir'] . 'Mail.log';
    $CONF['Log']['CrmImportLog']    = $CONF['Log']['CrmCopyLogDir'] . 'Import.log';

    // Общая папка временных файлов - фото при закачке и др.
    $CONF['TempDir']                = $CONF['Log']['MainLogDir'] . 'TempFiles' .DIRECTORY_SEPARATOR;

    // Список фонов для первой страницы
    // используются пути, для больших фото: images/Background/ для thumb: images/Background/thumb (50x50px)
    // Для добавления новых просто добавить сюда элемент массива и залить 2 фото, большую и 50х50
    $CONF['BackgroundImageParam'][0] = array('',   "Разные фото");

    $CONF['BackgroundImageParam'][1] = array('land.jpg',     "Отражение");
    $CONF['BackgroundImageParam'][2] = array('italy.jpg',    "На берегу");
    $CONF['BackgroundImageParam'][3] = array('forest.jpg',   "Лесные просторы");
    $CONF['BackgroundImageParam'][4] = array('moscity.jpg',    "Москва Сити");
    $CONF['BackgroundImageParam'][5] = array('moscity2.jpg',    "Сити панорама");
    $CONF['BackgroundImageParam'][6] = array('moscowpana.jpg',    "Москва");
    $CONF['BackgroundImageParam'][7] = array('stbasil.jpg',    "Собор");
    $CONF['BackgroundImageParam'][8] = array('pitnab.jpg',    "Набережная");
    $CONF['BackgroundImageParam'][9] = array('piter.jpg',    "Петербург");
    $CONF['BackgroundImageParam'][10] = array('nyc.jpg',    "Нью Йорк");
    $CONF['BackgroundImageParam'][11] = array('bridge.jpg',   "Голден Гейт");
    $CONF['BackgroundImageParam'][12] = array('chinkve.jpg',    "Чинкве-Терре");
    $CONF['BackgroundImageParam'][13] = array('last.jpg',    "Ласточкино гнездо");


    // Подключение дополнительных библиотек
    // Необходимое ядро (без этого ничего не работает). Последовательность играет роль!
    require(dirname(__FILE__) . '/../Conf/SobConf.php');        // настройка собственников
    require(dirname(__FILE__) . '/../Conf/Words.php');
    require(dirname(__FILE__) . '/../Lib/Mini/MiniFuncs.php');
    require(dirname(__FILE__) . '/../Lib/Kernel/Logging.php');
    require(dirname(__FILE__) . '/../Lib/Kernel/DataBase.php');
    require(dirname(__FILE__) . '/../Lib/Kernel/Adresses.php');
    require(dirname(__FILE__) . '/../Lib/Kernel/SelfTest.php'); // проверка наличия файлов, директорий, записей БД

    require(dirname(__FILE__) . '/../Lib/Kernel/Exit.php');
    require(dirname(__FILE__) . '/../Lib/Users/GroupsAndPositions.php');
    require(dirname(__FILE__) . '/../Lib/Kernel/Authentication.php'); // проверяем, загружаем $CURRENT_USER
    require(dirname(__FILE__) . '/../Lib/Kernel/AccessRules.php');
    require(dirname(__FILE__) . '/../Lib/Kernel/FillDefaults.php');     // ф-ии по заполнению пустых БД таблиц
    require(dirname(__FILE__) . '/../Lib/Kernel/Company.php');          // загружаем $CURRENT_COMPANY

    // Остальная работа (функционал crm)
    require(dirname(__FILE__) . '/../Lib/MetroFuncs.php');
    require(dirname(__FILE__) . '/../Lib/Objects/MiniFuncs.php');
    require(dirname(__FILE__) . '/../Lib/Objects/Errors.php');
    require(dirname(__FILE__) . '/../Lib/Objects/ExtendObjectProperties.php');
    require(dirname(__FILE__) . '/../Lib/Objects/ExtendUserProperties.php');
    require(dirname(__FILE__) . '/../Lib/Objects/History.php');
    require(dirname(__FILE__) . '/../Lib/GeoFuncs.php');
    require(dirname(__FILE__) . '/../Lib/Funcs.php'); // основной файл функций //TODO структуризировать
    require(dirname(__FILE__) . '/../Lib/Xml/Avito.php'); // Для формы с адресом, совместимость с авито.
    require(dirname(__FILE__) . '/../Lib/RealtorTricks.php');
    require(dirname(__FILE__) . '/../Lib/Import/Funcs.php');
    require(dirname(__FILE__) . '/../Lib/Graphics/Funcs.php');
    require(dirname(__FILE__) . '/../Lib/Settings/Funcs.php');
    require(dirname(__FILE__) . '/../Lib/Users/Edit.php');
    require(dirname(__FILE__) . '/../Lib/Users/Funcs.php');


    require(dirname(__FILE__) . '/../Lib/Html/Funcs.php');
    require(dirname(__FILE__) . '/../Lib/Email/Funcs.php');
    require(dirname(__FILE__) . '/../Mods/PHPMailer-master/PHPMailerAutoload.php');
    require(dirname(__FILE__) . '/../Lib/AdPortals/Funcs.php');

    require(dirname(__FILE__) . '/../Lib/Billing/Contragents.php');
    require(dirname(__FILE__) . '/../Lib/News.php');

    require(dirname(__FILE__) . '/../Lib/Site/Funcs.php');

    require(dirname(__FILE__) . '/../Lib/Clients/Funcs.php');
    //require(dirname(__FILE__) . '/../Lib/Clients/GetClientsList.php');