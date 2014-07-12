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

use Memcache as Mem;
use Psr\Cache\CacheItemInterface;
use PSX\Cache\HandlerInterface;
use PSX\Cache\Item;

/**
 * Memcache
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Memcache implements HandlerInterface
{
	private $memcache;

	public function __construct(Mem $memcache)
	{
		$this->memcache = $memcache;
	}

	public function load($key)
	{
		$value = $this->memcache->get($key);

		if($value !== false)
		{
			return new Item($key, $value, true);
		}
		else
		{
			return new Item($key, null, false);
		}
	}

	public function write(CacheItemInterface $item)
	{
		$ttl = $item->getExpiration()->getTimestamp();

		$this->memcache->set($item->getKey(), $item->get(), 0, $ttl);
	}

	public function remove($key)
	{
		$this->memcache->delete($key);
	}

	public function removeAll()
	{
		return true;
	}
}
