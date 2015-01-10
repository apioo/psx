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

namespace PSX\ActivityStream\ObjectType;

use PSX\ActivityStream\Object;

/**
 * Task
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Task extends Object
{
	protected $actor;
	protected $by;
	protected $object;
	protected $prerequisites;
	protected $required;
	protected $supersedes;
	protected $verb;

	public function __construct()
	{
		$this->objectType = 'task';
	}

	/**
	 * @param PSX\ActivityStream\ObjectFactory $actor
	 */
	public function setActor($actor)
	{
		$this->actor = $actor;
	}
	
	public function getActor()
	{
		return $this->actor;
	}

	public function setBy($by)
	{
		$this->by = $by;
	}
	
	public function getBy()
	{
		return $this->by;
	}

	/**
	 * @param PSX\ActivityStream\ObjectFactory $object
	 */
	public function setObject($object)
	{
		$this->object = $object;
	}
	
	public function getObject()
	{
		return $this->object;
	}

	/**
	 * @param PSX\ActivityStream\ObjectFactory $prerequisites
	 */
	public function setPrerequisites($prerequisites)
	{
		$this->prerequisites = $prerequisites;
	}
	
	public function getPrerequisites()
	{
		return $this->prerequisites;
	}

	public function setRequired($required)
	{
		$this->required = $required;
	}
	
	public function getRequired()
	{
		return $this->required;
	}

	/**
	 * @param PSX\ActivityStream\ObjectFactory $supersedes
	 */
	public function setSupersedes($supersedes)
	{
		$this->supersedes = $supersedes;
	}
	
	public function getSupersedes()
	{
		return $this->supersedes;
	}

	public function setVerb($verb)
	{
		$this->verb = $verb;
	}
	
	public function getVerb()
	{
		return $this->verb;
	}
}
