<?php

require(dirname(__FILE__) . '/../../Conf/Config.php');
require(dirname(__FILE__) . '/../../Lib/Xml/Xml.php');
require(dirname(__FILE__) . '/../../Lib/Xml/Miel.php');

DBConnect();

// добавляем в корневой элемен  атрибут с датой
//$generationdate = $XmlTag->createElement( 'generation-date', date('c') );
//$Flats->appendChild( $generationdate );
$SysParams['RealtyType']    = 'city';
$SysParams['ElementTag']    = 'offer';

$XmlTag = new DOMDocument('1.0', 'UTF-8');
// создаем корневой элемент
$Flats = $XmlTag->createElement('offers');
//$Flats->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
//$Flats->setAttribute('xsi:noNamespaceSchemaLocation', 'flats.xsd');

$MainSqlQuery  = CreateSQLQueryForXMLLoad('MielFlats', $SysParams);

$Flats = BuildXmlForMiel($XmlTag, $Flats, $MainSqlQuery, $SysParams);

$XmlTag->appendChild($Flats);

$out = preg_replace('#</offer>#', "</offer>\n\n", $XmlTag->saveXML() );   // пробелы для читаемости
$out = preg_replace('/></', ">\n<", $out );
echo $out;


