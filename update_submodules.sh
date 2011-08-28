#!/bin/sh

for module in Propel kobold yaffmap-map PHPLiveX; do
  echo "update $module:"
  cd build/vendor/$module
  git fetch
  git merge origin/master
  cd ../../../
done


