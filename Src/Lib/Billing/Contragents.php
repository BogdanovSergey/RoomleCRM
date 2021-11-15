<?php


    function GetContragentsList($Params = array()) {
        global $WORDS;
        $out = array();
        $sql = "SELECT
                  *
                FROM
                  BillContragents";
        //$res = mysql_query($sql);
        $res = SQLQuery($sql, $GLOBALS['DBConn']['CrmDb']);

        $Params['Actual']   = true;
        $Params['ContragentIdKey'] = true;
        $PortalTariffsArr = GetTarifPricesArr($Params); // áåğåì öåííèêè íà ïîğòàëû

        while($str = mysql_fetch_object($res)) {
            //array_push($out, $str->ContragentName);

            //$result = SQLQuery($sql, $GLOBALS['DBConn']['DBAntiposrednik']);
            //$Obj = mysql_fetch_object($result);

            $AddInfoObj = GetContragentInfo($str->id);

            //$element                        = array();
            $element                        = $AddInfoObj; // âíîñèì ÂÑÅ õàğ-êè èç òáë ContragentsInfo (Description, Contacts...)
            $element['PortalName']          = $str->ContragentName;
            $element['PortalId']            = 'PortalId_'.$str->ContragentName;
            $element['PortalImg']           = $str->Image;
            $element['PortalStatus']        = $WORDS['PortalStatus'][$str->Active];
            //$element['Description']         = $AddInfoObj->Description;//$str->PrivateAccountInfo;
            $element['Price']               = @$PortalTariffsArr[$str->id];//$AddInfoObj->Contacts;
            $element['LoadCount'] = '777';
            $element['XmlLink'] = 'XmlLinkXmlLinkXmlLink';


            array_push($out, $element);

        }


        return $out;
    }

    function GetContragentInfo($ContragentId) {
        $sql = "SELECT
                  *
                FROM
                  ContragentsInfo
                WHERE
                  id=$ContragentId";
        //$res = mysql_query($sql);
        $res = SQLQuery($sql, $GLOBALS['DBConn']['CrmAdminDb']);
        $str = mysql_fetch_array($res);
        return $str;
    }

    function GetContragentIdBySysName($SysName) {
        $sql = "SELECT
                  id
                FROM
                  BillContragents
                WHERE
                  ContrSysName='$SysName'";
        $res = mysql_query($sql);
        $str = mysql_fetch_object($res);
        return $str->id;
    }
