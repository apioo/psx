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

use DateTime;

/**
 * Cookie
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Cookie
{
	private $name;
	private $value;
	private $expires;
	private $path;
	private $domain;
	private $secure;
	private $httponly;

	public function __construct($name, $value, DateTime $expires = null, $path = null, $domain = null, $secure = null, $httponly = null)
	{
		$this->name     = $name;
		$this->value    = $value;
		$this->expires  = $expires;
		$this->path     = $path;
		$this->domain   = $domain;
		$this->secure   = $secure;
		$this->httponly = $httponly;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getValue()
	{
		return $this->value;
	}

	public function getExpires()
	{
		return $this->expires;
	}

	public function getPath()
	{
		return $this->path;
	}

	public function getDomain()
	{
		return $this->domain;
	}

	public function getSecure()
	{
		return $this->secure;
	}

	public function getHttponly()
	{
		return $this->httponly;
	}
}
