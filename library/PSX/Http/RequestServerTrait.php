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
 * RequestServerTrait
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
trait RequestServerTrait
{
	/**
	 * @var array
	 */
	protected $bodyParams;

	/**
	 * @var array
	 */
	protected $cookieParams;

	/**
	 * @var array
	 */
	protected $fileParams;

	/**
	 * @var array
	 */
	protected $queryParams;

	/**
	 * @var array
	 */
	protected $serverParams;

	/**
	 * @var array
	 */
	protected $attributes = array();

	public function getBodyParams()
	{
		return $this->bodyParams;
	}

	public function setBodyParams(array $bodyParams)
	{
		$this->bodyParams = $bodyParams;
	}

	public function getCookieParams()
	{
		return $this->cookieParams;
	}

	public function setCookieParams(array $cookieParams)
	{
		$this->cookieParams = $cookieParams;
	}

	public function getFileParams()
	{
		return $this->fileParams;
	}

	public function setFileParams(array $fileParams)
	{
		$this->fileParams = $fileParams;
	}

	public function getQueryParams()
	{
		if(!empty($this->queryParams))
		{
			return $this->queryParams;
		}
		else
		{
			return $this->uri->getParameters();
		}
	}

	public function setQueryParams(array $queryParams)
	{
		$this->queryParams = $queryParams;
	}

	public function getServerParams()
	{
		return $this->serverParams;
	}

	public function setServerParams(array $serverParams)
	{
		$this->serverParams = $serverParams;
	}

	public function getAttributes()
	{
		return $this->attributes;
	}

	public function getAttribute($name)
	{
		return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
	}

	public function setAttribute($name, $value)
	{
		$this->attributes[$name] = $value;
	}

	public function removeAttribute($name)
	{
		if(isset($this->attributes[$name]))
		{
			unset($this->attributes[$name]);
		}
	}
}