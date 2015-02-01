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
 * RequestInterface
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
interface RequestInterface extends MessageInterface
{
	/**
	 * Returns the request target
	 *
	 * @return string
	 */
	public function getRequestTarget();

	/**
	 * Sets the request target
	 *
	 * @param string $requestTarget
	 */
	public function setRequestTarget($requestTarget);

	/**
	 * Returns the request method
	 *
	 * @return string
	 */
	public function getMethod();

	/**
	 * Sets the request method
	 *
	 * @param string $method
	 */
	public function setMethod($method);

	/**
	 * Returns the request uri
	 *
	 * @return PSX\Uri
	 */
	public function getUri();

	/**
	 * Sets the request uri
	 *
	 * @param PSX\Uri $uri
	 */
	public function setUri(Uri $uri);

	/**
	 * @return array
	 */
	public function getBodyParams();

	/**
	 * @param array $bodyParams
	 */
	public function setBodyParams(array $bodyParams);

	/**
	 * @return array
	 */
	public function getCookieParams();

	/**
	 * @param array $cookieParams
	 */
	public function setCookieParams(array $cookieParams);

	/**
	 * @return array
	 */
	public function getFileParams();

	/**
	 * @param array $fileParams
	 */
	public function setFileParams(array $fileParams);

	/**
	 * @return array
	 */
	public function getQueryParams();

	/**
	 * @param array $queryParams
	 */
	public function setQueryParams(array $queryParams);

	/**
	 * @return array
	 */
	public function getServerParams();

	/**
	 * @param array $serverParams
	 */
	public function setServerParams(array $serverParams);

	/**
	 * @return array
	 */
	public function getAttributes();

	/**
	 * @param string $name
	 * @return mixed
	 */
	public function getAttribute($name);

	/**
	 * @param string $name
	 * @param mixed $value
	 */
	public function setAttribute($name, $value);

	/**
	 * @param string $name
	 */
	public function removeAttribute($name);
}
