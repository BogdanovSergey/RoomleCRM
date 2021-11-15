<?php
// Обрабатываем письма из stdin и сохраняем в тбл: DownloadedEmails, DownloadedEmailsFiles
// Кодировка меняется на стр: 320
require_once('/usr/share/pear/Mail/mimeDecode.php');

/*
 * @class mailReader.php
 *
 * @brief Recieve mail and attachments with PHP
 *
 * Support: 
 * http://stuporglue.org/mailreader-php-parse-e-mail-and-save-attachments-php-version-2/
 *
 * Code:
 * https://github.com/stuporglue/mailreader
 *
 * See the README.md for the license, and other information
 */
class mailReader {
    var $saved_files    = Array();
    var $FilesCount     = 0;
    var $OriginalBody   = ''; //
    //var $SysLogFile   = '/tmp/m/mailReader.log';
    var $send_email     = FALSE; // Send confirmation e-mail back to sender?
    var $save_msg_to_db = FALSE; // Save e-mail message and file list to DB?
    var $save_directory; // A safe place for files. Malicious users could upload a php or executable file, so keep this out of your web root
    var $allowed_senders = Array(); // Allowed senders is just the email part of the sender (no name part)
    var $allowed_mime_types = Array(
        'audio/wave',
        'application/pdf',
        'application/zip',
        'application/octet-stream',
        'image/jpeg',
        'image/png',
        'image/gif',
    );
    var $debug = TRUE;

    var $raw = '';
    var $decoded; // uuencoded file data
    var $notdecoded;
    var $from;
    var $subject;
    var $body;


    /**
     * @param $save_directory (required) A path to a directory where files will be saved
     * @param $allowed_senders (required) An array of email addresses allowed to send through this script
     * @param $pdo (optional) A PDO connection to a database for saving emails
     */
    public function __construct($save_directory, $CheckAllowedSenders, $allowed_senders, $ServiceName, $pdo = NULL) {
        MainNoticeLog(  __FUNCTION__ . "($save_directory, [\$allowed_senders], $ServiceName)"); // SYS LOGGING

        if(!preg_match('|/$|',$save_directory)) { $save_directory .= '/'; } // add trailing slash if needed
        $this->CheckAllowedSenders   = $CheckAllowedSenders;
        $this->save_directory   = $save_directory;
        $this->allowed_senders  = $allowed_senders;
        $this->pdo              = $pdo;
        $this->ServiceName      = $ServiceName;
        $this->ContentType      = '';
        $this->ContentTransferEncoding = ''; // base64/8bit/
    }

