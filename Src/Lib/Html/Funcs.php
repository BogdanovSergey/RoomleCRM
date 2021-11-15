<?php

    function ParseFile($Data, $DataType, $TEMPLATE_VARS) {
        if($DataType == 'OpenFile' ) {
            $FileData = file_get_contents($Data);
        } elseif($DataType == 'UseText') {
            $FileData = $Data;
        } else {
            CoreLog(__FUNCTION__.'() error in param');
        }
        preg_match_all('/\<\!--\%(.*)\%--\>/iuU', $FileData, $matches); // $matches is arr of <!--%FirstPageStat_SellMoscowRoomTotal%-->
        $i = 0;
        foreach($matches[1] as $var) {
            if(isset($TEMPLATE_VARS[$var])) {
                // rename template var
                $i++;
                $changeTo = $TEMPLATE_VARS[$var];
            } else {
                $changeTo = '';
            }
            $FileData = preg_replace('/\<\!--\%'.$var.'\%--\>/i', $changeTo, $FileData);
        }
        return $FileData;
    }