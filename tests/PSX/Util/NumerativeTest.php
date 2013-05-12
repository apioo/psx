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

namespace PSX\Util;

/**
 * NumerativeTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class NumerativeTest extends \PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
	}

	protected function tearDown()
	{
	}

	public function testNumerativeBin()
	{
		$this->assertEquals(20, Numerative::bin2oct(10000));
		$this->assertEquals(16, Numerative::bin2dez(10000));
		$this->assertEquals(10, Numerative::bin2hex(10000));
	}

	public function testNumerativeOct()
	{
		$this->assertEquals(10000, Numerative::oct2bin(20));
		$this->assertEquals(16, Numerative::oct2dez(20));
		$this->assertEquals(10, Numerative::oct2hex(20));
	}

	public function testNumerativeDez()
	{
		$this->assertEquals(10000, Numerative::dez2bin(16));
		$this->assertEquals(20, Numerative::dez2oct(16));
		$this->assertEquals(10, Numerative::dez2hex(16));
	}

	public function testNumerativeHex()
	{
		$this->assertEquals(10000, Numerative::hex2bin(10));
		$this->assertEquals(20, Numerative::hex2oct(10));
		$this->assertEquals(16, Numerative::hex2dez(10));
	}
}