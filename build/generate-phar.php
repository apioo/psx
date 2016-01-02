<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2016 Christoph Kappestein <k42b3.x@gmail.com>
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
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
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
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

