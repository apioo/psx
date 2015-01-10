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

namespace PSX\Swagger;

use PSX\Data\RecordAbstract;

/**
 * Model
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Model extends RecordAbstract
{
	protected $id;
	protected $description;
	protected $required;
	protected $discriminator;
	protected $properties = array();
	protected $subTypes;

	public function __construct($id = null, $description = null, array $required = null)
	{
		$this->id          = $id;
		$this->description = $description;
		$this->required    = $required;
	}

	public function setId($id)
	{
		$this->id = $id;
	}

	public function getId()
	{
		return $this->id;
	}

	public function setDescription($description)
	{
		$this->description = $description;
	}

	public function getDescription()
	{
		return $this->description;
	}

	public function setRequired($required)
	{
		$this->required = $required;
	}

	public function getRequired()
	{
		return $this->required;
	}

	public function setDiscriminator($discriminator)
	{
		$this->discriminator = $discriminator;
	}
	
	public function getDiscriminator()
	{
		return $this->discriminator;
	}

	public function setProperties($properties)
	{
		$this->properties = $properties;
	}
	
	public function getProperties()
	{
		return $this->properties;
	}

	public function addProperty(Property $property)
	{
		$this->properties[$property->getId()] = $property;
	}

	public function setSubTypes($subTypes)
	{
		$this->subTypes = $subTypes;
	}
	
	public function getSubTypes()
	{
		return $this->subTypes;
	}

	public function addSubType($subType)
	{
		$this->subTypes[] = $subType;
	}
}