    /**
     * @brief Read an email message
     *
     * @param $src (optional) Which file to read the email from. Default is php://stdin for use as a pipe email handler
     *
     * @return An associative array of files saved. The key is the file name, the value is an associative array with size and mime type as keys.
     */
    public function readEmail($src = 'php://stdin') {
        $LogParamsArr['OnlyMsg'] = true; // не добавлять лишнего описания в лог
        // Process the e-mail from stdin
        $fd = fopen($src, 'r');
        while(!feof($fd)){ $this->raw .= fread($fd,1024); }
        $this->OriginalBody = $this->raw;
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //MainNoticeLog(  __FUNCTION__ . "($src strlen = ".strlen($this->raw).")", $LogParamsArr);
        // Now decode it!
        // http://pear.php.net/manual/en/package.mail.mail-mimedecode.decode.php
        $decoder = new Mail_mimeDecode($this->raw);
        $this->decoded = $decoder->decode(
            Array(
                'decode_headers' => false,
                'include_bodies' => TRUE,
                'decode_bodies' => false
            )
        );

        // Set $this->from_email [and check if it's allowed]

        $this->ContentType = $this->decoded->headers['content-type'];// Сохраняем кодировку письма для последующего перекодирования
        $this->ContentTransferEncoding = $this->decoded->headers['content-transfer-encoding'];
        $this->from = $this->decoded->headers['from'];
        $this->from_email = preg_replace('/.*<(.*)>.*/',"$1",$this->from);
        if($this->CheckAllowedSenders && !in_array($this->from_email, $this->allowed_senders)) {
            //проверяем на разрешенных отправителей
            MainNoticeLog(  __FUNCTION__ . "(): {$this->from_email} not an allowed sender", $LogParamsArr);
            SystemExit();
        }
//MainNoticeLog(  "----> from_email: {$this->from_email}", $LogParamsArr);

        // Set the $this->subject
        $this->subject = $this->decoded->headers['subject'];
        MainNoticeLog( __FUNCTION__ . "(): this->subject = " . $this->subject, $LogParamsArr);
        // Find the email body, and any attachments
        // $body_part->ctype_primary and $body_part->ctype_secondary make up the mime type eg. text/plain or text/html
        if(isset($this->decoded->parts) && is_array($this->decoded->parts)){
            foreach($this->decoded->parts as $idx => $body_part){
                $this->decodePart($body_part);
            }
        }

        if(isset($this->decoded->disposition) && $this->decoded->disposition == 'inline'){
            $mimeType = "{$this->decoded->ctype_primary}/{$this->decoded->ctype_secondary}";

            if(isset($this->decoded->d_parameters) &&  array_key_exists('filename',$this->decoded->d_parameters)){
                $filename = $this->decoded->d_parameters['filename'];
            }else{
                $filename = 'file';
            }

            $this->saveFile($filename, $this->decoded->body ,$mimeType);
            $this->body = "Body was a binary";
        }
MainNoticeLog(  "----> body: (".strlen($this->body).')', $LogParamsArr);
MainNoticeLog(  "----> decoded->body: (".strlen($this->decoded->body).')', $LogParamsArr);
MainNoticeLog(  "----> ContentType: (".$this->ContentType.')', $LogParamsArr);
MainNoticeLog(  "----> ContentTransferEncoding: (".$this->ContentTransferEncoding.')', $LogParamsArr);


        // We might also have uuencoded files. Check for those.
        if(!isset($this->body)) {
            if(isset($this->decoded->body)) {
                $this->body = $this->decoded->body;
            } else {
                $this->body = "No plain text body found";
            }
        }

        if(preg_match("/begin ([0-7]{3}) (.+)\r?\n(.+)\r?\nend/Us", $this->body) > 0) {
            foreach($decoder->uudecode($this->body) as $file) {
                // file = Array('filename' => $filename, 'fileperm' => $fileperm, 'filedata' => $filedata)
                $this->saveFile($file['filename'], $file['filedata']);
            }
            // Strip out all the uuencoded attachments from the body
            while(preg_match("/begin ([0-7]{3}) (.+)\r?\n(.+)\r?\nend/Us", $this->body) > 0) {
                $this->body = preg_replace("/begin ([0-7]{3}) (.+)\r?\n(.+)\r?\nend/Us", "\n",$this->body);
            }
        }
        /////////////////////////////////////////////
        MainNoticeLog(  "New email from: {$this->from_email}, subj: {$this->subject}, ".
                        "FilesCount: {$this->FilesCount}, bodylen: ".strlen($this->body).", decbodylen: ".strlen($this->decoded->body)." ".
                        "files: ".print_r($this->saved_files, true), $LogParamsArr);

        // Put the results in the database if needed
        if($this->save_msg_to_db && !is_null($this->pdo)) {
            $this->saveToDb();
        }

        // Send response e-mail if needed
        if($this->send_email && $this->from_email != "") {
            $this->sendEmail();
        }

        /*/ Print messages
        if($this->debug){
            $this->debugMsg();
        }*/

        return $this->saved_files;
    }

    /**
     * @brief Decode a single body part of an email message
     *
     * @note Recursive if nested body parts are found
     *
     * @note This is the meat of the script.
     *
     * @param $body_part (required) The body part of the email message, as parsed by Mail_mimeDecode
     */
    private function decodePart($body_part) {
        $LogParamsArr['OnlyMsg'] = true; // не добавлять лишнего описания в лог
        MainNoticeLog(  __FUNCTION__ . "()", $LogParamsArr);
        if(isset($body_part->ctype_parameters) && array_key_exists('name', $body_part->ctype_parameters)){ // everyone else I've tried
            $filename = $body_part->ctype_parameters['name'];
        } else if($body_part->ctype_parameters && array_key_exists('filename', $body_part->ctype_parameters)) { // hotmail
            $filename = $body_part->ctype_parameters['filename'];
        } else{
            $filename = "file";
        }

        $mimeType = "{$body_part->ctype_primary}/{$body_part->ctype_secondary}";

        if($this->debug) {
            //MainNoticeLog(__FUNCTION__."(): Found body part type $mimeType", $LogParamsArr);
        }

        if($body_part->ctype_primary == 'multipart') {
            if(is_array($body_part->parts)){
                foreach($body_part->parts as $ix => $sub_part) {
                    $this->decodePart($sub_part);
                }
            }
        } else if($mimeType == 'text/plain') {
            if(!isset($body_part->disposition)) {
                $this->body .= $body_part->body . "\n"; // Gather all plain/text which doesn't have an inline or attachment disposition
            }
        } else if($this->CheckMimeTypes) {
            // check of file extentions enabled
            if(in_array($mimeType,$this->allowed_mime_types)) {
                $this->saveFile($filename, $body_part->body, $mimeType);
            }
        } else if(!$this->CheckMimeTypes) {
            // check of file extentions disabled, save all
            $this->saveFile($filename, $body_part->body, $mimeType);
        }
        /*} else if(in_array($mimeType,$this->allowed_mime_types)){
            $this->saveFile($filename, $body_part->body, $mimeType);
        }*/
    }

