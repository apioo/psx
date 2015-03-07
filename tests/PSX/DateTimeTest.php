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

namespace PSX;

use DateInterval;

/**
 * DateTimeTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class DateTimeTest extends \PHPUnit_Framework_TestCase
{
	public function testConvertIntervalToSeconds()
	{
		$this->assertEquals(1, DateTime::convertIntervalToSeconds(new DateInterval('PT1S')));
		$this->assertEquals(60, DateTime::convertIntervalToSeconds(new DateInterval('PT60S')));
		$this->assertEquals(60, DateTime::convertIntervalToSeconds(new DateInterval('PT1M')));
		$this->assertEquals(3600, DateTime::convertIntervalToSeconds(new DateInterval('PT60M')));
		$this->assertEquals(3600, DateTime::convertIntervalToSeconds(new DateInterval('PT1H')));
		$this->assertEquals(86400, DateTime::convertIntervalToSeconds(new DateInterval('PT24H')));
		$this->assertEquals(86400, DateTime::convertIntervalToSeconds(new DateInterval('P1D')));
		$this->assertEquals(2592000, DateTime::convertIntervalToSeconds(new DateInterval('P30D')));
		$this->assertEquals(2592000, DateTime::convertIntervalToSeconds(new DateInterval('P1M')));
		$this->assertEquals(31104000, DateTime::convertIntervalToSeconds(new DateInterval('P12M')));
		$this->assertEquals(31536000, DateTime::convertIntervalToSeconds(new DateInterval('P1Y')));
	}
}

