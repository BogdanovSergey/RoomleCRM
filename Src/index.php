<?php
//echo phpinfo();exit;
    require('Conf/Config.php');
connectToDB('CrmAdminDb'); // create new db link: $GLOBALS['DBConn']["CrmAdminDb"]
    DBConnect();

    InitAuthentication();


    if(isset($CURRENT_USER->id)) {
        $TEMPLATE_VARS['MainSiteUrl'] = $CONF['SysParams']['MainSiteUrl'];
        $TEMPLATE_VARS['CompanyName'] = $CONF['SysParams']['CompanyName'];
        echo ParseFile(file_get_contents($CONF['SystemPath'] . $CONF['CrmSubDir'] . '/Lib/Html/Templates/AgencyCrmIndex.html'), 'UseText', $TEMPLATE_VARS);


    } else {
        // User is not authorized
        $TEMPLATE_VARS = $CONF['SysParams']; // все переменные из тбл
        $TEMPLATE_VARS['MainSiteUrl'] = $CONF['SysParams']['MainSiteUrl'];
        $TEMPLATE_VARS['CompanyName'] = $CONF['SysParams']['CompanyName'];
        list(
            $TEMPLATE_VARS['CssBgClass'],
            $TEMPLATE_VARS['CssBgOption'],
            $TEMPLATE_VARS['CssCurrentBgUrl']) = PrepareBackgroundImages();
        echo ParseFile(file_get_contents($CONF['SystemPath'] . $CONF['CrmSubDir'] . '/Lib/Html/Templates/AgencySiteIndex.html'), 'UseText', $TEMPLATE_VARS);
    }




