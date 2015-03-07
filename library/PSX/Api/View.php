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

use BadMethodCallException;
use PSX\Data\SchemaInterface;

/**
 * A view is an abstract representation of an resource. It provides informations
 * what methods the resource accepts and which request and response schemas
 * are required. Based on an view we can validate incomming/outgoing data and
 * create documentation and schema definitions
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class View implements \IteratorAggregate
{
	const STATUS_ACTIVE     = 0x0;
	const STATUS_DEPRECATED = 0x1;
	const STATUS_CLOSED     = 0x2;

	const METHOD_GET        = 0x1;
	const METHOD_POST       = 0x2;
	const METHOD_PUT        = 0x4;
	const METHOD_DELETE     = 0x8;

	const TYPE_REQUEST      = 0x10;
	const TYPE_RESPONSE     = 0x20;
	const TYPE_PARAMETER    = 0x40;

	protected static $methods = array(
		self::METHOD_GET    => 'GET',
		self::METHOD_POST   => 'POST',
		self::METHOD_PUT    => 'PUT',
		self::METHOD_DELETE => 'DELETE',
	);

	protected static $types = array(
		self::TYPE_REQUEST   => 'Request',
		self::TYPE_RESPONSE  => 'Response',
		self::TYPE_PARAMETER => 'Parameter',
	);

	protected $status;
	protected $path;
	protected $container = array();

	/**
	 * Provides the status of the view and optional the path to the resource. 
	 * The path is the actual route to the resource so it may contains variable
	 * path fragments i.e.: /foo/:bar
	 *
	 * @param integer $status
	 * @param string $path
	 */
	public function __construct($status = self::STATUS_ACTIVE, $path = null)
	{
		$this->status = $status;
		$this->path   = $path;
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

	public function get($modifier)
	{
		return isset($this->container[$modifier]) ? $this->container[$modifier] : null;
	}

	public function has($modifier)
	{
		if($modifier >= self::TYPE_REQUEST)
		{
			return isset($this->container[$modifier]);
		}

		$result = 0;
		foreach($this->container as $key => $view)
		{
			$result|= $key;
		}

		return (bool) ($result & $modifier);
	}

	public function set($modifier, SchemaInterface $schema = null)
	{
		if($schema !== null)
		{
			$this->container[$modifier] = $schema;
		}
		else if(isset($this->container[$modifier]))
		{
			unset($this->container[$modifier]);
		}
	}

	public function getIterator()
	{
		return new \ArrayIterator($this->container);
	}

	public static function getMethods()
	{
		return self::$methods;
	}

	public static function getTypes()
	{
		return self::$types;
	}

	public static function getMethodName($modifier)
	{
		foreach(self::$methods as $value => $name)
		{
			if($modifier & $value)
			{
				return $name;
			}
		}

		return null;
	}

	public static function getTypeName($modifier)
	{
		foreach(self::$types as $value => $name)
		{
			if($modifier & $value)
			{
				return $name;
			}
		}

		return null;
	}
}
