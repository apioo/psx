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

namespace PSX\Html\Filter;

/**
 * Element
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Element
{
	private $name;
	private $attributes;
	private $values;

	public function __construct($name, array $attributes = array(), $values = array())
	{
		$this->setName($name);
		$this->setAttributes($attributes);
		$this->setValues($values);
	}

	public function setName($name)
	{
		$this->name = $name;
	}

	public function setAttributes(array $attributes)
	{
		$this->attributes = array();

		foreach($attributes as $name => $filter)
		{
			$this->addAttribute($name, $filter);
		}
	}

	/**
	 * Adds an attribute filter to the element can be PSX\FilterAbstract or an
	 * array<PSX\FilterAbstract> or an string.
	 *
	 * @param string $name
	 * @param PSX\FilterAbstract|string|array $filter
	 * @return void
	 */
	public function addAttribute($name, $filter)
	{
		$this->attributes[$name] = $filter;
	}

	/**
	 * Array of element names wich are allowed in this element
	 *
	 * @param array|constant $values
	 * @return void
	 */
	public function setValues($values)
	{
		$this->values = $values;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getAttributes()
	{
		return $this->attributes;
	}

	public function getValues()
	{
		return $this->values;
	}
}

