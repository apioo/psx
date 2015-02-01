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

use Psr\Http\Message\StreamableInterface;

/**
 * This is a mutable version of the PSR HTTP message interface
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 * @see     https://github.com/php-fig/fig-standards/blob/master/proposed/http-message.md
 */
interface MessageInterface
{
	/**
	 * Retrieves the HTTP protocol version as a string
	 *
	 * @return string
	 */
	public function getProtocolVersion();

	/**
	 * Sets the HTTP protocol version as a string
	 *
	 * @param string $protocol
	 */
	public function setProtocolVersion($protocol);

	/**
	 * Retrieves all message headers
	 *
	 * @return array
	 */
	public function getHeaders();

	/**
	 * Sets all message headers
	 *
	 * @param array $headers
	 */
	public function setHeaders(array $headers);

	/**
	 * Checks if a header exists by the given case-insensitive name
	 *
	 * @param string $name
	 * @return boolean
	 */
	public function hasHeader($name);

	/**
	 * Retrieve a header by the given case-insensitive name, as a string
	 *
	 * @param string $name
	 * @return string
	 */
	public function getHeader($name);

	/**
	 * Retrieves a header by the given case-insensitive name as an array of 
	 * strings
	 *
	 * @param string $name
	 * @return array
	 */
	public function getHeaderLines($name);

	/**
	 * Sets a new header, replacing any existing values of any headers with the 
	 * same case-insensitive name
	 *
	 * @param string $name
	 * @param string|array $value
	 */
	public function setHeader($name, $value);

	/**
	 * Sets a new header, appended with the given value
	 *
	 * @param string $name
	 * @param string|array $value
	 */
	public function addHeader($name, $value);

	/**
	 * Gets the body of the message
	 *
	 * @return Psr\Http\Message\StreamableInterface
	 */
	public function getBody();

	/**
	 * Sets the specified message body
	 *
	 * @param Psr\Http\Message\StreamableInterface $body
	 */
	public function setBody(StreamableInterface $body);
}
