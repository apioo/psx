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
use PSX\ActivityStream\Object;

/**
 * Event
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
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
     * @param \PSX\ActivityStream\ObjectType\Collection $attendedBy
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
     * @param \PSX\ActivityStream\ObjectType\Collection $attending
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
     * @param \PSX\DateTime $endTime
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
     * @param \PSX\ActivityStream\ObjectType\Collection $invited
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
     * @param \PSX\ActivityStream\ObjectType\Collection $maybeAttending
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
     * @param \PSX\ActivityStream\ObjectType\Collection $notAttendedBy
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
     * @param \PSX\ActivityStream\ObjectType\Collection $notAttending
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
     * @param \PSX\DateTime $startTime
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
