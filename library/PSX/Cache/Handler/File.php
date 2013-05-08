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

namespace PSX\Cache\Handler;

use PSX\Cache\HandlerInterface;
use PSX\Cache\Item;

/**
 * File
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class File implements HandlerInterface
{
	public function load($key)
	{
		$file = self::getFile($key);

		if(is_file($file))
		{
			$content = file_get_contents($file);
			$time    = filemtime($file);

			return new Item($content, $time);
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

