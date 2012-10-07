<?php
/*
 *  $Id: File.php 652 2012-10-06 22:18:51Z k42b3.x@googlemail.com $
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
 * PSX_Cache_Handler_File
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Cache
 * @version    $Revision: 652 $
 */
class PSX_Cache_Handler_File implements PSX_Cache_HandlerInterface
{
	public function load($key)
	{
		$file = self::getFile($key);

		if(is_file($file))
		{
			$content = file_get_contents($file);
			$time    = filemtime($file);

			return new PSX_Cache_Item($content, $time);
		}
		else
		{
			return false;
		}
	}

	public function write($key, $content, $expire)
	{
		$file = self::getFile($key);

		file_put_contents($file, $content);
	}

	public function remove($key)
	{
		$file = self::getFile($key);

		if(is_file($file))
		{
			unlink($file);
		}
	}

	public static function getFile($key)
	{
		return PSX_PATH_CACHE . '/psx_' . $key . '.cache';
	}
}

