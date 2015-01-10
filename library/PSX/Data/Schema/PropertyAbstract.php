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
 * PropertyAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
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
	 * @return boolean
	 */
	public function validate($data)
	{
		if($this->required && $data === null)
		{
			throw new ValidationException($this->getName() . ' is required');
		}
		else if($data === null)
		{
			return true;
		}
	}
}
