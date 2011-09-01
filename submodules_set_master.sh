#!/bin/sh

for module in Propel kobold yaffmap-map PHPLiveX; do
	echo "update $module:"
	cd build/vendor/$module
	git checkout master
	cd ../../../
done