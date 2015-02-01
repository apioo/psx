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
 * MessageInterface
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
interface MessageInterface
{
	/**
	 * @return string
	 */
	public function getProtocolVersion();

	/**
	 * @param string $protocol
	 */
	public function setProtocolVersion($protocol);

	/**
	 * @return array
	 */
	public function getHeaders();

	/**
	 * @param array $headers
	 */
	public function setHeaders(array $headers);

	/**
	 * @param string $name
	 * @return boolean
	 */
	public function hasHeader($name);

	/**
	 * @param string $name
	 * @return string
	 */
	public function getHeader($name);

	/**
	 * @param string $name
	 * @return array
	 */
	public function getHeaderLines($name);

	/**
	 * @param string $name
	 * @param string|array $value
	 */
	public function setHeader($name, $value);

	/**
	 * @param string $name
	 * @param string|array $value
	 */
	public function addHeader($name, $value);

	/**
	 * Returns the message body
	 *
	 * @return Psr\Http\StreamableInterface
	 */
	public function getBody();

	/**
	 * Sets the response body
	 *
	 * @param Psr\Http\Message\StreamableInterface $body
	 */
	public function setBody(StreamableInterface $body);
}
