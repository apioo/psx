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

/**
 * ControllerInterface
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
interface ControllerInterface
{
	/**
	 * Method which gets always called before the on* methods are called
	 */
	public function onLoad();

	/**
	 * Method which gets called on an DELETE request
	 */
	public function onDelete();

	/**
	 * Method which gets called on an GET request
	 */
	public function onGet();

	/**
	 * Method which gets called on an HEAD request
	 */
	public function onHead();

	/**
	 * Method which gets called on an OPTIONS request
	 */
	public function onOptions();

	/**
	 * Method which gets called on an POST request
	 */
	public function onPost();

	/**
	 * Method which gets called on an PUT request
	 */
	public function onPut();

	/**
	 * Method which gets called on an TRACE request
	 */
	public function onTrace();

	/**
	 * Is called after the controller action was called
	 */
	public function processResponse();
}
