<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2013 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\ActivityStream\Type;

use PSX\ActivityStream\Object;
use PSX\Data\RecordInfo;

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

	public function getRecordInfo()
	{
		return new RecordInfo('task', array(
			'actor'         => $this->actor,
			'by'            => $this->by,
			'object'        => $this->object,
			'prerequisites' => $this->prerequisites,
			'required'      => $this->required,
			'supersedes'    => $this->supersedes,
			'verb'          => $this->verb,
		), parent::getRecordInfo());
	}

	/**
	 * @param PSX\ActivityStream\ObjectFactory
	 */
	public function setActor(Object $actor)
	{
		$this->actor = $actor;
	}

	/**
	 * @param string
	 */
	public function setBy($by)
	{
		$this->by = $by;
	}

	/**
	 * @param PSX\ActivityStream\ObjectFactory
	 */
	public function setObject(Object $object)
	{
		$this->object = $object;
	}

	/**
	 * @param array<PSX\ActivityStream\Type\Task>
	 */
	public function setPrerequisites(array $prerequisites)
	{
		$this->prerequisites = $prerequisites;
	}

	/**
	 * @param boolean
	 */
	public function setRequired($required)
	{
		$this->required = $required;
	}

	/**
	 * @param array<PSX\ActivityStream\Type\Task>
	 */
	public function setSupersedes(array $supersedes)
	{
		$this->supersedes = $supersedes;
	}

	/**
	 * @param string
	 */
	public function setVerb($verb)
	{
		$this->verb = $verb;
	}
}

