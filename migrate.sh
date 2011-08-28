#!/bin/sh
if [ ! -f propel-gen ]
then
	ln -s build/vendor/Propel/generator/bin/propel-gen propel-gen
fi
ln -s runtime-conf.xml buildtime-conf.xml
./propel-gen diff
./propel-gen migrate
./propel-gen om