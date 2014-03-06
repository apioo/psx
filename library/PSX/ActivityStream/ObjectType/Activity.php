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

namespace PSX\ActivityStream\ObjectType;

use DateTime;
use PSX\ActivityStream\Object;

/**
 * Activity
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Activity extends Object
{
	protected $verb;
	protected $actor;
	protected $object;
	protected $target;
	protected $result;
	protected $priority;
	protected $to;
	protected $cc;
	protected $bto;
	protected $bcc;

	public function setVerb($verb)
	{
		$this->verb = $verb;
	}

	public function getVerb()
	{
		return $this->verb;
	}

	/**
	 * @param PSX\ActivityStream\LinkBuilder $actor
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
	 * @param PSX\ActivityStream\LinkBuilder $object
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
	 * @param PSX\ActivityStream\LinkBuilder $target
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
	 * @param PSX\ActivityStream\LinkBuilder $result
	 */
	public function setResult($result)
	{
		$this->result = $result;
	}
	
	public function getResult()
	{
		return $this->result;
	}

	public function setPriority($priority)
	{
		$this->priority = $priority;
	}
	
	public function getPriority()
	{
		return $this->priority;
	}

	/**
	 * @param PSX\ActivityStream\LinkBuilder $to
	 */
	public function setTo($to)
	{
		$this->to = $to;
	}
	
	public function getTo()
	{
		return $this->to;
	}

	/**
	 * @param PSX\ActivityStream\LinkBuilder $cc
	 */
	public function setCc($cc)
	{
		$this->cc = $cc;
	}
	
	public function getCc()
	{
		return $this->cc;
	}

	/**
	 * @param PSX\ActivityStream\LinkBuilder $bto
	 */
	public function setBto($bto)
	{
		$this->bto = $bto;
	}
	
	public function getBto()
	{
		return $this->bto;
	}

	/**
	 * @param PSX\ActivityStream\LinkBuilder $bcc
	 */
	public function setBcc($bcc)
	{
		$this->bcc = $bcc;
	}
	
	public function getBcc()
	{
		return $this->bcc;
	}
}
