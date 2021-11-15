<?php

    function ExtendUserProperties($Params) {
        // ф-я дополняет нужные поля

        // TODO сквозная???
        //$IncomeObj = Array();
        $IncomeObj = $Params['Data'];
        if($Params['CopyData']) {
            $out     = $Params['Data'];     // копируем предидущие значения
        } else {
            $out     = (object) array();    // возвращаем новый объект без прошлых значений, только новые
        }



        if(@$Params['InArray']) {
            return (array) $out;
        } else {
            return $out;
        }


    }