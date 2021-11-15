<?php

    require(dirname(__FILE__) . '/../../Conf/Config.php');
    require(dirname(__FILE__) . '/../../Lib/Xml/Xml.php');
    require(dirname(__FILE__) . '/../../Lib/Xml/Cian.php');
    $GLOBALS['FirePHP']->setEnabled(false);
    DBConnect();

    $XmlTag = new DOMDocument('1.0', 'UTF-8');
    // создаем корневой элемент
    $Flats = $XmlTag->createElement('suburbian');

    $MainSqlQuery               = CreateSQLQueryForXMLLoad('CianCountry');
    $SysParams['RealtyType']    = 'country';
    $Flats = BuildXmlForCian($XmlTag, $Flats, $MainSqlQuery, $SysParams);

    $XmlTag->appendChild($Flats);

    $out = preg_replace('#</suburbian>#', "</suburbian>\n\n", $XmlTag->saveXML() );   // пробелы для читаемости
    $out = preg_replace('/></', ">\n<", $out );
    echo $out;


