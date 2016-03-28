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

namespace PSX\DateTime;

use Countable;
use DateInterval;
use DateTimeZone;
use Iterator;

/**
 * Calendar
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Calendar implements Iterator, Countable
{
    /**
     * @var \DateTime
     */
    protected $date;

    /**
     * @var \DateTime
     */
    private $itDate;

    public function __construct(\DateTime $date = null, DateTimeZone $timezone = null)
    {
        $this->setDate($date !== null ? $date : new \DateTime());

        if ($timezone !== null) {
            $this->setTimezone($timezone);
        }
    }

    /**
     * Sets the underlying datetime object and removes the timepart of the
     * datetime object
     *
     * @param \DateTime $date
     * @return void
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date->setTime(0, 0, 0);
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setTimezone(DateTimeZone $timezone)
    {
        $this->date->setTimezone($timezone);
    }

    public function getTimezone()
    {
        return $this->date->getTimezone();
    }

    /**
     * Return the days of the current month and year
     *
     * @return integer
     */
    public function getDays()
    {
        return cal_days_in_month(CAL_GREGORIAN, $this->date->format('n'), $this->date->format('Y'));
    }

    /**
     * Returns the easter date for the current year
     *
     * @return \PSX\DateTime\DateTime
     */
    public function getEasterDate()
    {
        $easter = new \DateTime($this->getYear() . '-03-21');
        $days   = easter_days($this->getYear());

        return $easter->add(new DateInterval('P' . $days . 'D'));
    }

    public function getWeekNumber()
    {
        return $this->date->format('W');
    }

    public function getDay()
    {
        return $this->date->format('j');
    }

    public function getMonth()
    {
        return $this->date->format('n');
    }

    public function getYear()
    {
        return $this->date->format('Y');
    }

    public function getMonthName()
    {
        return $this->date->format('F');
    }

    public function add(DateInterval $interval)
    {
        $this->date->add($interval);

        return $this;
    }

    public function sub(DateInterval $interval)
    {
        $this->date->sub($interval);

        return $this;
    }

    public function nextDay()
    {
        return $this->add(new DateInterval('P1D'));
    }

    public function prevDay()
    {
        return $this->sub(new DateInterval('P1D'));
    }

    public function nextMonth()
    {
        return $this->add(new DateInterval('P1M'));
    }

    public function prevMonth()
    {
        return $this->sub(new DateInterval('P1M'));
    }

    public function nextYear()
    {
        return $this->add(new DateInterval('P1Y'));
    }

    public function prevYear()
    {
        return $this->sub(new DateInterval('P1Y'));
    }

    // countable
    public function count()
    {
        return $this->getDays();
    }

    // iterator
    public function current()
    {
        return $this->itDate;
    }

    public function key()
    {
        return $this->itDate->format('j');
    }

    public function next()
    {
        $this->itDate->add(new DateInterval('P1D'))->setTime(0, 0, 0);
    }

    public function rewind()
    {
        $this->itDate = new \DateTime($this->getYear() . '-' . $this->getMonth() . '-01');
    }

    public function valid()
    {
        return $this->date->format('n') == $this->itDate->format('n');
    }
}
