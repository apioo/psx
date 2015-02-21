<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
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

/**
 * Builder
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
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
