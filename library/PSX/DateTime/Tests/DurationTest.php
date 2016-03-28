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

use DateInterval;
use PSX\DateTime\Duration;

/**
 * DurationTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class DurationTest extends \PHPUnit_Framework_TestCase
{
    public function testDuration()
    {
        $duration = new Duration('P2015Y4M25DT19H35M20S');

        $this->assertEquals(2015, $duration->getYear());
        $this->assertEquals(4, $duration->getMonth());
        $this->assertEquals(25, $duration->getDay());
        $this->assertEquals(19, $duration->getHour());
        $this->assertEquals(35, $duration->getMinute());
        $this->assertEquals(20, $duration->getSecond());
        $this->assertEquals('P2015Y4M25DT19H35M20S', $duration->toString());
    }

    public function testDurationYear()
    {
        $duration = new Duration('P2015Y');

        $this->assertEquals(2015, $duration->getYear());
        $this->assertEquals(0, $duration->getMonth());
        $this->assertEquals(0, $duration->getDay());
        $this->assertEquals(0, $duration->getHour());
        $this->assertEquals(0, $duration->getMinute());
        $this->assertEquals(0, $duration->getSecond());
        $this->assertEquals('P2015Y', $duration->toString());
    }

    public function testDurationMonth()
    {
        $duration = new Duration('P4M');

        $this->assertEquals(0, $duration->getYear());
        $this->assertEquals(4, $duration->getMonth());
        $this->assertEquals(0, $duration->getDay());
        $this->assertEquals(0, $duration->getHour());
        $this->assertEquals(0, $duration->getMinute());
        $this->assertEquals(0, $duration->getSecond());
        $this->assertEquals('P4M', $duration->toString());
    }

    public function testDurationDay()
    {
        $duration = new Duration('P25D');

        $this->assertEquals(0, $duration->getYear());
        $this->assertEquals(0, $duration->getMonth());
        $this->assertEquals(25, $duration->getDay());
        $this->assertEquals(0, $duration->getHour());
        $this->assertEquals(0, $duration->getMinute());
        $this->assertEquals(0, $duration->getSecond());
        $this->assertEquals('P25D', $duration->toString());
    }

    public function testDurationHour()
    {
        $duration = new Duration('PT19H');

        $this->assertEquals(0, $duration->getYear());
        $this->assertEquals(0, $duration->getMonth());
        $this->assertEquals(0, $duration->getDay());
        $this->assertEquals(19, $duration->getHour());
        $this->assertEquals(0, $duration->getMinute());
        $this->assertEquals(0, $duration->getSecond());
        $this->assertEquals('PT19H', $duration->toString());
    }

    public function testDurationMinute()
    {
        $duration = new Duration('PT35M');

        $this->assertEquals(0, $duration->getYear());
        $this->assertEquals(0, $duration->getMonth());
        $this->assertEquals(0, $duration->getDay());
        $this->assertEquals(0, $duration->getHour());
        $this->assertEquals(35, $duration->getMinute());
        $this->assertEquals(0, $duration->getSecond());
        $this->assertEquals('PT35M', $duration->toString());
    }

    public function testDurationSecond()
    {
        $duration = new Duration('PT20S');

        $this->assertEquals(0, $duration->getYear());
        $this->assertEquals(0, $duration->getMonth());
        $this->assertEquals(0, $duration->getDay());
        $this->assertEquals(0, $duration->getHour());
        $this->assertEquals(0, $duration->getMinute());
        $this->assertEquals(20, $duration->getSecond());
        $this->assertEquals('PT20S', $duration->toString());
    }

    public function testConstructorFull()
    {
        $duration = new Duration(1, 1, 1, 1, 1, 1);

        $this->assertEquals('P1Y1M1DT1H1M1S', $duration->toString());
        $this->assertEquals('1.1.1.1.1.1', $duration->format('%y.%m.%d.%h.%i.%s'));
    }

    public function testToString()
    {
        $duration = new Duration(1, 1, 1, 1, 1, 1);

        $this->assertEquals('P1Y1M1DT1H1M1S', (string) $duration);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testDurationEmpty()
    {
        new Duration('');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testDurationInvalid()
    {
        new Duration('foo');
    }

    public function testGetSecondsFromInterval()
    {
        $this->assertEquals(1, Duration::getSecondsFromInterval(new DateInterval('PT1S')));
        $this->assertEquals(60, Duration::getSecondsFromInterval(new DateInterval('PT60S')));
        $this->assertEquals(60, Duration::getSecondsFromInterval(new DateInterval('PT1M')));
        $this->assertEquals(3600, Duration::getSecondsFromInterval(new DateInterval('PT60M')));
        $this->assertEquals(3600, Duration::getSecondsFromInterval(new DateInterval('PT1H')));
        $this->assertEquals(86400, Duration::getSecondsFromInterval(new DateInterval('PT24H')));
        $this->assertEquals(86400, Duration::getSecondsFromInterval(new DateInterval('P1D')));
        $this->assertEquals(2592000, Duration::getSecondsFromInterval(new DateInterval('P30D')));
        $this->assertEquals(2592000, Duration::getSecondsFromInterval(new DateInterval('P1M')));
        $this->assertEquals(31104000, Duration::getSecondsFromInterval(new DateInterval('P12M')));
        $this->assertEquals(31536000, Duration::getSecondsFromInterval(new DateInterval('P1Y')));
    }

    public function testFromDateInterval()
    {
        $this->assertEquals('P2015Y4M25DT19H35M20S', Duration::fromDateInterval(new DateInterval('P2015Y4M25DT19H35M20S'))->toString());
        $this->assertEquals('PT60S', Duration::fromDateInterval(new DateInterval('PT60S'))->toString());
    }
}
