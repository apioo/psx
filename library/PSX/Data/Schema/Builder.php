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

/**
 * Builder
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Builder
{
	protected $property;

	public function __construct($name)
	{
		$this->property = new Property\ComplexType($name);
	}

	/**
	 * @param string $description
	 * @return $this
	 */
	public function setDescription($description)
	{
		$this->property->setDescription($description);

		return $this;
	}

	/**
	 * @param boolean $required
	 * @return $this
	 */
	public function setRequired($required)
	{
		$this->property->setRequired($required);

		return $this;
	}

	/**
	 * @param string $reference
	 * @return $this
	 */
	public function setReference($reference)
	{
		$this->property->setReference($reference);

		return $this;
	}

	/**
	 * @param PSX\Data\Schema\PropertyInteface $property
	 * @return $this
	 */
	public function add(PropertyInterface $property)
	{
		$this->property->add($property);

		return $this;
	}

	/**
	 * @param string $name
	 * @return PSX\Data\Schema\Property\ArrayType
	 */
	public function arrayType($name)
	{
		if($name instanceof Property\ArrayType)
		{
			$this->add($property = $name);
		}
		else
		{
			$this->add($property = new Property\ArrayType($name));
		}

		return $property;
	}

	/**
	 * @param string $name
	 * @return PSX\Data\Schema\Property\Boolean
	 */
	public function boolean($name)
	{
		$this->add($property = new Property\Boolean($name));

		return $property;
	}

	/**
	 * @param string $name
	 * @return PSX\Data\Schema\Property\ComplexType
	 */
	public function complexType($name, Property\ComplexType $template = null)
	{
		if($template === null)
		{
			if($name instanceof Property\ComplexType)
			{
				$this->add($property = $name);
			}
			else
			{
				$this->add($property = new Property\ComplexType($name));
			}
		}
		else
		{
			$property = clone $template;
			$property->setName($name);

			$this->add($property);
		}

		return $property;
	}

	/**
	 * @param string $name
	 * @return PSX\Data\Schema\Property\Date
	 */
	public function date($name)
	{
		$this->add($property = new Property\Date($name));

		return $property;
	}

	/**
	 * @param string $name
	 * @return PSX\Data\Schema\Property\DateTime
	 */
	public function dateTime($name)
	{
		$this->add($property = new Property\DateTime($name));

		return $property;
	}

	/**
	 * @param string $name
	 * @return PSX\Data\Schema\Property\Duration
	 */
	public function duration($name)
	{
		$this->add($property = new Property\Duration($name));

		return $property;
	}

	/**
	 * @param string $name
	 * @return PSX\Data\Schema\Property\Float
	 */
	public function float($name)
	{
		$this->add($property = new Property\Float($name));

		return $property;
	}

	/**
	 * @param string $name
	 * @return PSX\Data\Schema\Property\Integer
	 */
	public function integer($name)
	{
		$this->add($property = new Property\Integer($name));

		return $property;
	}

	/**
	 * @param string $name
	 * @return PSX\Data\Schema\Property\String
	 */
	public function string($name)
	{
		$this->add($property = new Property\String($name));

		return $property;
	}

	/**
	 * @param string $name
	 * @return PSX\Data\Schema\Property\Time
	 */
	public function time($name)
	{
		$this->add($property = new Property\Time($name));

		return $property;
	}

	/**
	 * @return PSX\Data\Schema\Property\ComplexType
	 */
	public function getProperty()
	{
		return $this->property;
	}
}
