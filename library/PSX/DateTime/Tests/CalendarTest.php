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

namespace PSX\DateTime\Tests;

use PSX\DateTime\Calendar;

/**
 * CalendarTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class CalendarTest extends \PHPUnit_Framework_TestCase
{
    public function testGetter()
    {
        $calendar = new Calendar(new \DateTime('2014-01-01 12:14:00'), new \DateTimeZone('UTC'));

        $this->assertEquals('2014-01-01 00:00:00', $calendar->getDate()->format('Y-m-d H:i:s'));

        $calendar->setDate(new \DateTime('2014-01-04 12:14:00'));

        $this->assertEquals('2014-01-04 00:00:00', $calendar->getDate()->format('Y-m-d H:i:s'));

        $tz = new \DateTimeZone('Europe/Berlin');

        $calendar->setTimezone($tz);

        $this->assertEquals($tz, $calendar->getTimezone());
        $this->assertEquals('01', $calendar->getWeekNumber());
        $this->assertEquals(4, $calendar->getDay());
        $this->assertEquals(1, $calendar->getMonth());
        $this->assertEquals(2014, $calendar->getYear());
        $this->assertEquals('January', $calendar->getMonthName());
    }

    public function testGetDays()
    {
        if (!function_exists('cal_days_in_month')) {
            $this->markTestSkipped('cal_days_in_month function not available');
        }

        $calendar = new Calendar(new \DateTime('2014-01-01 12:14:00'), new \DateTimeZone('UTC'));

        $this->assertEquals(31, $calendar->getDays());
        $this->assertEquals(31, count($calendar));
    }

    public function testGetEasterDate()
    {
        if (!function_exists('easter_days')) {
            $this->markTestSkipped('easter_days function not available');
        }

        $calendar = new Calendar(new \DateTime('2014-01-04 12:14:00'));

        $this->assertEquals('2014-04-20', $calendar->getEasterDate()->format('Y-m-d'));
    }

    public function testDateNavigation()
    {
        $calendar = new Calendar(new \DateTime('2014-01-04 12:14:00'));

        $this->assertEquals('2014-01-04', $calendar->getDate()->format('Y-m-d'));

        $calendar->nextDay();

        $this->assertEquals('2014-01-05', $calendar->getDate()->format('Y-m-d'));

        $calendar->prevDay();

        $this->assertEquals('2014-01-04', $calendar->getDate()->format('Y-m-d'));

        $calendar->nextMonth();

        $this->assertEquals('2014-02-04', $calendar->getDate()->format('Y-m-d'));

        $calendar->prevMonth();

        $this->assertEquals('2014-01-04', $calendar->getDate()->format('Y-m-d'));

        $calendar->nextYear();

        $this->assertEquals('2015-01-04', $calendar->getDate()->format('Y-m-d'));

        $calendar->prevYear();

        $this->assertEquals('2014-01-04', $calendar->getDate()->format('Y-m-d'));

        $calendar->add(new \DateInterval('P1M2D'));

        $this->assertEquals('2014-02-06', $calendar->getDate()->format('Y-m-d'));

        $calendar->sub(new \DateInterval('P1M2D'));

        $this->assertEquals('2014-01-04', $calendar->getDate()->format('Y-m-d'));
    }

    public function testDateIterator()
    {
        $calendar = new Calendar(new \DateTime('2014-01-04 12:14:00'));
        $i = 0;

        foreach ($calendar as $key => $day) {
            $i++;
            $this->assertEquals($i, $day->format('j'));
            $this->assertEquals($i, $key);
        }
    }
}
