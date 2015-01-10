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

namespace PSX\Loader;

/**
 * This class contains informations about the context from where an command or
 * controller was called. The framework uses the KEY_* constants, in userland
 * you should use strings as keys to not get in conflict with future definitions
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Location
{
	const KEY_FRAGMENT  = 0;
	const KEY_SOURCE    = 1;
	const KEY_EXCEPTION = 2;

	protected $parameters;

	public function __construct(array $parameters = array())
	{
		$this->parameters = $parameters;
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
