<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2014 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Api;

use PSX\Data\SchemaInterface;

/**
 * View
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class View implements \IteratorAggregate
{
	const STATUS_ACTIVE     = 0x0;
	const STATUS_DEPRECATED = 0x1;
	const STATUS_CLOSED     = 0x2;

	const METHOD_GET    = 0x1;
	const METHOD_POST   = 0x2;
	const METHOD_PUT    = 0x4;
	const METHOD_DELETE = 0x8;

	const TYPE_REQUEST  = 0x10;
	const TYPE_RESPONSE = 0x20;

	protected $status;
	protected $container = array();

	public function __construct($status = self::STATUS_ACTIVE)
	{
		$this->status = $status;
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

	public function setGet(SchemaInterface $responseSchema)
	{
		$this->set(self::METHOD_GET | self::TYPE_RESPONSE, $responseSchema);
	}

	public function hasGet()
	{
		return $this->has(self::METHOD_GET);
	}

	public function hasGetResponse()
	{
		return $this->has(self::METHOD_GET | self::TYPE_RESPONSE);
	}

	public function getGetResponse()
	{
		return $this->get(self::METHOD_GET | self::TYPE_RESPONSE);
	}

	public function setPost(SchemaInterface $requestSchema, SchemaInterface $responseSchema = null)
	{
		$this->set(self::METHOD_POST | self::TYPE_REQUEST, $requestSchema);
		$this->set(self::METHOD_POST | self::TYPE_RESPONSE, $responseSchema);
	}

	public function hasPost()
	{
		return $this->has(self::METHOD_POST);
	}

	public function hasPostRequest()
	{
		return $this->has(self::METHOD_POST | self::TYPE_REQUEST);
	}

	public function getPostRequest()
	{
		return $this->get(self::METHOD_POST | self::TYPE_REQUEST);
	}

	public function hasPostResponse()
	{
		return $this->has(self::METHOD_POST | self::TYPE_RESPONSE);
	}

	public function getPostResponse()
	{
		return $this->get(self::METHOD_POST | self::TYPE_RESPONSE);
	}

	public function setPut(SchemaInterface $requestSchema, SchemaInterface $responseSchema = null)
	{
		$this->set(self::METHOD_PUT | self::TYPE_REQUEST, $requestSchema);
		$this->set(self::METHOD_PUT | self::TYPE_RESPONSE, $responseSchema);
	}

	public function hasPut()
	{
		return $this->has(self::METHOD_PUT);
	}

	public function hasPutRequest()
	{
		return $this->has(self::METHOD_PUT | self::TYPE_REQUEST);
	}

	public function getPutRequest()
	{
		return $this->get(self::METHOD_PUT | self::TYPE_REQUEST);
	}

	public function hasPutResponse()
	{
		return $this->has(self::METHOD_PUT | self::TYPE_RESPONSE);
	}

	public function getPutResponse()
	{
		return $this->get(self::METHOD_PUT | self::TYPE_RESPONSE);
	}

	public function setDelete(SchemaInterface $requestSchema, SchemaInterface $responseSchema = null)
	{
		$this->set(self::METHOD_DELETE | self::TYPE_REQUEST, $requestSchema);
		$this->set(self::METHOD_DELETE | self::TYPE_RESPONSE, $responseSchema);
	}

	public function hasDelete()
	{
		return $this->has(self::METHOD_DELETE);
	}

	public function hasDeleteRequest()
	{
		return $this->has(self::METHOD_DELETE | self::TYPE_REQUEST);
	}

	public function getDeleteRequest()
	{
		return $this->get(self::METHOD_DELETE | self::TYPE_REQUEST);
	}

	public function hasDeleteResponse()
	{
		return $this->has(self::METHOD_DELETE | self::TYPE_RESPONSE);
	}

	public function getDeleteResponse()
	{
		return $this->get(self::METHOD_DELETE | self::TYPE_RESPONSE);
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
		else
		{
			$result = 0;
			foreach($this->container as $key => $view)
			{
				$result|= $key;
			}

			return (bool) ($result & $modifier);
		}
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

	public function getAllowedMethods()
	{
		$methods = array();

		foreach($this->container as $key => $view)
		{
			if($key & self::METHOD_GET)
			{
				$methods[] = 'GET';
			}
			else if($key & self::METHOD_POST)
			{
				$methods[] = 'POST';
			}
			else if($key & self::METHOD_PUT)
			{
				$methods[] = 'PUT';
			}
			else if($key & self::METHOD_DELETE)
			{
				$methods[] = 'DELETE';
			}
		}

		return array_values(array_unique($methods));
	}

	public function getIterator()
	{
		return new \ArrayIterator($this->container);
	}
}
