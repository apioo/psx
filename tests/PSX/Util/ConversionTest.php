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

namespace PSX\Util;

/**
 * ConversionTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ConversionTest extends \PHPUnit_Framework_TestCase
{
	public function testConversionBi()
	{
		$this->assertEquals('32 byte', Conversion::bi(32));
		$this->assertEquals('1 Kibi', Conversion::bi(1024));
	}

	public function testConversionByte()
	{
		$this->assertEquals('32 byte', Conversion::byte(32));
		$this->assertEquals('1.02 kB', Conversion::byte(1024));
	}

	public function testConversionMeter()
	{
		$this->assertEquals('1 m', Conversion::meter(1));
		$this->assertEquals('1.02 km', Conversion::meter(1024));
	}

	public function testConversionGram()
	{
		$this->assertEquals('1 g', Conversion::gram(1));
		$this->assertEquals('1.02 kg', Conversion::gram(1024));
	}

	public function testConversionSeconds()
	{
		$this->assertEquals('1 s', Conversion::seconds(1));
		$this->assertEquals('6 ms', Conversion::seconds(0.006));
	}
}