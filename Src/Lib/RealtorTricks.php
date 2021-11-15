<?php


    function ChangeDealTypeIfAlternativaFoundInText($DealType, $Description) {
        global $CONF;
        foreach($CONF['RealtorWords']['AlternativaRegexpArr'] as $Regexp) {
            if(preg_match($Regexp, $Description)) {
                $DealType             = 59; // TODO статический номер - плохо
                $ParamsArr            = array();
                $ParamsArr['OnlyMsg'] = true;
                CrmCopyNoticeLog(__FUNCTION__."(): DealType changed to $DealType");
                break;
            }
        }
        return $DealType;
    }

