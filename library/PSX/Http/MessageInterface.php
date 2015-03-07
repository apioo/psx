<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace PSX\Http;

use Psr\Http\Message\StreamableInterface;

/**
 * This is a mutable version of the PSR HTTP message interface
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
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
