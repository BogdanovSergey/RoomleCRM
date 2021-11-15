<?php

    require(dirname(__FILE__) . '/../../Conf/Config.php');
    require(dirname(__FILE__) . '/../../Lib/Xml/Xml.php');
    require(dirname(__FILE__) . '/../../Lib/Xml/Rbc.php');

    DBConnect();

    $XmlTag = new DOMDocument('1.0', 'UTF-8');
    // создаем корневой элемент
    $Document = $XmlTag->createElement('document');
    $flats    = $XmlTag->createElement('flats');
    $MainSqlQuery  = CreateSQLQueryForXMLLoad('RbcFlats');

    $SysParams['ElementTag']    = 'offer';
    $SysParams['RealtyType']    = 'city';
    $flats = BuildXmlForRbc($XmlTag, $flats, $MainSqlQuery, $SysParams);
    $Document->appendChild($flats);
    $XmlTag->appendChild($Document);

    $out = preg_replace('#</offer>#', "</offer>\n\n", $XmlTag->saveXML() );   // пробелы для читаемости
    $out = preg_replace('/></', ">\n<", $out );
    echo $out;