    /**
     * @brief Save off a single file
     *
     * @param $filename (required) The filename to use for this file
     * @param $contents (required) The contents of the file we will save
     * @param $mimeType (required) The mime-type of the file
     */
    private function saveFile($filename, $contents, $mimeType = 'unknown') {
        global $CONF;
        $LogParamsArr['OnlyMsg'] = true; // не добавлять лишнего описания в лог
        //MainNoticeLog(   __FUNCTION__ . "({$this->save_directory} $filename, ".strlen($contents).")", $LogParamsArr);
        //////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $filename = preg_replace('/[^a-zA-Z0-9_-]/','_',$filename);

/////////////////////////////
        $unlocked_and_unique = FALSE;
        while(!$unlocked_and_unique) {
            // Find unique
            $name = date('dMY-His') . "_" . $filename;
            while(file_exists($this->save_directory . $name)) {
                $name = date('dMY_H-i-s') . "_" . $filename;
            }
            $EncodedFilePath = $this->save_directory.$name;
            MainNoticeLog(__FUNCTION__."(): saving file: $EncodedFilePath", $LogParamsArr); // logging
            $this->FilesCount = $this->FilesCount + 1; //  /////////////////////////////
            // Attempt to lock
            $outfile = fopen($EncodedFilePath, 'w');
            if(flock($outfile, LOCK_EX)) {
                $unlocked_and_unique = TRUE;
            } else {
                flock($outfile, LOCK_UN);
                fclose($outfile);
            }
        }

        fwrite($outfile, $contents );
        fclose($outfile);
        if( !chmod($EncodedFilePath, 0644) ) {
            MainNoticeLog(__FUNCTION__."(): Cant chmod $EncodedFilePath", $LogParamsArr);
        }

        // This is for readability for the return e-mail and in the DB
        $this->saved_files[$name] = Array(
            'size' => $this->formatBytes(filesize($this->save_directory.$name)),
            'mime' => $mimeType
        );
    $fl = file_get_contents("$EncodedFilePath");
        $oktext = base64_decode($fl, true);
        if($oktext) {
            file_put_contents( $CONF['CrmCopyMailDir'] . $name, $oktext );
        }
MainNoticeLog(  " ---> $EncodedFilePath - {$CONF['CrmCopyMailDir']}$name", $LogParamsArr);
//        DecodeUUencodedFile($EncodedFilePath, "/tmp/crm/f/", $name);

    }

