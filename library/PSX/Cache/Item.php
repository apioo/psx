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

namespace PSX\Cache;

use Psr\Cache\CacheItemInterface;

/**
 * Item
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Item /*implements CacheItemInterface*/
{
	protected $key;
	protected $value;
	protected $isHit;
	protected $ttl;

	public function __construct($key, $value, $isHit, $ttl = null)
	{
		$this->key   = $key;
		$this->value = $value;
		$this->isHit = $isHit;

		$this->setExpiration($ttl);
	}

	public function getKey()
	{
		return $this->key;
	}

	public function get()
	{
		return $this->value;
	}

	public function set($value, $ttl = null)
	{
		$this->value = $value;

		$this->setExpiration($ttl);

		return $this;
	}

	public function isHit()
	{
		return $this->isHit;
	}

	public function exists()
	{
		return $this->value !== null;
	}

	public function setExpiration($ttl = null)
	{
		if(is_numeric($ttl))
		{
			$this->ttl = time() + $ttl;
		}
		else if($ttl instanceof \DateTime)
		{
			$this->ttl = $ttl->getTimestamp();
		}
		else if($ttl === null)
		{
			$this->ttl = null;
		}

		return $this;
	}

	public function getExpiration()
	{
		if($this->ttl === null)
		{
			return new \DateTime();
		}
		else
		{
			return new \DateTime('@' . $this->ttl);
		}
	}

	public function hasExpiration()
	{
		return $this->ttl !== null;
	}
}
