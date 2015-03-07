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

namespace PSX\Api\ResourceListing;

use PSX\Api\DocumentationInterface;

/**
 * Resource
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Resource
{
	protected $name;
	protected $methods;
	protected $path;
	protected $source;
	protected $documentation;

	public function __construct($name, array $methods, $path, $source, DocumentationInterface $documentation)
	{
		$this->name          = $name;
		$this->methods       = $methods;
		$this->path          = $path;
		$this->source        = $source;
		$this->documentation = $documentation;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getMethods()
	{
		return $this->methods;
	}

	public function getPath()
	{
		return $this->path;
	}

	public function getSource()
	{
		return $this->source;
	}

	public function getDocumentation()
	{
		return $this->documentation;
	}
}

