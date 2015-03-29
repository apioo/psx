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

namespace PSX\Api;

use PSX\Api\Resource\MethodAbstract;
use PSX\Data\Schema;
use PSX\Data\Schema\Property;
use PSX\Data\Schema\PropertySimpleAbstract;

/**
 * Resource
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link	http://phpsx.org
 */
class Resource implements \IteratorAggregate
{
	const STATUS_ACTIVE      = 0x1;
	const STATUS_DEPRECATED  = 0x2;
	const STATUS_CLOSED      = 0x3;

	const CODE_INFORMATIONAL = 199;
	const CODE_SUCCESS       = 299;
	const CODE_REDIRECTION   = 399;
	const CODE_CLIENT_ERROR  = 499;
	const CODE_SERVER_ERROR  = 599;

	protected $status;
	protected $path;
	protected $title;
	protected $description;
	protected $pathParameters;
	protected $methods;

	public function __construct($status, $path)
	{
		$this->status          = $status;
		$this->path            = $path;
		$this->pathParameters  = new Property\ComplexType('path');
		$this->methods         = array();
	}

	public function isActive()
	{
		return $this->status == self::STATUS_ACTIVE;
	}

	public function isDeprecated()
	{
		return $this->status == self::STATUS_DEPRECATED;
	}

	public function isClosed()
	{
		return $this->status == self::STATUS_CLOSED;
	}

	public function getStatus()
	{
		return $this->status;
	}

	public function getPath()
	{
		return $this->path;
	}

	public function setTitle($title)
	{
		$this->title = $title;
	}
	
	public function getTitle()
	{
		return $this->title;
	}

	public function setDescription($description)
	{
		$this->description = $description;
	}
	
	public function getDescription()
	{
		return $this->description;
	}

	public function addPathParameter(PropertySimpleAbstract $property)
	{
		$this->pathParameters->add($property);

		return $this;
	}

	public function getPathParameters()
	{
		return new Schema($this->pathParameters);
	}

	public function addMethod(MethodAbstract $method)
	{
		$this->methods[$method->getName()] = $method;
	}

	public function getMethod($method)
	{
		if(isset($this->methods[$method]))
		{
			return $this->methods[$method];
		}
		else
		{
			throw new \RuntimeException('Method ' . $method . ' is not available for this resource');
		}
	}

	public function getMethods()
	{
		return $this->methods;
	}

	public function getAllowedMethods()
	{
		return array_keys($this->methods);
	}

	public function hasMethod($method)
	{
		return isset($this->methods[$method]);
	}

	public function getIterator()
	{
		return new \ArrayIterator($this->methods);
	}
}
