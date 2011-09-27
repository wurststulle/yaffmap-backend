#!/bin/sh

INSERT=$1

if [ ! -f propel-gen ] 
then
  ln -s build/vendor/Propel/generator/bin/propel-gen propel-gen
fi

./propel-gen om
./propel-gen sql
if [ $INSERT = "insert"]
then
	./propel-gen insert-sql
fi
./propel-gen convert-conf
