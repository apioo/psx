<?php
/*
 *  $Id: File.php 612 2012-08-25 11:14:23Z k42b3.x@googlemail.com $
 *
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2012 Christoph Kappestein <k42b3.x@gmail.com>
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

/**
 * Represents a file and offers a high-level object oriented interface to
 * information for an individual file. It doesnt touch the file if you create an
 * instance. Here an example code:
 * <code>
 * $file = new PSX_File('foo.txt');
 *
 * if($file->isFile() && $file->isWritable())
 * {
 * 	$file->open('w')->fwrite('foobar');
 * }
 * </code>
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_File
 * @version    $Revision: 612 $
 */
class PSX_File extends SplFileInfo
{
	public function __construct($fileName)
	{
		parent::__construct($fileName);

		$this->setInfoClass('PSX_File');
		$this->setFileClass('PSX_File_Object');
	}

	/**
	 * Factory method to create a new file object. You can also use the openFile
	 * method to get a new file object
	 *
	 * @param string $file
	 * @param string $mode
	 * @param boolean $useIncludePath
	 * @param resource $context
	 * @return PSX_File_Object
	 */
	public static function open($file, $mode = 'r', $useIncludePath = false)
	{
		return new PSX_File_Object($file, $mode, $useIncludePath);
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
		if(true)
		{
			return is_file($file);
		}
		else
		{
			return true;
		}
	}

	/**
	 * Returns the complete content of the $file
	 *
	 * @return string
	 */
	public static function getContents($file)
	{
		return file_get_contents($file);
	}

	/**
	 * Writes the $content into the $file
	 *
	 * @return integer|false
	 */
	public static function putContents($file, $content, $flags = 0, $context = null)
	{
		return file_put_contents($file, $content, $flags, $context);
	}
}

