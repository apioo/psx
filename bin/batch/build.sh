#!/bin/bash
##
# PSX build script
#
# @author     Christoph Kappestein <k42b3.x@gmail.com>
# @license    http://www.gnu.org/licenses/gpl.html GPLv3
# @link       http://phpsx.org
# @category   PSX
# @version    $Revision: 664 $

# check commands
CMDS=( "php" "svn" "phpunit" "tar" )

for (( i=0;i<${#CMDS[@]};i++ )); do
	cmd=${CMDS[${i}]}
	command -v $cmd >/dev/null 2>&1 || { echo >&2 "Required command $cmd is not installed"; exit 1; }
done

# get version
cd ..

version=`php get-version.php`

echo "Building version $version"

# move existing zip release to history folder
file="psx_$version.zip"

if [ -f $file ]
then
	mv $file history/$file
	echo "Moved existing release $file to history folder"
fi

# running tests
echo "Running tests"

cd "../tests"

phpunit --bootstrap Bootstrap.php AllTests.php

if [ $? == 0 ]
then
	# tests successful
	echo "Tests successful"

	cd ../bin

	# clean up previous builds
	echo "Cleaning up previous build files"

	FILES=( "psx" "psx-release" "PSX-$version" "PSX-$version.tgz" "psx_$version.tar.gz" "psx_$version.zip" )

	for (( i=0;i<${#FILES[@]};i++ )); do
		file=${FILES[${i}]}
		# remove file
		if [ -f $file ]
		then
			rm $file
			echo "Removed $file"
		fi
		# remove folder
		if [ -d $file ]
		then
			rm -r $file
			echo "Removed $file"
		fi
	done

	# export svn
	echo "Export repository"

	svn export ../ psx

	# basic syntax check by including all classes of the framework
	echo "Check syntax";

	result=`php check-syntax.php`

	if($result != "OK")
	{
		echo "Syntax check failed"
		exit
	}

	# build release
	mkdir psx-release

	cp -r psx/cache psx-release/cache
	cp -r psx/doc psx-release/doc
	cp -r psx/library psx-release/library
	cp -r psx/module psx-release/module
	cp -r psx/public psx-release/public
	cp -r psx/template psx-release/template

	cp psx/configuration.php psx-release/configuration.php

	# build pear
	echo "Build pear"

	mkdir PSX-$version

	cp -r psx/library/PSX PSX-$version/PSX
	cp -r psx/tests PSX-$version/tests

	php generate-pear.php $version

	tar zcvf PSX-$version.tgz PSX-$version package.xml

	# build phar
	echo "Build phar"

	php generate-phar.php psx-$version

	# compress
	echo "Create archives"

	cd psx-release

	tar zcvf psx_$version.tar.gz *
	mv psx_$version.tar.gz ../

	zip -r psx_$version.zip *
	mv psx_$version.zip ../

	cd ../batch

	echo "Build successful ..."
else
	# tests failed
	cd ../bin/batch

	echo "Tests failed ..."
fi

