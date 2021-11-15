<?php

    require(dirname(__FILE__) . '/../../Conf/Config.php');
    require(dirname(__FILE__) . '/../../Lib/Xml/Xml.php');
    require(dirname(__FILE__) . '/../../Lib/Xml/Winner.php');

    DBConnect();
    //LoadSysParams(); // обновляет $CONF для индивидуальных параметров компании, использ в BuildXmlForWinner

    $XmlTag = new DOMDocument('1.0', 'UTF-8');
    // создаем корневой элемент
    $Flats = $XmlTag->createElement('flats');
    $Flats->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
    $Flats->setAttribute('xsi:noNamespaceSchemaLocation', 'flats.xsd');

    $MainSqlQuery  = CreateSQLQueryForXMLLoad('NavigatorFlats');

    // добавляем в корневой элемен  атрибут с датой
    //$generationdate = $XmlTag->createElement( 'generation-date', date('c') );
    //$Flats->appendChild( $generationdate );
    $SysParams['Navigator']     = true;
    $SysParams['ElementTag']    = 'flat';
    $SysParams['RealtyType']    = 'city';
    $Flats = BuildXmlForWinner($XmlTag, $Flats, $MainSqlQuery, $SysParams);

    $XmlTag->appendChild($Flats);

    $out = preg_replace('#</flat>#', "</flat>\n\n", $XmlTag->saveXML() );   // пробелы для читаемости
    $out = preg_replace('/></', ">\n<", $out );
    echo $out;


