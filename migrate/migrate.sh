#!/bin/sh
cd ..
ln -s runtime-conf.xml buildtime-conf.xml
cd migrate
/usr/bin/php5 dropViews.php
propel-gen ../ diff
propel-gen ../ migrate
#/usr/bin/php5 dropTables.php
/usr/bin/php5 createViews.php
propel-gen ../ om