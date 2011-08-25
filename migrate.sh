#!/bin/sh
ln -s runtime-conf.xml buildtime-conf.xml
./propel-gen diff
./propel-gen migrate
./propel-gen om