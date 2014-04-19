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

namespace PSX\Data\Record\Definition;

/**
 * Property
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Property
{
	protected $name;
	protected $type;
	protected $reference;
	protected $class;
	protected $required;
	protected $default;
	protected $title;
	protected $child;

	/**
	 * @param string $name
	 * @param integer $type
	 * @param PSX\Data\RecordInterface $reference
	 * @param string $class
	 * @param boolean $required
	 * @param string $default
	 * @param string $title
	 * @param PSX\Data\Record\Definition\Property $child
	 */
	public function __construct($name, $type, $reference = null, $class = null, $required = true, $default = null, $title = null, Property $child = null)
	{
		$this->name      = $name;
		$this->type      = $type;
		$this->reference = $reference;
		$this->class     = $class;
		$this->required  = (bool) $required;
		$this->default   = $default;
		$this->title     = $title;
		$this->child     = $child;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getType()
	{
		return $this->type;
	}

	public function getReference()
	{
		return $this->reference;
	}

	public function getClass()
	{
		return $this->class;
	}

	public function isRequired()
	{
		return $this->required;
	}

	public function getDefault()
	{
		return $this->default;
	}

	public function getTitle()
	{
		return $this->title;
	}

	public function getChild()
	{
		return $this->child;
	}
}
