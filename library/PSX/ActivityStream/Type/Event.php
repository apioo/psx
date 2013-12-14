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
use PSX\ActivityStream\Collection;
use PSX\Data\RecordInfo;

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

	public function getRecordInfo()
	{
		return new RecordInfo('event', array(
			'attendedBy'     => $this->attendedBy,
			'attending'      => $this->attending,
			'endTime'        => $this->endTime,
			'invited'        => $this->invited,
			'maybeAttending' => $this->maybeAttending,
			'notAttendedBy'  => $this->notAttendedBy,
			'notAttending'   => $this->notAttending,
			'startTime'      => $this->startTime,
		), parent::getRecordInfo());
	}

	/**
	 * @param PSX\ActivityStream\Collection
	 */
	public function setAttendedBy(Collection $attendedBy)
	{
		$this->attendedBy = $attendedBy;
	}

	/**
	 * @param PSX\ActivityStream\Collection
	 */
	public function setAttending(Collection $attending)
	{
		$this->attending = $attending;
	}

	/**
	 * @param string
	 */
	public function setEndTime($endTime)
	{
		$this->endTime = $endTime;
	}

	/**
	 * @param PSX\ActivityStream\Collection
	 */
	public function setInvited(Collection $invited)
	{
		$this->invited = $invited;
	}

	/**
	 * @param PSX\ActivityStream\Collection
	 */
	public function setMaybeAttending(Collection $maybeAttending)
	{
		$this->maybeAttending = $maybeAttending;
	}

	/**
	 * @param PSX\ActivityStream\Collection
	 */
	public function setNotAttendedBy(Collection $notAttendedBy)
	{
		$this->notAttendedBy = $notAttendedBy;
	}

	/**
	 * @param PSX\ActivityStream\Collection
	 */
	public function setNotAttending(Collection $notAttending)
	{
		$this->notAttending = $notAttending;
	}

	/**
	 * @param string
	 */
	public function setStartTime($startTime)
	{
		$this->startTime = $startTime;
	}
}

