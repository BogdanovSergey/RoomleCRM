<?php

    function PrepareBackgroundImages() {
        global $CONF;
        $CssBgClass = '';
        $CssBgOption = '';
        $CssCurrentBgUrl = null;
        for($i=0; $i<count($CONF['BackgroundImageParam']); $i++ ) {
            if(@$_COOKIE['background'] == $i) {
                $sel = ' selected';
                if(@$_COOKIE['background'] == 0) { // это рандом
                    $r = rand(0, count($CONF['BackgroundImageParam'])-1 );
                    $CssCurrentBgUrl = "images/Background/" . $CONF['BackgroundImageParam'][ $r ][0];
                } else {                          // конкретная картинка
                    $CssCurrentBgUrl = "images/Background/" . $CONF['BackgroundImageParam'][$i][0];
                }
            } else { // куки ваще нету
                $sel = ' ';
                //$r = rand(0, count($CONF['BackgroundImageParam'])-1 );
                //$CssCurrentBgUrl = "images/Background/" . $CONF['BackgroundImageParam'][ $r ][0];
            }
            $CssBgClass .= ".ui-icon.BG$i { background: url(\"images/Background/thumb/{$CONF['BackgroundImageParam'][$i][0]}\") 0 0 no-repeat; }\n";
            $CssBgOption .= "<option value=\"$i\" data-class=\"BG$i\" $sel>{$CONF['BackgroundImageParam'][$i][1]}</option>\n";

        }
        if(!$CssCurrentBgUrl) { $CssCurrentBgUrl = "images/Background/".$CONF['BackgroundImageParam'][0][0]; }
        return array($CssBgClass, $CssBgOption, $CssCurrentBgUrl);
    }


