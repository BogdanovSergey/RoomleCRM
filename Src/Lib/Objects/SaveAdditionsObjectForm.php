<?php


if($_REQUEST['LoadedObjectId'] > 0) {
    // обновление существующего объекта

    $_REQUEST['SiteTitle']       = mysql_real_escape_string( $_REQUEST['SiteTitle'] );
    $_REQUEST['SiteKeywords']    = mysql_real_escape_string( $_REQUEST['SiteKeywords'] );
    $_REQUEST['SiteDescription'] = mysql_real_escape_string( $_REQUEST['SiteDescription'] );
    $_REQUEST['SiteVideo']       = mysql_real_escape_string( $_REQUEST['SiteVideo'] );
    $_REQUEST['RealtyType']      = mysql_real_escape_string( $_REQUEST['RealtyType'] );

    $Params['OnlyNames'] = true;
    if( in_array($_REQUEST['RealtyType'], GetRealtyTypesArr($Params)) ) {
        $TypePatch = ", RealtyType='{$_REQUEST['RealtyType']}'";
    } else {
        $TypePatch = '';
    }
    $sql = "
            UPDATE Objects SET
                LastUpdateDate  = NOW(),
                SiteTitle       = '{$_REQUEST['SiteTitle']}',
                SiteKeywords    = '{$_REQUEST['SiteKeywords']}',
                SiteDescription = '{$_REQUEST['SiteDescription']}',
                SiteVideo       = '{$_REQUEST['SiteVideo']}'
                {$TypePatch}
            WHERE
                id = {$_REQUEST['LoadedObjectId']}
                ";
    $GLOBALS['FirePHP']->info($sql);
    $res = mysql_query($sql);

    if($res) {
        echo '{"success":true,"message":"Дополнительная информация объекта № '.$_REQUEST['LoadedObjectId'].' успешно обновлена"}';
    } else {
        $msg = mysql_error();
        echo '{"success":false,"message":"'.$msg.'"}';
    }

} else {
    CrmCopyNoticeLog(__FILE__.'(): LoadedObjectId < 0');
}
