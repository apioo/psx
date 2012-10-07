<?php
/*
 *  $Id: HandlerInterface.php 583 2012-08-15 21:27:23Z k42b3.x@googlemail.com $
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
 * PSX_Http_HandlerInterface
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Http
 * @version    $Revision: 583 $
 */
interface PSX_Http_HandlerInterface
{
	/**
	 * Makes an http request and returns the raw response string including the
	 * header
	 *
	 * @param PSX_Http_Request $request
	 * @return string
	 */
	public function request(PSX_Http_Request $request);

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
