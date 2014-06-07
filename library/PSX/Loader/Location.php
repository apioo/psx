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

namespace PSX\Loader;

/**
 * Previously this object represents an Location which must have an id, fragment
 * and source parameter. Since we can create an controller from other contexts 
 * this object was redesigned to an general key value store which can be passed 
 * to an controller.
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Location
{
	const KEY_ID        = 1;
	const KEY_FRAGMENT  = 2;
	const KEY_SOURCE    = 3;
	const KEY_EXCEPTION = 4;

	protected $parameters;

	public function __construct($id = null, $fragments = null, $source = null)
	{
		$this->parameters = array(
			self::KEY_ID       => $id,
			self::KEY_FRAGMENT => $fragments,
			self::KEY_SOURCE   => $source,
		);
	}

	/**
	 * Returns an unique id representing this location
	 *
	 * @deprecated
	 * @return string
	 */
	public function getId()
	{
		return $this->getParameter(self::KEY_ID);
	}

	/**
	 * Returns the path. The loader will try to find the method wich should be
	 * called depending on this path and the @path parameter in the docblock
	 * comments of the controller
	 *
	 * @deprecated
	 * @return string
	 */
	public function getParameters()
	{
		return $this->getParameter(self::KEY_FRAGMENT);
	}

	/**
	 * Returns an string containing all informations for an callback resolver
	 * to resolve it to an callback
	 *
	 * @deprecated
	 * @return string
	 */
	public function getSource()
	{
		return $this->getParameter(self::KEY_SOURCE);
	}

	/**
	 * Sets a parameter
	 *
	 * @param string $key
	 * @param mixed $value
	 */
	public function setParameter($key, $value)
	{
		$this->parameters[$key] = $value;
	}

	/**
	 * Gets a parameter
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function getParameter($key)
	{
		return isset($this->parameters[$key]) ? $this->parameters[$key] : null;
	}
}