    /**
     * @brief Format Bytes into human-friendly sizes
     *
     * @return A string with the number of bytes in the largest applicable unit (eg. KB, MB, GB, TB)
     */
    private function formatBytes($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * @brief Save the plain text, subject and sender of an email to the database
     */
    private function saveToDb() {
        $LogParamsArr['OnlyMsg'] = true; // не добавлять лишнего описания в лог
        MainNoticeLog( __FUNCTION__ . "(): saving to DB", $LogParamsArr);
        $this->pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING ); // change the PDO error reporting type
        $this->pdo->setAttribute( PDO::ATTR_EMULATE_PREPARES, false );
        $sth = $this->pdo->prepare("INSERT INTO DownloadedEmails (ContentType, ContentTransferEncoding, MailFrom, Subject, OriginalBody, DecodedBody, ServiceName) VALUES (?,?,?,?,?,?,?)");
        // Replace non UTF-8 characters with their UTF-8 equivalent, or drop them
//            mb_convert_encoding($this->from_email,'UTF-8','UTF-8'),
//            mb_convert_encoding($this->subject,'UTF-8','UTF-8'),
//            mb_convert_encoding($this->body,'UTF-8','UTF-8')
            //mb_convert_encoding($this->from_email,'UTF-8'),
            //mb_convert_encoding($this->subject,'UTF-8'),
            //mb_convert_encoding($this->body,'UTF-8')
        //iconv("KOI8-R","UTF-8", $this->subject)
        //$this->subject,
        // iconv_mime_decode($this->subject, ICONV_MIME_DECODE_CONTINUE_ON_ERROR, 'UTF-8'),

        //$text = $this->body. "\n\n\n-=-=-=-=-=-=-".
//                $this->decoded->body. "\n\n\n-=-=-=-=-=-=-".

        if($this->ContentTransferEncoding == '8bit') {
            // письма от Winner
            // не надо делать base64_decode
            $DecodedBody = $this->body;
            if(preg_match('/charset=koi8-r/i', $this->ContentType, $matches)) {
                $DecodedBody = iconv("KOI8-R","UTF-8", $DecodedBody );
            }
        } else if($this->ContentTransferEncoding == 'base64') {
            $DecodedBody = base64_decode($this->body);
            if(preg_match('/charset=koi8-r/i', $this->ContentType, $matches)) {
                $DecodedBody = iconv("KOI8-R","UTF-8", $DecodedBody );
            }
        } else if( preg_match('/charset=UTF-8/i', $this->ContentType) ) {
            // письма от Avito
            $DecodedBody = quoted_printable_decode($this->body);

        } else {
            $DecodedBody = $this->body;
        }


        /*$text2= base64_decode($this->body). "\n\n\n-=-=-=-=-=-=-".
                base64_decode($this->decoded->body). "\n\n\n-=-=-=-=-=-=-".
            iconv("KOI8-R","UTF-8", base64_decode($this->body) ). "\n\n\n-=-=-=-=-=-=-".
            iconv("Windows-1251","UTF-8", base64_decode($this->body) ). "\n\n\n-=-=-=-=-=-=-";*/
// OriginalBody = base64_decode($this->body),
//file_put_contents('/tmp/o', base64_decode($this->body)."\n\n".$this->body );
        //MainNoticeLog( __FUNCTION__ . "()!!!: ".$text, $LogParamsArr);
        //MainNoticeLog( __FUNCTION__ . "(): INSERT INTO DownloadedEmails failed!--: ". print_r($sth->errorInfo(), true));
        $ExecResult = $sth->execute(Array(
            $this->ContentType,
            $this->ContentTransferEncoding,
            $this->from_email,
            iconv_mime_decode($this->subject, ICONV_MIME_DECODE_CONTINUE_ON_ERROR, 'UTF-8'),
            $this->OriginalBody,
            $DecodedBody,
            $this->ServiceName
        ));

        //MainNoticeLog( __FUNCTION__ . "(): INSERT INTO DownloadedEmails failed!: ". print_r($ExecResult->errorInfo(), true));
        if( !$ExecResult ) {
            MainNoticeLog( __FUNCTION__ . "(): INSERT INTO DownloadedEmails failed!--: ". print_r($sth->errorInfo(), true));
            SystemExit();
        }
        $email_id = $this->pdo->lastInsertId();

        foreach($this->saved_files as $f => $data) {
            $insertFile = $this->pdo->prepare("INSERT INTO DownloadedEmailFiles (email_id,filename,mailsize,mime) VALUES (:email_id,:filename,:size,:mime)");
            $insertFile->bindParam(':email_id',$email_id);
            $insertFile->bindParam(':filename',mb_convert_encoding($f,'UTF-8','UTF-8'));
            $insertFile->bindParam(':size',$data['size']);
            $insertFile->bindParam(':mime',$data['mime']);
            if(!$insertFile->execute()){
                if($this->debug){
                    print_r($insertFile->errorInfo());
                }
                //$this->LogAndExit( __FUNCTION__ . "(): Insert file info failed!");
                MainNoticeLog( __FUNCTION__ . "(): Insert file info failed!");
                SystemExit();
            }
        }
    }

    /**
     * @brief Send the sender a response email with a summary of what was saved
     */
    private function sendEmail() {
        $newmsg = "Thanks! I just uploaded the following ";
        $newmsg .= "files to your storage:\n\n";
        $newmsg .= "Filename -- Size\n";
        foreach($this->saved_files as $f => $s) {
            $newmsg .= "$f -- ({$s['size']}) of type {$s['mime']}\n";
        }
        $newmsg .= "\nI hope everything looks right. If not,";
        $newmsg .=  "please send me an e-mail!\n";

        mail($this->from_email, $this->subject, $newmsg);
    }

    /**
     * @brief Print a summary of the most recent email read
     */
    /*private function debugMsg() {
        $LogParamsArr['OnlyMsg'] = true; // не добавлять лишнего описания в лог
        $msg = "From : $this->from_email, Subject : $this->subject, Body : $this->body, Saved Files : " . print_r($this->saved_files, true);
        MainNoticeLog( __FUNCTION__ . "(): " . $msg, $LogParamsArr);
    }*/
    /*private function MRLog($msg) {
        //file_put_contents($this->SysLogFile, "$msg\n", FILE_APPEND); // SYS LOGGING
        MainNoticeLog($msg);
    }
    private function LogAndExit($msg) {
        $this->MRLog($msg);
        die($msg);
    }*/


}
