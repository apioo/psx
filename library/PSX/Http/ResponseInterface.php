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
 * This is a mutable version of the PSR HTTP message interface
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 * @see     https://github.com/php-fig/fig-standards/blob/master/proposed/http-message.md
 */
interface ResponseInterface extends MessageInterface
{
	/**
	 * Gets the response Status-Code
	 *
	 * @return integer
	 */
	public function getStatusCode();

	/**
	 * Gets the response Reason-Phrase, a short textual description of the 
	 * Status-Code
	 *
	 * @return string
	 */
	public function getReasonPhrase();

	/**
	 * Sets the status code and reason phrase. If no reason phrase is provided
	 * the standard message according to the status code is used. If the status
	 * code is unknown an reason phrase must be provided 
	 *
	 * @param integer $code
	 * @param integer $reasonPhrase
	 */
	public function setStatus($code, $reasonPhrase = null);
}
