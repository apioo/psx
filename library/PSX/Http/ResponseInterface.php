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

namespace PSX\Http;

/**
 * ResponseInterface
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
interface ResponseInterface extends MessageInterface
{
	/**
	 * Returns the status code of the response
	 *
	 * @return integer
	 */
	public function getStatusCode();

	/**
	 * Returns the http response message. That means the last part of the status
	 * line i.e. "OK" from an 200 response
	 *
	 * @return string
	 */
	public function getReasonPhrase();

	/**
	 * Sets the status code and reason phrase. If no reason phrase is provided
	 * the standard message according to the status code is used
	 *
	 * @param integer $code
	 * @param integer $reasonPhrase
	 */
	public function setStatus($code, $reasonPhrase = null);
}
