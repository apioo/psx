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

namespace PSX;

/**
 * Interface wich describes an DI container. These are the methods wich an DI
 * container must have. Because psx doesnt explicit check whether an
 * DependencyInterface type is returned you can also return an symfony DI 
 * container wich implements the same methods.
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
interface DependencyInterface
{
	/**
	 * Sets an service to the container
	 *
	 * @param string $name
	 * @param object $obj
	 * @return void
	 */
	public function set($name, $obj);

	/**
	 * Gets an service from the container
	 *
	 * @param string $name
	 * @return object
	 */
	public function get($name);

	/**
	 * Checks wheterh the service is available
	 *
	 * @param string $name
	 * @return boolean
	 */
	public function has($name);
}
