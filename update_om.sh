#!/bin/sh

if [ ! -f propel-gen ] 
then
  ln -s build/vendor/Propel/generator/bin/propel-gen propel-gen
fi

./propel-gen om
./propel-gen sql
./propel-gen insert-sql
./propel-gen convert-conf
