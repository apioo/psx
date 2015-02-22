<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX;

use PSX\Cache\CacheItemInterface;
use PSX\Cache\CacheItemPoolInterface;
use PSX\Cache\Handler;
use PSX\Cache\HandlerInterface;

/**
 * Cache
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Cache implements CacheItemPoolInterface
{
	protected $handler;
	protected $items = array();

	public function __construct(HandlerInterface $handler = null)
	{
		$this->handler = $handler === null ? new Handler\File() : $handler;
	}

	public function getItem($key)
	{
		return $this->handler->load($key);
	}

	public function getItems(array $keys = array())
	{
		$items = array();

		foreach($keys as $key)
		{
			$items[] = $this->handler->load($key);
		}

		return $items;
	}

	public function clear()
	{
		return $this->handler->removeAll();
	}

	public function deleteItems(array $keys)
	{
		foreach($keys as $key)
		{
			$this->handler->remove($key);
		}

		return $this;
	}

	public function save(CacheItemInterface $item)
	{
		$this->handler->write($item);

		return $this;
	}

	public function saveDeferred(CacheItemInterface $item)
	{
		$this->items[] = $item;

		return $this;
	}

	public function commit()
	{
		foreach($this->items as $item)
		{
			$this->handler->write($item);
		}

		$this->items = array();

		return true;
	}
}
