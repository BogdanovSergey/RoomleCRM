<?php

    function CheckCityInRegionInAvitoLib($RegionName, $CityName) {
        global $CONF;
        $out = false;
        $x = simplexml_load_file($CONF['SystemPath'].$CONF['CrmSubDir'].'/Mods/Avito/Locations.xml') or die('Cant open file');
        foreach($x->Region as $RegionObj) {
            if( preg_match("/^{$RegionObj['Name']}$/iu", $RegionName) ) {
                foreach($RegionObj as $Item) {      // по городам
                    if( preg_match("/^{$Item['Name']}$/iu", $CityName) ) {
                        $out = true;
                        break;
                    }
                }
                break;
            }
        }
        ($out) ? $GLOBALS['FirePHP']->info(__FUNCTION__."() город '$CityName' найден в регионе: '$RegionName'") :
                 $GLOBALS['FirePHP']->info(__FUNCTION__."() город '$CityName' НЕ найден в регионе: '$RegionName'");
        return $out;
    }


    function GetAvitoCitiesArrByRegion($RegionName, $Params) {
        global $CONF;
        $count   = 0;
        $out     = array();
        $x = simplexml_load_file($CONF['SystemPath'].$CONF['CrmSubDir'].'/Mods/Avito/Locations.xml') or die('Cant open file');
        foreach($x->Region as $RegionObj) {
            if($RegionObj['Name'] == $RegionName) { // обнаружен выбранный регион //TODO есть несовпадение "АО" и "авт округ"
                foreach($RegionObj as $Item) {
                    if($Params['InJson']) {
                        $count++;
                        $element           = array();
                        $element['Name']   = (string) $Item['Name'];
                        //$element['Socr']   = $str->SOCRNAME;
                        array_push($out, $element);
                    } else {
                        array_push($out, (string) $Item['Name']);
                    }
                }
                break;
            }
        }
        $element           = array('');
        $element['found']  = true;
        return $out;
    }
