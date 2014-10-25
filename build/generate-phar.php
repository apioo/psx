<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2013 Christoph Kappestein <k42b3.x@gmail.com>
 *
 * This file is part of psx. psx is free software: you can
 * redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or any later version.
 *
 * psx is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with psx. If not, see <http://www.gnu.org/licenses/>.
 */

if(!isset($_SERVER['argv'][1]))
{
	throw new Exception('Argument one must be a version number');
}

define('VERSION', $_SERVER['argv'][1]);
define('PATH', 'psx/library');


if(!is_dir(PATH))
{
	throw new Exception('No release dir exists');
}


$license = <<<LICENSE
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2014 Christoph Kappestein <k42b3.x@gmail.com>
 *
 * This file is part of psx. psx is free software: you can
 * redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or any later version.
 *
 * psx is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with psx. If not, see <http://www.gnu.org/licenses/>.
 */
LICENSE;


$file = 'psx-' . VERSION . '.phar';
$dir  = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(PATH), RecursiveIteratorIterator::SELF_FIRST);
$phar = new Phar($file, 0, $file);
$phar->setMetadata($license);

foreach($dir as $file)
{
	$path = (string) $file;
	$name = substr($path, 12); // remove psx/library/ from path

	if($file->isFile())
	{
		$phar->addFromString($name, php_strip_whitespace($path));
	}
	else if($file->isDir() && $file->getFilename() != '.' && $file->getFilename() != '..')
	{
		$phar->addEmptyDir($name);
	}

	echo 'A ' . $name . "\n";
}

