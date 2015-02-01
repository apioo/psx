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

namespace PSX\Controller\Behaviour;

use PSX\Validate;

/**
 * Provides methods to read and set values from the request/response
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link	http://phpsx.org
 */
trait HttpTrait
{
	/**
	 * Returns the request method. Note the X-HTTP-Method-Override header 
	 * replaces the actually request method if available
	 *
	 * @return string
	 */
	protected function getMethod()
	{
		return $this->request->getMethod();
	}

	/**
	 * Returns the request uri
	 *
	 * @return PSX\Uri
	 */
	protected function getUri()
	{
		return $this->request->getUri();
	}

	/**
	 * Sets the response status code
	 *
	 * @param integer $code
	 */
	protected function setResponseCode($code)
	{
		$this->response->setStatus($code);
	}

	/**
	 * Sets an response header
	 *
	 * @param string $name
	 * @param string $value
	 */
	protected function setHeader($name, $value)
	{
		$this->response->setHeader($name, $value);
	}

	/**
	 * Returns an specific request header
	 *
	 * @param string $key
	 * @return string
	 */
	protected function getHeader($key)
	{
		return $this->request->getHeader($key);
	}

	/**
	 * Returns whether an header is available
	 *
	 * @param string $key
	 * @return boolean
	 */
	protected function hasHeader($key)
	{
		return $this->request->hasHeader($key);
	}

	/**
	 * Returns an parameter from the query fragment of the request url
	 *
	 * @param string $key
	 * @param string $type
	 * @param array $filter
	 * @param string $title
	 * @param boolean $required
	 * @return mixed
	 */
	protected function getParameter($key, $type = Validate::TYPE_STRING, array $filter = array(), $title = null, $required = true)
	{
		$parameters = $this->request->getQueryParams();

		if(isset($parameters[$key]))
		{
			return $this->validate->apply($parameters[$key], $type, $filter, $title, $required);
		}
		else
		{
			return null;
		}
	}

	/**
	 * Returns all available request parameters
	 *
	 * @return array
	 */
	protected function getParameters()
	{
		return $this->request->getQueryParams();
	}
}
