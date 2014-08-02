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

/**
 * Builder
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Builder
{
	protected $parent;
	protected $property;

	public function __construct($name, $parent = null)
	{
		$this->parent   = $parent;
		$this->property = new Property\ComplexType($name);
	}

	/**
	 * @return PSX\Data\Schema\Property\ArrayType
	 */
	public function arrayType($name)
	{
		if($name instanceof Property\ArrayType)
		{
			$this->addProperty($property = $name);
		}
		else
		{
			$this->addProperty($property = new Property\ArrayType($name));
		}

		return $property;
	}

	/**
	 * @return PSX\Data\Schema\Property\Boolean
	 */
	public function boolean($name)
	{
		$this->addProperty($property = new Property\Boolean($name));

		return $property;
	}

	/**
	 * @return PSX\Data\Schema\Property\ComplexType
	 */
	public function complexType($name)
	{
		if($name instanceof Property\ComplexType)
		{
			$this->addProperty($property = $name);
		}
		else
		{
			$this->addProperty($property = new Property\ComplexType($name));
		}

		return $property;
	}

	/**
	 * @return PSX\Data\Schema\Property\Date
	 */
	public function date($name)
	{
		$this->addProperty($property = new Property\Date($name));

		return $property;
	}

	/**
	 * @return PSX\Data\Schema\Property\DateTime
	 */
	public function dateTime($name)
	{
		$this->addProperty($property = new Property\DateTime($name));

		return $property;
	}

	/**
	 * @return PSX\Data\Schema\Property\Duration
	 */
	public function duration($name)
	{
		$this->addProperty($property = new Property\Duration($name));

		return $property;
	}

	/**
	 * @return PSX\Data\Schema\Property\Float
	 */
	public function float($name)
	{
		$this->addProperty($property = new Property\Float($name));

		return $property;
	}

	/**
	 * @return PSX\Data\Schema\Property\Integer
	 */
	public function integer($name)
	{
		$this->addProperty($property = new Property\Integer($name));

		return $property;
	}

	/**
	 * @return PSX\Data\Schema\Property\String
	 */
	public function string($name)
	{
		$this->addProperty($property = new Property\String($name));

		return $property;
	}

	/**
	 * @return PSX\Data\Schema\Property\Time
	 */
	public function time($name)
	{
		$this->addProperty($property = new Property\Time($name));

		return $property;
	}

	/**
	 * @param PSX\Data\Schema\PropertyInteface $property
	 */
	public function addProperty(PropertyInterface $property)
	{
		$this->property->add($property);
	}

	/**
	 * @return PSX\Data\Schema\Property\ComplexType
	 */
	public function getProperty()
	{
		return $this->property;
	}
}
