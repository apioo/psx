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

namespace PSX\Data\Schema\Property;

use PSX\Data\Schema\PropertyInterface;
use PSX\Data\Schema\ValidationException;

/**
 * ComplexType
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ComplexType implements PropertyInterface
{
	protected $name;
	protected $required;
	protected $properties = array();

	public function __construct($name)
	{
		$this->name = $name;
	}

	public function getName()
	{
		return $this->name;
	}

	public function isRequired()
	{
		return $this->required;
	}

	public function setRequired($required)
	{
		$this->required = $required;

		return $this;
	}

	public function add(PropertyInterface $property)
	{
		$this->properties[] = $property;

		return $this;
	}

	public function getChildren()
	{
		return $this->properties;
	}

	public function hasConstraints()
	{
		return true;
	}

	public function validate($data)
	{
		if($data === null)
		{
			return true;
		}

		if(!is_array($data))
		{
			throw new ValidationException($this->getName() . ' must be an array');
		}

		return true;
	}
}
