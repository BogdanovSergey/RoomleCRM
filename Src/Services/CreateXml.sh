#!/bin/sh
# файл должен быть executabe!
# в параметре к запуску данного файла указываем путь к CRM, например:
# /var/www/vhosts/CRM.roomle.ru/Services/Xml/CreateXml.sh /var/www/vhosts/CRM.roomle.ru
ROOTDIR=$1
WORKINGDIR=$ROOTDIR/xml
TEMPDIR=$ROOTDIR/xml/tmp

/usr/bin/php $ROOTDIR/Services/Xml/XmlWinner.php        > $TEMPDIR/winner_flats.xml
/usr/bin/php $ROOTDIR/Services/Xml/XmlWinnerCountry.php > $TEMPDIR/WinnerCountry.xml
/usr/bin/php $ROOTDIR/Services/Xml/XmlWinnerDoli.php    > $TEMPDIR/WinnerDoli.xml

/usr/bin/php $ROOTDIR/Services/Xml/XmlCian.php          > $TEMPDIR/CianFlats.xml
/usr/bin/php $ROOTDIR/Services/Xml/XmlCianCountry.php   > $TEMPDIR/CianCountry.xml

/usr/bin/php $ROOTDIR/Services/Xml/XmlAvito.php         > $TEMPDIR/Avito.xml

/usr/bin/php $ROOTDIR/Services/Xml/XmlNavigator.php         > $TEMPDIR/NavigatorFlats.xml
/usr/bin/php $ROOTDIR/Services/Xml/XmlNavigatorCountry.php  > $TEMPDIR/NavigatorCountry.xml

/usr/bin/php $ROOTDIR/Services/Xml/XmlSiteFlats.php       > $TEMPDIR/SiteFlats.xml
/usr/bin/php $ROOTDIR/Services/Xml/XmlSiteCountry.php     > $TEMPDIR/SiteCountry.xml

/usr/bin/php $ROOTDIR/Services/Xml/XmlRbcFlats.php        > $TEMPDIR/RbcFlats.xml
/usr/bin/php $ROOTDIR/Services/Xml/XmlRbcCountry.php      > $TEMPDIR/RbcCountry.xml

/usr/bin/php $ROOTDIR/Services/Xml/XmlAfy.php           > $TEMPDIR/Afy.xml

/usr/bin/php $ROOTDIR/Services/Xml/XmlMiel.php           > $TEMPDIR/Miel.xml

/usr/bin/php $ROOTDIR/Services/Xml/XmlYandex.php           > $TEMPDIR/Yandex.xml

/bin/mv -f $TEMPDIR/*.xml $WORKINGDIR/
