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

/**
 * RequestServerTrait
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
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