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

namespace PSX\Data\Schema;

use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use RuntimeException;

/**
 * PropertyAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class PropertyAbstract implements PropertyInterface
{
	protected $name;
	protected $description;
	protected $required;
	protected $reference;

	public function __construct($name)
	{
		$this->name = $name;
	}

	/**
	 * @param string $name
	 * @return $this
	 */
	public function setName($name)
	{
		$this->name = $name;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param string $description
	 * @return $this
	 */
	public function setDescription($description)
	{
		$this->description = $description;

		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * @param boolean $required
	 * @return $this
	 */
	public function setRequired($required)
	{
		$this->required = $required;

		return $this;
	}

	/**
	 * @return boolean
	 */
	public function isRequired()
	{
		return $this->required;
	}

	/**
	 * @param string $required
	 * @return $this
	 */
	public function setReference($reference)
	{
		$this->reference = $reference;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getReference()
	{
		return $this->reference;
	}

	/**
	 * @return string
	 */
	public function getId()
	{
		return md5(
			get_class($this) .
			$this->required
		);
	}

	/**
	 * @return string
	 */
	public function getTypeName()
	{
		$class    = explode('\\', get_class($this));
		$typeName = substr(end($class), 0, -4);

		return lcfirst($typeName);
	}

	/**
	 * @return boolean
	 */
	public function validate($data, $path = '/')
	{
		if($this->required && $data === null)
		{
			throw new ValidationException($path . ' is required');
		}
		else if($data === null)
		{
			return true;
		}
	}

	public function assimilate($data, $path = '/')
	{
		if($this->required && $data === null)
		{
			throw new RuntimeException('Property ' . $path . ' is required');
		}
	}
}
