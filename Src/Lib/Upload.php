<?php

    function FileUploader($FILESARR, $ObjectId, $Params = array()) {
        // функция заливает один файл!
        global $CONF;
        // TODO навести здесь порядок! удалить мусор, разобрать по функциям, вывести переменные в главн конфиг
        (isset($Params['prefix']))   ? $upload_name    = $Params['prefix']   : $upload_name     = 'file';               // при локальной закачке
        (isset($Params['filesize'])) ? $CONTENT_LENGTH = $Params['filesize'] : $CONTENT_LENGTH  = $_SERVER['CONTENT_LENGTH']; // при локальной закачке
        //$upload_name                = 'file';//
        //$CONF['WebRoot'] = '/1/images/'; // внешняя папка где хранятся фотки
//print_r($Params);
//        print_r($FILESARR);
//        exit;
        if(@$Params['local']) {
            $UPLOAD['FileExtension']    = $FILESARR[$upload_name]['extension'];
            $UPLOAD['NewFileName']      = $FILESARR[$upload_name]['name'];
        } else {
            $path_info                  = pathinfo($FILESARR[$upload_name]['name']);
            $UPLOAD['FileExtension']    = $path_info["extension"];
            $UPLOAD['NewFileName']      = GetMaxImagesId() . rand(1,1000);                // генерируем новое имя на каждый новый файл
        }



        //$CONF['ObjectImagesPath']['big']   = '/images/big/'; // внешняя папка где хранятся фотки большие
        //$CONF['ObjectImagesPath']['thumb'] = '/images/thumb/'; // внешняя папка где хранятся фотки - превьюшки
        $valid_chars_regex          = '.A-Z0-9_ ()+={}[]-';	// Characters allowed in the file name (in a Regular Expression format)
        //$extension_whitelist = array('csv', 'gif', 'png','tif');	// Allowed file extensions
        $MAX_FILENAME_LENGTH        = 260;
        $max_file_size_in_bytes     = 2147483647; // 2GB in bytes

        // Check post_max_size (http://us3.php.net/manual/en/features.file-upload.php#73762)
        $POST_MAX_SIZE          = ini_get('post_max_size');
        $unit                   = strtoupper(substr($POST_MAX_SIZE, -1));
        $multiplier             = ($unit == 'M' ? 1048576 : ($unit == 'K' ? 1024 : ($unit == 'G' ? 1073741824 : 1)));

        if ((int)$CONTENT_LENGTH > $multiplier*(int)$POST_MAX_SIZE && $POST_MAX_SIZE) {
            //header("HTTP/1.1 500 Internal Server Error"); // This will trigger an uploadError event in SWFUpload
            //echo "POST exceeded maximum allowed size.";
            UploadError('POST exceeded maximum allowed size.', $Params);
        }

    // Other variables
        //$file_name = '';
        //$file_extension = '';
        $uploadErrors = array(
            0=>'There is no error, the file uploaded with success',
            1=>'File exceeds the upload_max_filesize directive in php.ini',
            2=>'File exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
            3=>'File was only partially uploaded',
            4=>'No file was uploaded',
            6=>'Missing a temporary folder'
        );

    // Validate the upload
    if (!isset($FILESARR[$upload_name])) {
        UploadError('No upload found in \$FILESARR for ' . $upload_name, $Params);
    } else if (isset($FILESARR[$upload_name]["error"]) && $FILESARR[$upload_name]["error"] != 0) {
        UploadError($uploadErrors[$FILESARR[$upload_name]["error"]], $Params);
    } else if ((!isset($FILESARR[$upload_name]["tmp_name"]) || !@is_uploaded_file($FILESARR[$upload_name]["tmp_name"])) && !$Params['local']) {
        UploadError('Upload failed is_uploaded_file test.', $Params);
    } else if (!isset($FILESARR[$upload_name]['name'])) {
        UploadError('File has no name.', $Params);
    }

    // Validate the file size (Warning: the largest files supported by this code is 2GB)
    $file_size = @filesize($FILESARR[$upload_name]["tmp_name"]);
    if (!$file_size || $file_size > $max_file_size_in_bytes) {
        UploadError('File exceeds the maximum allowed size', $Params);
    }

    if ($file_size <= 0) {
        UploadError('File size outside allowed lower bound', $Params);
    }

    // Validate file name (for our purposes we'll just remove invalid characters)
    $file_name = preg_replace('/[^'.$valid_chars_regex.']|\.+$/i', "", basename($FILESARR[$upload_name]['name']));
    if (strlen($file_name) == 0 || strlen($file_name) > $MAX_FILENAME_LENGTH) {
        UploadError('Invalid file name', $Params);
    }


    /*
    // Validate file extension
        $path_info = pathinfo($FILESARR[$upload_name]['name']);
        $file_extension = $path_info["extension"];
        $is_valid_extension = false;
        foreach ($extension_whitelist as $extension) {
            if (strcasecmp($file_extension, $extension) == 0) {
                $is_valid_extension = true;
                break;
            }
        }
        if (!$is_valid_extension) {
            UploadError("Invalid file extension");
            exit(0);
        }

    // Validate file contents (extension and mime-type can't be trusted)

            Validating the file contents is OS and web server configuration dependant.  Also, it may not be reliable.
            See the comments on this page: http://us2.php.net/fileinfo

            Also see http://72.14.253.104/search?q=cache:3YGZfcnKDrYJ:www.scanit.be/uploads/php-file-upload.pdf+php+file+command&hl=en&ct=clnk&cd=8&gl=us&client=firefox-a
             which describes how a PHP script can be embedded within a GIF image file.

            Therefore, no sample code will be provided here.  Research the issue, decide how much security is
             needed, and implement a solution that meets the needs.



    // Process the file

            At this point we are ready to process the valid file. This sample code shows how to save the file. Other tasks
             could be done such as creating an entry in a database or generating a thumbnail.

            Depending on your server OS and needs you may need to set the Security Permissions on the file after it has
            been saved.
        */

        $CurrentFilePath = $FILESARR[$upload_name]["tmp_name"];

        // строим путь к новому файлу размером 480px - см. $CONF['ObjectImagesSize']['Width']
        $BigFilePath = $CONF['ObjectImagesPath']['big'] . "{$UPLOAD['NewFileName']}.{$UPLOAD['FileExtension']}";
        list($r1, $w, $h) = ResizeAndSaveImage($CurrentFilePath, $CONF['SystemPath'] . $BigFilePath,
            $CONF['ObjectImagesSize']['Width'],
            $CONF['ObjectImagesSize']['Height']);  // ресайзим, сохраняем по абсолютному пути

        // строим путь к новому файлу-превьюшке
        $ThumbFilePath = $CONF['ObjectImagesPath']['thumb'] . "{$UPLOAD['NewFileName']}.{$UPLOAD['FileExtension']}";
        list($r2, $wprev, $hprev) = ResizeAndSaveImage($CurrentFilePath, $CONF['SystemPath'] . $ThumbFilePath, 120); // ресайзим, сохраняем по абсолютному пути

        if( !$r1 || !$r2) {
            // some problems
            UploadError("File could not be resized or saved in func ResizeAndSaveImage()", $Params);
        } else {
            // work successfull

            $Obj                = (object)[]; // creating empty object
            $Obj->FilePath      = $BigFilePath;
            $Obj->PreviewPath   = $ThumbFilePath;
            $Obj->ObjectId      = $ObjectId;
            SaveUploadedImageToDb($Obj, true, $w, $h);
        }
        return UploadSuccess($Params);
    }




    function UploadSuccess($Params) {
        if(@$Params['local']) {
            return true;
        } else {
            return '{"success":true}';
        }
    }



    /* Handles the error output. This error message will be sent to the uploadSuccess event handler.  The event handler
    will have to check for any error messages and react as needed. */
    function UploadError($message, $Params) {
        global $CONF;
        if(@$Params['local']) {
            // ошибка при локальном импорте объекта
            echo $message;
            SimpleLog($CONF['Log']['CrmImportLog'], $message);
        } else {
            die('{"success":false,"message":'.json_encode($message).'}');
        }
    }
