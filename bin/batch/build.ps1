##
# PSX build script
#
# @author     Christoph Kappestein <k42b3.x@gmail.com>
# @license    http://www.gnu.org/licenses/gpl.html GPLv3
# @link       http://phpsx.org
# @category   PSX
# @version    $Revision: 664 $

# checking whether all needed commands exist
$cmds = "php -v", "svn --version", "phpunit --version", "tar --version", "zip -L"

foreach($cmd in $cmds)
{
	# throws an error if the command does not exist
	$result = iex $cmd
}

# get version
cd ".."

$version = iex "php get-version.php"

echo "Building version $version";

# move existing zip release to history folder
$file = "psx_$version.zip"

if(Test-Path $file)
{
	Move-Item $file "history/$file"

	echo "Moved existing release $file to history folder";
}

# running tests
echo "Running tests"

cd "../tests"

phpunit --bootstrap Bootstrap.php AllTests.php

if($?)
{
	# tests successful
	echo "Tests successful"

	cd "../bin"

	# clean up previous builds
	echo "Cleaning up previous build files"

	$files = "psx", "psx-release", "PSX-$version", "PSX-$version.tgz", "psx_$version.tar.gz", "psx_$version.zip"

	foreach($file in $files)
	{
		if(Test-Path $file)
		{
			Remove-Item $file
		}
	}

	# export svn
	echo "Export repository"

	svn export ../ psx

	# basic syntax check by including all classes of the framework
	echo "Check syntax";

	$result = iex "php check-syntax.php psx"

	if($result -ne "OK")
	{
		echo "Syntax check failed"
		exit
	}

	# build release
	mkdir "psx-release"

	robocopy psx/cache psx-release/cache /E
	robocopy psx/doc psx-release/doc /E /XD docbook
	robocopy psx/library psx-release/library /E
	robocopy psx/module psx-release/module /E
	robocopy psx/public psx-release/public /E
	robocopy psx/template psx-release/template /E

	copy psx\configuration.php psx-release\configuration.php

	# build pear
	echo "Build pear"

	mkdir PSX-$version

	robocopy psx/library/PSX PSX-$version/PSX /E
	robocopy psx/tests PSX-$version/tests /E

	php generate-pear.php $version

	tar zcvf PSX-$version.tgz PSX-$version package.xml

	# build phar
	echo "Build phar"

	php generate-phar.php psx-$version

	# compress
	echo "Create archives"

	cd psx-release

	tar zcvf psx_$version.tar.gz *
	move psx_$version.tar.gz ../

	zip -r psx_$version.zip *
	move psx_$version.zip ../

	cd "../batch"

	echo "Build successful ..."
}
else
{
	# tests failed
	cd "../bin/batch"

	echo "Tests failed ..."
}

