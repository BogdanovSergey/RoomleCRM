#!/usr/bin/php -q
<?php

/* Use -q so that php doesn't print out the HTTP headers
 * https://github.com/stuporglue/mailreader
 */

/*    $str = '';
    $fd = fopen('php://stdin', 'r');
    while(!feof($fd)){ $str .= fread($fd, 1024); }
    file_put_contents('/tmp/ooooo', $str, FILE_APPEND);
    exit;
*/

require_once(dirname(__FILE__) . "/../Conf/Config.php");    // CRM configs
require_once(dirname(__FILE__) . "/mailReader.php");           // library
//require_once(dirname(__FILE__) . "/../Lib/Email/mailReader.php");           // library
ExitOnWebAccess(); // сервисы должны запускаться только в CLI

// Set a long timeout in case we're dealing with big files
set_time_limit(600);
ini_set('max_execution_time',600);
$ServiceName    = "GeneralEmail"; //  этот конфиг описывает настройки для сервиса забора писем с фотками листингов


// Anything printed to STDOUT will be sent back to the sender as an error!
// error_reporting(-1);
// ini_set("display_errors", 1);


// Require the file with the mailReader class in it


// Configure your MySQL database connection here
try {
    $pdo = new PDO("mysql:host={$CONF['CrmDb']['host']};dbname={$CONF['CrmDb']['name']};charset={$CONF['CrmDb']['charset']}", $CONF['CrmDb']['user'], $CONF['CrmDb']['password']);
} catch (PDOException $e) {
    MainFatalLog(__FILE__.": pdoDbException");
    throw new pdoDbException($e);
}

$CheckAllowedSenders = false;
// Who can send files to through this script?
$allowed_senders    = Array('MailFromAuthorized@mailbox.ru');
$mr                 = new mailReader($CONF['CrmCopyMailDir'], $CheckAllowedSenders, $allowed_senders, $ServiceName, $pdo);
$mr->save_msg_to_db = TRUE;
$mr->send_email     = false; // Send confirmation e-mail back to sender?
// Example of how to add additional allowed mime types to the list
$mr->CheckMimeTypes = FALSE;    // enable check of specific file extentions?
/*$mr->allowed_mime_types[] = 'text/csv';
$mr->allowed_mime_types[] = 'application/vnd.ms-excel';
$mr->allowed_mime_types[] = 'application/vnd.ms-office';
*/
$mr->readEmail();

