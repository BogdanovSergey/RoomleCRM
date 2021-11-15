<?php

    //$GLOBALS['FIREPHP']->error('asd');
    function CheckUnreadEmailsByServiceName($ServiceName) {
        $sql = "SELECT
                    COUNT(id) AS c
                FROM
                      DownloadedEmails
                WHERE
                    Opened      = 0 AND
                    ServiceName ='$ServiceName'
                ORDER BY id";
        $res = $GLOBALS['DBLink']->select($sql);
        if($res[0]['c'] > 0) {
            $out = true;
        } else {
            $out = false;
        }
        return $out;
    }

    function GetEmailsArrByServiceName($ServiceName) {
        $sql = "SELECT
                    *
                FROM
                      DownloadedEmails
                WHERE
                    Opened      = 0 AND
                    ServiceName ='$ServiceName'
                ORDER BY id";
        $res = $GLOBALS['DBLink']->select($sql);
        return $res;
    }

    function GetEmailAttachmentsArrByEmailId($EmailId) {
        $sql = "SELECT
                    *
                FROM
                      DownloadedEmailFiles
                WHERE
                    email_id      = $EmailId
                ORDER BY id";
        $res = $GLOBALS['DBLink']->select($sql);
        return $res;

    }


    function MarkObjectForEListing($ObjectId, $ObjectType) {

    }


