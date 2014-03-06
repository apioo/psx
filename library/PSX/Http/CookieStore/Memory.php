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

namespace PSX\Http\CookieStore;

use PSX\Http\Cookie;
use PSX\Http\CookieStoreInterface;

/**
 * Memory
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Memory implements CookieStoreInterface
{
	private $container = array();

	public function store($domain, Cookie $cookie)
	{
		$key = md5($domain);

		if(!isset($this->container[$key]))
		{
			$this->container[$key] = array();
		}

		$this->container[$key][$cookie->getName()] = $cookie;
	}

	public function load($domain)
	{
		$key = md5($domain);

		if(isset($this->container[$key]))
		{
			return $this->container[$key];
		}
	}

	public function remove($domain, Cookie $cookie)
	{
		$key = md5($domain);

		if(isset($this->container[$key][$cookie->getName()]))
		{
			unset($this->container[$key][$cookie->getName()]);
		}
	}
}

