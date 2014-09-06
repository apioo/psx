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
class View
{
	const METHOD_GET    = 'GET';
	const METHOD_POST   = 'POST';
	const METHOD_PUT    = 'PUT';
	const METHOD_DELETE = 'DELETE';

	const TYPE_REQUEST  = 0x0;
	const TYPE_RESPONSE = 0x1;

	protected $status;
	protected $container = array();

	public function __construct($status = Documentation::STATUS_ACTIVE)
	{
		$this->status = $status;
	}

	public function isActive()
	{
		return $this->status == Documentation::STATUS_ACTIVE;
	}

	public function isDeprecated()
	{
		return $this->status == Documentation::STATUS_DEPRECATED;
	}

	public function isClosed()
	{
		return $this->status == Documentation::STATUS_CLOSED;
	}

	public function getStatus()
	{
		return $this->status;
	}

	public function setGet(SchemaInterface $responseSchema)
	{
		$this->container[self::METHOD_GET] = array(
			self::TYPE_RESPONSE => $responseSchema
		);
	}

	public function hasGet()
	{
		return isset($this->container[self::METHOD_GET]);
	}

	public function hasGetResponse()
	{
		return isset($this->container[self::METHOD_GET][self::TYPE_RESPONSE]);
	}

	public function getGetResponse()
	{
		return $this->container[self::METHOD_GET][self::TYPE_RESPONSE];
	}

	public function setPost(SchemaInterface $requestSchema, SchemaInterface $responseSchema = null)
	{
		$this->container[self::METHOD_POST] = array(
			self::TYPE_REQUEST  => $requestSchema, 
			self::TYPE_RESPONSE => $responseSchema
		);
	}

	public function hasPost()
	{
		return isset($this->container[self::METHOD_POST]);
	}

	public function hasPostRequest()
	{
		return isset($this->container[self::METHOD_POST][self::TYPE_REQUEST]);
	}

	public function getPostRequest()
	{
		return $this->container[self::METHOD_POST][self::TYPE_REQUEST];
	}

	public function hasPostResponse()
	{
		return isset($this->container[self::METHOD_POST][self::TYPE_RESPONSE]);
	}

	public function getPostResponse()
	{
		return $this->container[self::METHOD_POST][self::TYPE_RESPONSE];
	}

	public function setPut(SchemaInterface $requestSchema, SchemaInterface $responseSchema = null)
	{
		$this->container[self::METHOD_PUT] = array(
			self::TYPE_REQUEST  => $requestSchema, 
			self::TYPE_RESPONSE => $responseSchema
		);
	}

	public function hasPut()
	{
		return isset($this->container[self::METHOD_PUT]);
	}

	public function hasPutRequest()
	{
		return isset($this->container[self::METHOD_PUT][self::TYPE_REQUEST]);
	}

	public function getPutRequest()
	{
		return $this->container[self::METHOD_PUT][self::TYPE_REQUEST];
	}

	public function hasPutResponse()
	{
		return isset($this->container[self::METHOD_PUT][self::TYPE_RESPONSE]);
	}

	public function getPutResponse()
	{
		return $this->container[self::METHOD_PUT][self::TYPE_RESPONSE];
	}

	public function setDelete(SchemaInterface $requestSchema, SchemaInterface $responseSchema = null)
	{
		$this->container[self::METHOD_DELETE] = array(
			self::TYPE_REQUEST  => $requestSchema, 
			self::TYPE_RESPONSE => $responseSchema
		);
	}

	public function hasDelete()
	{
		return isset($this->container[self::METHOD_DELETE]);
	}

	public function hasDeleteRequest()
	{
		return isset($this->container[self::METHOD_DELETE][self::TYPE_REQUEST]);
	}

	public function getDeleteRequest()
	{
		return $this->container[self::METHOD_DELETE][self::TYPE_REQUEST];
	}

	public function hasDeleteResponse()
	{
		return isset($this->container[self::METHOD_DELETE][self::TYPE_RESPONSE]);
	}

	public function getDeleteResponse()
	{
		return $this->container[self::METHOD_DELETE][self::TYPE_RESPONSE];
	}

	public function getAllowedMethods()
	{
		return array_keys($this->container);
	}
}
