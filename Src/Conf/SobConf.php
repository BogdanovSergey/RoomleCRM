<?php

// как по тексту описания можно попытаться выявить риэлтора? слова из риэлторского лексикона.
$CONF['SobReadyWordsArr'] = array(
    '/(альтернатива)/iu',
    '/(полная сумма)/iu',
    '/(сумма в договоре)/iu',
    '/(номер объекта)/iu',
    '/(Лот)/u',
    '/(ипотека)/iu',
    '/(дкп)/iu',
    '/(агенту бонус)/iu',
    '/(бонус агенту)/iu',
    '/(объектом занимается)/iu',
    '/(нежилой фонд)/iu',
    '/(комиссия)/iu'
);

// как по тексту описания можно попытаться определить тип сделки "альтернатива"
$CONF['RealtorWords']['AlternativaRegexpArr'] = array(
    '/(альтернатива)/iu',
    '/(по альтернативе)/iu',
    '/(продается с альтернативой)/iu',
    '/(продажа альтернативная)/iu'
);