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

use PSX\Uri;

/**
 * This is a mutable version of the PSR HTTP message interface
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 * @see     https://github.com/php-fig/fig-standards/blob/master/proposed/http-message.md
 */
interface RequestInterface extends MessageInterface
{
	/**
	 * Retrieves the message request target
	 *
	 * @return string
	 */
	public function getRequestTarget();

	/**
	 * Sets the message request target
	 *
	 * @param string $requestTarget
	 */
	public function setRequestTarget($requestTarget);

	/**
	 * Retrieves the HTTP method of the request
	 *
	 * @return string
	 */
	public function getMethod();

	/**
	 * Sets the HTTP method of the request
	 *
	 * @param string $method
	 */
	public function setMethod($method);

	/**
	 * Retrieves the URI instance
	 *
	 * @return PSX\Uri
	 */
	public function getUri();

	/**
	 * Sets the URI instance
	 *
	 * @param PSX\Uri $uri
	 */
	public function setUri(Uri $uri);

	/**
	 * If the request body can be deserialized to an array, this method MAY be
	 * used to retrieve them
	 *
	 * @return array
	 */
	public function getBodyParams();

	/**
	 * Sets the body parameters
	 *
	 * @param array $bodyParams
	 */
	public function setBodyParams(array $bodyParams);

	/**
	 * Retrieves cookies sent by the client to the server. The data MUST be 
	 * compatible with the structure of the $_COOKIE superglobal
	 *
	 * @return array
	 */
	public function getCookieParams();

	/**
	 * Sets the cookie parameters
	 *
	 * @param array $cookieParams
	 */
	public function setCookieParams(array $cookieParams);

	/**
	 * This method MUST return file upload metadata in the same structure as 
	 * PHP's $_FILES superglobal. These values MUST remain immutable over the 
	 * course of the incoming request. They SHOULD be injected during 
	 * instantiation, such as from PHP's $_FILES superglobal, but MAY be derived 
	 * from other sources
	 *
	 * @return array
	 */
	public function getFileParams();

	/**
	 * Sets the file parameters
	 *
	 * @param array $fileParams
	 */
	public function setFileParams(array $fileParams);

	/**
	 * Retrieves the deserialized query string arguments, if any. Note: the 
	 * query params might not be in sync with the URL or server params. If you 
	 * need to ensure you are only getting the original values, you may need to 
	 * parse the composed URL or the `QUERY_STRING` composed in the server 
	 * params
	 *
	 * @return array
	 */
	public function getQueryParams();

	/**
	 * Sets the quey parameters
	 *
	 * @param array $queryParams
	 */
	public function setQueryParams(array $queryParams);

	/**
	 * Retrieves data related to the incoming request environment typically 
	 * derived from PHP's $_SERVER superglobal. The data IS NOT REQUIRED to 
	 * originate from $_SERVER
	 *
	 * @return array
	 */
	public function getServerParams();

	/**
	 * Sets the server parameters
	 *
	 * @param array $serverParams
	 */
	public function setServerParams(array $serverParams);

	/**
	 * Retrieve attributes derived from the request
	 *
	 * @return array
	 */
	public function getAttributes();

	/**
	 * Retrieve a single derived request attribute
	 *
	 * @param string $name
	 * @return mixed
	 */
	public function getAttribute($name);

	/**
	 * Sets the specified derived request attribute
	 *
	 * @param string $name
	 * @param mixed $value
	 */
	public function setAttribute($name, $value);

	/**
	 * Removes the specified derived request attribute
     *
	 * @param string $name
	 */
	public function removeAttribute($name);
}
