<?php
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

namespace PSX\Cache\Handler;

use PSX\Cache\HandlerInterface;
use PSX\Cache\Item;

/**
 * Cache handle which writes cache items to an file. Note this handler does not 
 * work after 2038 since the expire timestamp of the file is stored in the first 
 * 32bits of the file
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class File implements HandlerInterface
{
	protected $path;

	public function __construct($path = null)
	{
		$this->path = $path === null ? PSX_PATH_CACHE : $path;
	}

	public function load($key)
	{
		$file = $this->getFile($key);

		if(is_file($file))
		{
			$handle = fopen($file, 'r');
			$ttl    = unpack('I*', fread($handle, 4));
			$ttl    = (int) current($ttl);

			if($ttl >= time())
			{
				$value = stream_get_contents($handle);

				fclose($handle);

				return new Item($key, unserialize($value), true, new \DateTime('@' . $ttl));
			}
			else
			{
				fclose($handle);
			}
		}

		return new Item($key, null, false);
	}

	public function write(Item $item)
	{
		$file = $this->getFile($item->getKey());

		if($item->hasExpiration())
		{
			$ttl = $item->getExpiration()->getTimestamp();
		}
		else
		{
			$ttl = PHP_INT_MAX;
		}

		// we write the expire date in the first bits of the file and then the 
		// content
		$data = pack('I*', $ttl);
		$data.= serialize($item->get());

		file_put_contents($file, $data);
	}

	public function remove($key)
	{
		$file = $this->getFile($key);

		if(is_file($file))
		{
			unlink($file);
		}
	}

	public function removeAll()
	{
		$files = scandir($this->path);

		foreach($files as $file)
		{
			$item = $this->path . '/' . $file;

			if(is_file($item) && preg_match('/^psx_(.*).cache$/', $file))
			{
				unlink($item);
			}
		}

		return true;
	}

	public function getFile($key)
	{
		return $this->path . '/psx_' . $key . '.cache';
	}
}

