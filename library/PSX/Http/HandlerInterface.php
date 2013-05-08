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

namespace PSX\Http;

/**
 * HandlerInterface
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
interface HandlerInterface
{
	/**
	 * Makes an http request and returns the raw response string including the
	 * header
	 *
	 * @param PSX\Http\Request $request
	 * @return string
	 */
	public function request(Request $request);

	/**
	 * Must return the error message of the last request or false if no error
	 * occured
	 *
	 * @return string|false
	 */
	public function getLastError();

	/**
	 * Must return the raw http request string of the last request
	 *
	 * @return string
	 */
	public function getRequest();

	/**
	 * Must return the raw http response string of the last request
	 *
	 * @return string
	 */
	public function getResponse();
}
