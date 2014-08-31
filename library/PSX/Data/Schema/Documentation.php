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

namespace PSX\Data\Schema;

use PSX\Data\SchemaInterface;

/**
 * Documentation
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Documentation
{
	const METHOD_GET    = 0x0;
	const METHOD_POST   = 0x1;
	const METHOD_PUT    = 0x2;
	const METHOD_DELETE = 0x3;

	const TYPE_REQUEST  = 0x0;
	const TYPE_RESPONSE = 0x1;

	protected $container = array();

	public function setGet(SchemaInterface $responseSchema)
	{
		$this->container[self::METHOD_GET] = array(
			self::TYPE_RESPONSE => $responseSchema
		);
	}

	public function setPost(SchemaInterface $requestSchema, SchemaInterface $responseSchema = null)
	{
		$this->container[self::METHOD_POST] = array(
			self::TYPE_REQUEST  => $requestSchema, 
			self::TYPE_RESPONSE => $responseSchema
		);
	}

	public function setPut(SchemaInterface $requestSchema, SchemaInterface $responseSchema = null)
	{
		$this->container[self::METHOD_PUT] = array(
			self::TYPE_REQUEST  => $requestSchema, 
			self::TYPE_RESPONSE => $responseSchema
		);
	}

	public function setDelete(SchemaInterface $requestSchema, SchemaInterface $responseSchema = null)
	{
		$this->container[self::METHOD_DELETE] = array(
			self::TYPE_REQUEST  => $requestSchema, 
			self::TYPE_RESPONSE => $responseSchema
		);
	}

	public function getRequest($method)
	{
		return isset($this->container[$method][self::TYPE_REQUEST]) ? $this->container[$method][self::TYPE_REQUEST] : null;
	}

	public function hasRequest($method)
	{
		return isset($this->container[$method][self::TYPE_REQUEST]);
	}

	public function getResponse($method)
	{
		return isset($this->container[$method][self::TYPE_RESPONSE]) ? $this->container[$method][self::TYPE_RESPONSE] : null;
	}

	public function hasResponse($method)
	{
		return isset($this->container[$method][self::TYPE_RESPONSE]);
	}

	public function get($method)
	{
		return isset($this->container[$method]) ? $this->container[$method] : null;
	}

	public function has($method)
	{
		return isset($this->container[$method]);
	}
}
