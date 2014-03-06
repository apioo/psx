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
 * Event
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Event extends Object
{
	protected $attendedBy;
	protected $attending;
	protected $endTime;
	protected $invited;
	protected $maybeAttending;
	protected $notAttendedBy;
	protected $notAttending;
	protected $startTime;

	public function __construct()
	{
		$this->objectType = 'event';
	}

	/**
	 * @param PSX\ActivityStream\ObjectType\Collection $attendedBy
	 */
	public function setAttendedBy(Collection $attendedBy)
	{
		$this->attendedBy = $attendedBy;
	}
	
	public function getAttendedBy()
	{
		return $this->attendedBy;
	}

	/**
	 * @param PSX\ActivityStream\ObjectType\Collection $attending
	 */
	public function setAttending(Collection $attending)
	{
		$this->attending = $attending;
	}
	
	public function getAttending()
	{
		return $this->attending;
	}

	/**
	 * @param DateTime $endTime
	 */
	public function setEndTime(DateTime $endTime)
	{
		$this->endTime = $endTime;
	}
	
	public function getEndTime()
	{
		return $this->endTime;
	}

	/**
	 * @param PSX\ActivityStream\ObjectType\Collection $invited
	 */
 	public function setInvited(Collection $invited)
	{
		$this->invited = $invited;
	}
	
	public function getInvited()
	{
		return $this->invited;
	}

	/**
	 * @param PSX\ActivityStream\ObjectType\Collection $maybeAttending
	 */
	public function setMaybeAttending(Collection $maybeAttending)
	{
		$this->maybeAttending = $maybeAttending;
	}
	
	public function getMaybeAttending()
	{
		return $this->maybeAttending;
	}

	/**
	 * @param PSX\ActivityStream\ObjectType\Collection $notAttendedBy
	 */
	public function setNotAttendedBy(Collection $notAttendedBy)
	{
		$this->notAttendedBy = $notAttendedBy;
	}
	
	public function getNotAttendedBy()
	{
		return $this->notAttendedBy;
	}

	/**
	 * @param PSX\ActivityStream\ObjectType\Collection $notAttending
	 */
	public function setNotAttending(Collection $notAttending)
	{
		$this->notAttending = $notAttending;
	}
	
	public function getNotAttending()
	{
		return $this->notAttending;
	}

	/**
	 * @param DateTime $startTime
	 */
	public function setStartTime(DateTime $startTime)
	{
		$this->startTime = $startTime;
	}
	
	public function getStartTime()
	{
		return $this->startTime;
	}
}
