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

use PSX\DateTime\DateTime;

/**
 * DateTimeTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class DateTimeTest extends \PHPUnit_Framework_TestCase
{
    public function testDateTime()
    {
        $date = new DateTime('2015-04-25T19:35:20');

        $this->assertEquals(2015, $date->getYear());
        $this->assertEquals(4, $date->getMonth());
        $this->assertEquals(25, $date->getDay());
        $this->assertEquals(19, $date->getHour());
        $this->assertEquals(35, $date->getMinute());
        $this->assertEquals(20, $date->getSecond());
        $this->assertEquals(0, $date->getMicroSecond());
        $this->assertEquals(0, $date->getOffset());
        $this->assertEquals('2015-04-25T19:35:20Z', $date->toString());
    }

    public function testDateTimeMicroSeconds()
    {
        $date = new DateTime('2015-04-25T19:35:20.1234');

        $this->assertEquals(2015, $date->getYear());
        $this->assertEquals(4, $date->getMonth());
        $this->assertEquals(25, $date->getDay());
        $this->assertEquals(19, $date->getHour());
        $this->assertEquals(35, $date->getMinute());
        $this->assertEquals(20, $date->getSecond());
        $this->assertEquals(123400, $date->getMicroSecond());
        $this->assertEquals(0, $date->getOffset());
        $this->assertEquals('2015-04-25T19:35:20.123400Z', $date->toString());
    }

    public function testDateTimeOffset()
    {
        $date = new DateTime('2015-04-25T19:35:20+01:00');

        $this->assertEquals(2015, $date->getYear());
        $this->assertEquals(4, $date->getMonth());
        $this->assertEquals(25, $date->getDay());
        $this->assertEquals(19, $date->getHour());
        $this->assertEquals(35, $date->getMinute());
        $this->assertEquals(20, $date->getSecond());
        $this->assertEquals(0, $date->getMicroSecond());
        $this->assertEquals(3600, $date->getOffset());
        $this->assertEquals('2015-04-25T19:35:20+01:00', $date->toString());
    }

    public function testDateTimeMicroSecondsAndOffset()
    {
        $date = new DateTime('2015-04-25T19:35:20.1234+01:00');

        $this->assertEquals(2015, $date->getYear());
        $this->assertEquals(4, $date->getMonth());
        $this->assertEquals(25, $date->getDay());
        $this->assertEquals(19, $date->getHour());
        $this->assertEquals(35, $date->getMinute());
        $this->assertEquals(20, $date->getSecond());
        $this->assertEquals(123400, $date->getMicroSecond());
        $this->assertEquals(3600, $date->getOffset());
        $this->assertEquals('2015-04-25T19:35:20.123400+01:00', $date->toString());
    }

    /**
     * @dataProvider providerRfc
     */
    public function testRfcExamples($data, $expected)
    {
        $date = new DateTime($data);

        $this->assertEquals($expected, $date->toString());
    }

    public function providerRfc()
    {
        return [
            ['1985-04-12T23:20:50.52Z', '1985-04-12T23:20:50.520000Z'],
            ['1996-12-19T16:39:57-08:00', '1996-12-19T16:39:57-08:00'],
            ['1937-01-01T12:00:27.87+00:20', '1937-01-01T12:00:27.870000+00:20'],
        ];
    }

    public function testConstructorFull()
    {
        $date = new DateTime(2014, 1, 1, 13, 37, 12);

        $this->assertEquals('2014-01-01T13:37:12Z', $date->toString());
    }

    public function testToString()
    {
        $date = new DateTime(2014, 1, 1, 13, 37, 12);

        $this->assertEquals('2014-01-01T13:37:12Z', (string) $date);
    }

    public function testDateTimeNow()
    {
        $date = new DateTime();

        $this->assertEquals(date('Y-m-d\TH:i:s\Z'), $date->toString());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testDateTimeEmpty()
    {
        new DateTime('');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testDateTimeInvalid()
    {
        new DateTime('foo');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testDateTimeInvalidOffset()
    {
        new DateTime('2015-04-25T19:35:20+50:00');
    }

    public function testMysqlDateTimeFormat()
    {
        $date = new DateTime('2015-04-25 19:35:20');

        $this->assertEquals('2015-04-25T19:35:20Z', $date->toString());
    }

    public function testFromDateTime()
    {
        $date = DateTime::fromDateTime(new \DateTime('2015-04-25T19:35:20'));

        $this->assertEquals('2015-04-25T19:35:20Z', $date->toString());
    }

    public function testGetOffsetBySeconds()
    {
        $this->assertEquals('+00:00', DateTime::getOffsetBySeconds(0));
        $this->assertEquals('+01:00', DateTime::getOffsetBySeconds(3600));
        $this->assertEquals('-01:00', DateTime::getOffsetBySeconds(-3600));
        $this->assertEquals('+01:30', DateTime::getOffsetBySeconds(5400));
        $this->assertEquals('-01:30', DateTime::getOffsetBySeconds(-5400));
        $this->assertEquals('+24:00', DateTime::getOffsetBySeconds(86400));
        $this->assertEquals('-24:00', DateTime::getOffsetBySeconds(-86400));
    }

    public function testGetSecondsFromOffset()
    {
        $this->assertEquals(0, DateTime::getSecondsFromOffset('+', 0, 0));
        $this->assertEquals(3600, DateTime::getSecondsFromOffset('+', 1, 0));
        $this->assertEquals(-3600, DateTime::getSecondsFromOffset('-', 1, 0));
        $this->assertEquals(5400, DateTime::getSecondsFromOffset('+', 1, 30));
        $this->assertEquals(-5400, DateTime::getSecondsFromOffset('-', 1, 30));
        $this->assertEquals(86400, DateTime::getSecondsFromOffset('+', 24, 0));
        $this->assertEquals(-86400, DateTime::getSecondsFromOffset('-', 24, 0));
    }
}
