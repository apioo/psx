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

namespace PSX\Filter;

use PSX\FilterAbstract;

/**
 * KeyExists
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class KeyExists extends FilterAbstract
{
	private $container;

	public function __construct(array $container)
	{
		$this->container = $container;
	}

	/**
	 * Returns true if value is an key in the array $this->container else false
	 *
	 * @param mixed $value
	 * @return boolean
	 */
	public function apply($value)
	{
		$key = (string) $value;

		return isset($this->container[$key]);
	}

	public function getErrorMsg()
	{
		return '%s is not valid value';
	}
}
