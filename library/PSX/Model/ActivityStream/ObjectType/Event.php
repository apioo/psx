<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2016 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Model\ActivityStream\ObjectType;

use DateTime;
use PSX\Model\ActivityStream\Collection;
use PSX\Model\ActivityStream\ObjectType;

/**
 * Event
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Event extends ObjectType
{
    /**
     * @Type("\PSX\Model\ActivityStream\Collection")
     */
    protected $attendedBy;

    /**
     * @Type("\PSX\Model\ActivityStream\Collection")
     */
    protected $attending;

    /**
     * @Type("datetime")
     */
    protected $endTime;

    /**
     * @Type("\PSX\Model\ActivityStream\Collection")
     */
    protected $invited;

    /**
     * @Type("\PSX\Model\ActivityStream\Collection")
     */
    protected $maybeAttending;

    /**
     * @Type("\PSX\Model\ActivityStream\Collection")
     */
    protected $notAttendedBy;

    /**
     * @Type("\PSX\Model\ActivityStream\Collection")
     */
    protected $notAttending;

    /**
     * @Type("datetime")
     */
    protected $startTime;

    public function __construct()
    {
        $this->objectType = 'event';
    }

    public function setAttendedBy(Collection $attendedBy)
    {
        $this->attendedBy = $attendedBy;
    }
    
    public function getAttendedBy()
    {
        return $this->attendedBy;
    }

    public function setAttending(Collection $attending)
    {
        $this->attending = $attending;
    }
    
    public function getAttending()
    {
        return $this->attending;
    }

    public function setEndTime(DateTime $endTime)
    {
        $this->endTime = $endTime;
    }
    
    public function getEndTime()
    {
        return $this->endTime;
    }

    public function setInvited(Collection $invited)
    {
        $this->invited = $invited;
    }
    
    public function getInvited()
    {
        return $this->invited;
    }

    public function setMaybeAttending(Collection $maybeAttending)
    {
        $this->maybeAttending = $maybeAttending;
    }
    
    public function getMaybeAttending()
    {
        return $this->maybeAttending;
    }

    public function setNotAttendedBy(Collection $notAttendedBy)
    {
        $this->notAttendedBy = $notAttendedBy;
    }
    
    public function getNotAttendedBy()
    {
        return $this->notAttendedBy;
    }

    public function setNotAttending(Collection $notAttending)
    {
        $this->notAttending = $notAttending;
    }
    
    public function getNotAttending()
    {
        return $this->notAttending;
    }

    public function setStartTime(DateTime $startTime)
    {
        $this->startTime = $startTime;
    }
    
    public function getStartTime()
    {
        return $this->startTime;
    }
}
