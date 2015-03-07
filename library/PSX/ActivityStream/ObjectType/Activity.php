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

namespace PSX\ActivityStream\ObjectType;

use DateTime;
use PSX\ActivityStream\AudienceTargetingTrait;
use PSX\ActivityStream\Object;

/**
 * Activity
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Activity extends Object
{
	use AudienceTargetingTrait;

	protected $verb;
	protected $actor;
	protected $object;
	protected $target;
	protected $result;
	protected $instrument;
	protected $participant;
	protected $priority;
	protected $status;

	public function setVerb($verb)
	{
		$this->verb = $verb;
	}

	public function getVerb()
	{
		return $this->verb;
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
	 * @param PSX\ActivityStream\ObjectFactory $target
	 */
	public function setTarget($target)
	{
		$this->target = $target;
	}
	
	public function getTarget()
	{
		return $this->target;
	}

	/**
	 * @param PSX\ActivityStream\ObjectFactory $result
	 */
	public function setResult($result)
	{
		$this->result = $result;
	}
	
	public function getResult()
	{
		return $this->result;
	}


	/**
	 * @param PSX\ActivityStream\ObjectFactory $result
	 */
	public function setInstrument($instrument)
	{
		$this->instrument = $instrument;
	}
	
	public function getInstrument()
	{
		return $this->instrument;
	}


	/**
	 * @param PSX\ActivityStream\ObjectFactory $result
	 */
	public function setParticipant($participant)
	{
		$this->participant = $participant;
	}
	
	public function getParticipant()
	{
		return $this->participant;
	}

	/**
	 * @param float $priority
	 */
	public function setPriority($priority)
	{
		$this->priority = $priority;
	}

	public function getPriority()
	{
		return $this->priority;
	}

	public function setStatus($status)
	{
		$this->status = $status;
	}
	
	public function getStatus()
	{
		return $this->status;
	}
}
