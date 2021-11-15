<?php

function Graphics_GetImageInfo($File) {
    $size = getimagesize ($File);
    $flag = array(
        1=>'image/gif',
        2=>'image/jpeg',
        3=>'image/png',
        4=>'SWF',
        5=>'image/psd',
        6=>'image/bmp',
        7=>'image/tiff',
        8=>'image/tiff',
        9=>'JPC',
        10=>'image/jp2',
        11=>'JPX'
    );
    $ImgInfoArr['width']    = $size[0];
    $ImgInfoArr['height']   = $size[1];
    $ImgInfoArr['type']     = $flag[$size[2]];
    $ImgInfoArr['filesize'] = filesize($File);
    $info = pathinfo($File);
    $ImgInfoArr['filename'] = $info['filename'];
    $ImgInfoArr['extension']= $info['extension'];
    return $ImgInfoArr;
}

/*
    $w_o и h_o - ширина и высота выходного изображения
*/
function ResizeAndSaveImage($ImageFrom, $ImageTo, $w_o = false, $h_o = false) {
    $GLOBALS['FirePHP']->info(__FUNCTION__."($ImageFrom, $ImageTo, $w_o, $h_o)");

    if (($w_o < 0) || ($h_o < 0)) {
        // TODO здесь и ниже вызывать ощую функцию протоколирующую ошибки!
        echo "Некорректные входные параметры";
        return false;
    }
    //echo "$ImageFrom, $ImageTo";
    //exif_imagetype?
    list($w_i, $h_i, $type) = getimagesize($ImageFrom); // Получаем размеры и тип изображения (число)
    $types = array("", "gif", "jpeg", "png");       // Массив с типами изображений
    $ext = $types[$type];                           // Зная "числовой" тип изображения, узнаём название типа
    $GLOBALS['FirePHP']->info(__FUNCTION__."(): File info: type: $ext, w: $w_i, h: $h_i");
    if ($ext) {
        $func = 'imagecreatefrom'.$ext;             // Получаем название функции, соответствующую типу, для создания изображения
        $img_i = $func($ImageFrom);                     // Создаём дескриптор для работы с исходным изображением
    } else {
        echo 'Некорректное изображение'; // Выводим ошибку, если формат изображения недопустимый
        return false;
    }
    /* Если указать только 1 параметр, то второй подстроится пропорционально */
    if (!$h_o) $h_o = $w_o / ($w_i / $h_i);
    if (!$w_o) $w_o = $h_o / ($h_i / $w_i);

    $img_o = imagecreatetruecolor($w_o, $h_o);      // Создаём дескриптор для выходного изображения
    $GLOBALS['FirePHP']->info(__FUNCTION__."(): imagecreatetruecolor($w_o, $h_o) ");

    $ResCR = imagecopyresampled($img_o, $img_i, 0, 0, 0, 0, $w_o, $h_o, $w_i, $h_i); // Переносим изображение из исходного в выходное, масштабируя его
    $func = 'image'.$ext;                           // Получаем функция для сохранения результата

    if(!$ResCR) {$GLOBALS['FirePHP']->error(__FUNCTION__."(): imagecopyresampled return false"); }

    $GLOBALS['FirePHP']->info(__FUNCTION__."(): $func($img_o, $ImageTo)");

    $FuncResult = $func($img_o, $ImageTo);
    if(!$FuncResult) {
        $msg = __FUNCTION__."(): ERROR: $func($img_o, $ImageTo)";
        $GLOBALS['FirePHP']->error($msg);
        CrmCopyErrorLog($msg);
    }

    // $func - это типа imagegif ( resource $image [, string $filename ] )
    // Returns TRUE on success or FALSE on failure.
    return array($FuncResult, $w_o, $h_o);   // Сохраняем изображение в тот же файл, что и исходное, возвращая результат этой операции
}


/* Вызываем функцию с целью уменьшить изображение до ширины в 100 пикселей, а высоту уменьшив пропорционально, чтобы не искажать изображение */
//resize("image.jpg", 120); // Вызываем функцию
//480