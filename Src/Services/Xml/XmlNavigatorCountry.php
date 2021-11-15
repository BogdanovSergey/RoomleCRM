<?php

    require(dirname(__FILE__) . '/../../Conf/Config.php');
    require(dirname(__FILE__) . '/../../Lib/Xml/Xml.php');
    require(dirname(__FILE__) . '/../../Lib/Xml/Winner.php');

    DBConnect();
    //LoadSysParams(); // обновляет $CONF для индивидуальных параметров компании, использ в BuildXmlForWinner

    $XmlTag = new DOMDocument('1.0', 'UTF-8');
    // создаем корневой элемент
    $CountryHouses = $XmlTag->createElement('country_houses');
    $CountryHouses->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
    $CountryHouses->setAttribute('xsi:noNamespaceSchemaLocation', 'flats.xsd');

    $MainSqlQuery  = CreateSQLQueryForXMLLoad('NavigatorCountry');

    // добавляем в корневой элемен  атрибут с датой
    //$generationdate = $XmlTag->createElement( 'generation-date', date('c') );
    //$CountryHouses->appendChild( $generationdate );
    $SysParams['Navigator']     = true;
    $SysParams['ElementTag']    = 'country_house';
    $SysParams['RealtyType']    = 'country';
    $CountryHouses = BuildXmlForWinner($XmlTag, $CountryHouses, $MainSqlQuery, $SysParams);

    $XmlTag->appendChild($CountryHouses);

    $out = preg_replace('#</country_house>#', "</country_house>\n\n", $XmlTag->saveXML() );   // пробелы для читаемости
    $out = preg_replace('/></', ">\n<", $out );
    echo $out;


