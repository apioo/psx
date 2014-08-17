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
 * ApiDocumentation
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ApiDocumentation
{
	const METHOD_GET    = 0x0;
	const METHOD_POST   = 0x1;
	const METHOD_PUT    = 0x2;
	const METHOD_DELETE = 0x3;

	protected $container = array();

	public function setGet(SchemaInterface $responseSchema)
	{
		$this->container[self::METHOD_GET] = [$responseSchema];
	}

	public function setPost(SchemaInterface $requestSchema, SchemaInterface $responseSchema = null)
	{
		$this->container[self::METHOD_POST] = [$requestSchema, $responseSchema];
	}

	public function setPut(SchemaInterface $requestSchema, SchemaInterface $responseSchema = null)
	{
		$this->container[self::METHOD_PUT] = [$requestSchema, $responseSchema];
	}

	public function setDelete(SchemaInterface $requestSchema, SchemaInterface $responseSchema = null)
	{
		$this->container[self::METHOD_DELETE] = [$requestSchema, $responseSchema];
	}

	public function get($method)
	{
		return isset($this->container[$method]) ? $this->container[$method] : null;
	}
}
