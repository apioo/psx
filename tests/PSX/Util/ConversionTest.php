<?php
/*
 *  $Id: ConversionTest.php 480 2012-05-01 18:13:54Z k42b3.x@googlemail.com $
 *
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2012 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Util;

/**
 * PSX_Util_ConversionTest
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   tests
 * @version    $Revision: 480 $
 */
class ConversionTest extends \PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
	}

	protected function tearDown()
	{
	}

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