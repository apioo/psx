<?php
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

namespace PSX;

use PSX\File\Object;
use SplFileInfo;

/**
 * Represents a file and offers a high-level object oriented interface for
 * informations about an file. It doesnt touch the file if you create an 
 * instance. Here an example code:
 * <code>
 * $file = new File('foo.txt');
 *
 * if($file->isFile() && $file->isWritable())
 * {
 * 	$file->open('w')->fwrite('foobar');
 * }
 * </code>
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class File extends SplFileInfo
{
	public function __construct($fileName)
	{
		parent::__construct($fileName);

		$this->setInfoClass('\PSX\File');
		$this->setFileClass('\PSX\File\Object');
	}

	/**
	 * Factory method to create a new file object. You can also use the openFile
	 * method to get a new file object
	 *
	 * @param string $file
	 * @param string $mode
	 * @param boolean $useIncludePath
	 * @return \PSX\File\Object
	 */
	public static function open($file, $mode = 'r', $useIncludePath = false)
	{
		return new Object($file, $mode, $useIncludePath);
	}

	/**
	 * Returns whether the file exists or not. Inorder to improve performance
	 * this method could always return true obvious this makes it more unsafe
	 *
	 * @param string $file
	 * @return boolean
	 */
	public static function exists($file)
	{
		return is_file($file);
	}

	/**
	 * Returns the complete content of the $file
	 *
     * @param string $file
	 * @return string
	 */
	public static function getContents($file)
	{
		return file_get_contents($file);
	}

	/**
	 * Writes the $content into the $file
	 *
     * @param string $file
     * @param string $content
     * @param integer $flags
     * @param resource $context
	 * @return integer|false
	 */
	public static function putContents($file, $content, $flags = 0, $context = null)
	{
		return file_put_contents($file, $content, $flags, $context);
	}

	/**
	 * Removes all chars from the string which are not allowed in an file name
	 *
     * @param string $fileName
	 * @return string
	 */
	public static function normalizeName($fileName)
	{
		$fileName = str_replace(' ', '-', $fileName);
		$fileName = preg_replace('/[^A-Za-z0-9\.\_\-]/', '', $fileName);

		return $fileName;
	}
}

