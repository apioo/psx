<?php
/*
 *  $Id: Memory.php 579 2012-08-14 18:22:10Z k42b3.x@googlemail.com $
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
 * PSX_Http_CookieStore_Memory
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Http
 * @version    $Revision: 579 $
 */
class PSX_Http_CookieStore_Memory implements PSX_Http_CookieStoreInterface
{
	private $container = array();

	public function store($domain, PSX_Http_Cookie $cookie)
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

	public function remove($domain, PSX_Http_Cookie $cookie)
	{
		$key = md5($domain);

		if(isset($this->container[$key][$cookie->getName()]))
		{
			unset($this->container[$key][$cookie->getName()]);
		}
	}
}

