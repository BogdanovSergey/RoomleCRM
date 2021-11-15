<?php

    function GetMailListArr($Params) {
        $SQL = array();
       /*if(isset($Params['OnlyActive'])) {
            if($Params['OnlyActive'] == 1) {
                $SQL['active'] = ' AND Active=1';
            } else {
                $SQL['active'] = ' AND Active=0';
            }
        } // без параметра OnlyActive берем всё
        if(isset($Params['OrderByField'])) {
            $SQL['OrderBy'] = " ORDER BY {$Params['OrderByField']} {$Params['OrderByTo']}";
        } else {
            $SQL['OrderBy'] = " ORDER BY LastName";
        }*/
        $SQL['OrderBy'] = " ORDER BY {$Params['OrderByField']} {$Params['OrderByTo']}";
        $out = array();
        $sql = "SELECT
                    *
                FROM
                    DownloadedEmails
                {$SQL['OrderBy']}";
        $res = mysql_query($sql);
        $GLOBALS['FirePHP']->info($sql);
        while($str = mysql_fetch_object($res)) {
            array_push($out, $str);
        }
        return $out;
    }


    function GetFilesArrByEmailId($EmailId, $Params=null) {
        $SQL = array();
        //$SQL['OrderBy'] = " ORDER BY {$Params['OrderByField']} {$Params['OrderByTo']}";
        $out = array();
        $sql = "SELECT
                    *
                FROM
                    DownloadedEmailFiles
                WHERE
                    email_id = $EmailId
                ORDER BY id";
                //{$SQL['OrderBy']}";
        $res = mysql_query($sql);
        $GLOBALS['FirePHP']->info($sql);
        while($str = mysql_fetch_object($res)) {
            array_push($out, $str);
        }
        return $out;
    }

    function GetEmailObjById($EmailId) {
        $sql = "SELECT
                    *
                FROM
                    DownloadedEmails
                WHERE
                    id = $EmailId";
        $res = mysql_query($sql);
        $GLOBALS['FirePHP']->info($sql);
        return mysql_fetch_object($res);
    }

    function MarkEmailAsRead($EmailId) {
        $sql = "UPDATE
                    DownloadedEmails
                SET
                    Opened = 1
                WHERE
                    id = $EmailId";
        $res = mysql_query($sql);
        $GLOBALS['FirePHP']->info($sql);
    }

    function Email_SimpleLetter($Params = array()) {
        global $CONF;
        $out = '';

        $mail = new PHPMailer;
        $mail->CharSet = 'utf-8';
        //$mail->SMTPDebug = 3;                               // Enable verbose debug output
        $mail->isSMTP();                                      // Set mailer to use SMTP
        //$mail->Host = 'smtp1.example.com;smtp2.example.com';  // Specify main and backup SMTP servers
        $mail->Host         = $CONF['EmailAccount']['Host'];
        $mail->SMTPAuth     = true;                             // Enable SMTP authentication
        $mail->Username     = $CONF['EmailAccount']['Username'];// SMTP username
        $mail->Password     = $CONF['EmailAccount']['Password'];// SMTP password
        $mail->SMTPSecure   = $CONF['EmailAccount']['SMTPSecure'];// Enable TLS encryption, `ssl` also accepted
        $mail->Port         = $CONF['EmailAccount']['Port'];     // TCP port to connect to [587]

        $mail->setFrom($CONF['EmailAccount']['MailFrom'], $CONF['EmailAccount']['FromName']);
        $mail->addAddress($Params['LetterData']['Email'], $Params['LetterData']['FirstName']);     // Add a recipient
        /*$mail->addAddress('ellen@example.com');               // Name is optional
        $mail->addReplyTo('info@example.com', 'Information');
        $mail->addCC('cc@example.com');
        $mail->addBCC('bcc@example.com');

        $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
        $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
        */$mail->isHTML(true);                                  // Set email format to HTML

        $mail->Subject = 'Здравствуйте, '.$Params['LetterData']['FirstName'];

        if(@$Params['LetterType'] == 'InvitationToNewUser') {
            $mail->Body    = $Params['LetterData']['FirstName'].'!<br>'.
                             'Ваша анкета была добавлена в CRM Агентства Недвижимости: '.$CONF['SysParams']['CompanyName'].'<br>'.
                             'Для входа в систему зайдите на адрес: <a href="'.$CONF['MainSiteUrl'].'">'.$CONF['MainSiteUrl'].'</a><br><br>'.
                             'Ваш логин: '.$Params['LetterData']['Login'].'<br>'.
                             'Ваш пароль: '.$Params['LetterData']['Password1'].'<br><br>'.
                             'Для входа в систему зайдите на адрес: <a href="'.$CONF['MainSiteUrl'].'">'.$CONF['MainSiteUrl'].'</a>'.
            $mail->AltBody = $mail->Body;//'This is the body in plain text for non-HTML mail clients';
            if(!$mail->send()) {
                $out = "Ошибка при отправке почты (<b>{$Params['LetterData']['Email']}</b>): {$mail->ErrorInfo}";
                CrmCopyNoticeLog($out);
            } else {
                $out = "Сотруднику отправлено оповещение на почту: <b>{$Params['LetterData']['Email']}</b>";
            }

        } elseif(@$Params['LetterType'] == 'UserUpdate') {
            if(!$Params['LetterData']['Password1']) { $Params['LetterData']['Password1']='(скрыт)'; }
            $Prms['OnlyNames'] = true;
            $Prms['InString']  = true;
            $UserGroupsNames   = GetGroupsNamesById($Params['LetterData']['Group0Id'], $Prms);
            $UserPositionsNames= GetPositionsNamesById($Params['LetterData']['Pos0Id'], $Prms);

            $mail->Body    = $Params['LetterData']['FirstName'].'!<br>'.
                'Данные вашей анкеты были обновлены в CRM агентства недвижимости: '.$CONF['SysParams']['CompanyName'].'<br><br>'.

                'Ваш логин: '.$Params['LetterData']['Login'].'<br>'.
                'Ваш пароль: '.$Params['LetterData']['Password1'].'<br><br>'.
                'Ваш основной мобильный: '.$Params['LetterData']['MobilePhone'].'<br>'.
                'Ваш альтернативный мобильный №1: '.$Params['LetterData']['MobilePhone1'].'<br>'.
                'Ваш альтернативный мобильный №2: '.$Params['LetterData']['MobilePhone2'].'<br>'.
                'Ваш email: '.$Params['LetterData']['Email'].'<br>'.
                'Ваша должность: '.$UserPositionsNames.'<br>'.
                'Ваш отдел: '.$UserGroupsNames.'<br><br>'.

                'Для входа в систему зайдите на адрес: <a href="'.$CONF['MainSiteUrl'].'">'.$CONF['MainSiteUrl'].'</a><br>';
            ;
            $mail->AltBody = $mail->Body; //'This is the body in plain text for non-HTML mail clients';
            if(!$mail->send()) {
                $out = "Ошибка при отправке почты (<b>{$Params['LetterData']['Email']}</b>): {$mail->ErrorInfo}";
                CrmCopyNoticeLog($out);
            } else {
                $out = "Сотруднику отправлено оповещение на почту: <b>{$Params['LetterData']['Email']}</b>";
            }

        } else {
            $out = 'Ошибка: LetterType unknown';
        }
        return $out;
    }


    function CheckUnparsedEmails() {
        $out = 0;
        $sql = "SELECT COUNT(id) AS c FROM `DownloadedEmails` WHERE ParsedAt IS NULL";
        $res = mysql_query($sql);
        $str = mysql_fetch_object($res);
        return $str->c;

    }
    function EmailParsingStart($EmailId) {
        $sql = "UPDATE
                    DownloadedEmails
                SET
                    ParsingNow = 1
                WHERE
                    id=$EmailId";
        $res = mysql_query($sql);
    }

    function EmailParsingFinished($EmailId) {
        $sql = "UPDATE
                    DownloadedEmails
                SET
                    ParsingNow = NULL,
                    ParsedAt = NOW()
                WHERE
                    id=$EmailId";
        $res = mysql_query($sql);
    }

    function PutEmailsByObjectIds($ObjectErrors) {
        global $CONF;
        // $ObjectErrors[ $ObjectId ] .= $Params['ErrorText']
        // берем email'ы через id объектов и ставим в очередь на отправку письма с ошибками
        foreach($ObjectErrors as $ObjectId => $Body) {
            $EmailArr = GetEmailArrByObjectId($ObjectId);
            foreach($EmailArr as $EmailTo) {
                $Params = array();
                $Params['MailFrom'] = $CONF['EmailAccount']['MailFrom'];
                $Params['MailTo']   = $EmailTo;
                $Params['Body']     = $Body;
                PutEmailQueue($Params);
            }
        }

    }


    function GetEmailArrByObjectId($ObjectId) {
        // пока берем только один email адрес - пользовательский
        $out = array();
        $ParamsArr['OnlyMsg'] = true;
        if($ObjectId > 0) {
            $sql = "SELECT
                        u.id AS UserId,
                        u.Email
                    FROM
                        Objects AS o,
                        Users   AS u
                    WHERE
                        o.OwnerUserId = u.id AND
                        o.id = $ObjectId";
            $res = mysql_query($sql);
            $str = mysql_fetch_object($res);

            if(isset($str->Email) && strlen($str->Email) > 3) {
                array_push($out, $str->Email);
            } else {
                $msg = __FUNCTION__."(): Cant send email (".@$str->Email.") to UserId: ".@$str->UserId.", ObjectId: $ObjectId";
                CrmCopyNoticeLog($msg, $ParamsArr);
            }
        } else {
            echo "ObjectId ($ObjectId) !> 0\n";
        }
        return $out;
    }

    function PutEmailQueue($Params) {
        $sql = "INSERT INTO
                    EmailTasks (AddedDate, MailFrom, MailTo, Body)
                VALUES
                    (NOW(), '{$Params['MailFrom']}', '{$Params['MailTo']}', '{$Params['Body']}')
                    ";
        $res = mysql_query($sql);
    }
