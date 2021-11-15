<?php

    require(dirname(__FILE__) . '/../../Conf/Config.php');
    require(dirname(__FILE__) . '/../../Lib/Xml/Xml.php');
    require(dirname(__FILE__) . '/../../Lib/Xml/Yandex.php');

    DBConnect();

    $XmlTag = new DOMDocument('1.0', 'UTF-8');
    // создаем корневой элемент
    $Document = $XmlTag->createElement('realty-feed');
    $Document->setAttribute('xmlns', 'http://webmaster.yandex.ru/schemas/feed/realty/2010-06');

    $Document->appendChild( $XmlTag->createElement('generation-date', date('c')) );

    $MainSqlQuery  = CreateSQLQueryForXMLLoad('YandexFlats');

    $SysParams['ElementTag']    = 'offer';
    $SysParams['RealtyType']    = 'city';
    $Document = BuildXmlForYandex($XmlTag, $Document, $MainSqlQuery, $SysParams);


    $XmlTag->appendChild( $Document );


    $out = preg_replace('#</offer>#', "</offer>\n\n", $XmlTag->saveXML() );   // пробелы для читаемости
    $out = preg_replace('/></', ">\n<", $out );
    echo $out;


