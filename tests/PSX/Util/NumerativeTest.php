<?php
/*
 *  $Id: NumerativeTest.php 480 2012-05-01 18:13:54Z k42b3.x@googlemail.com $
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

/**
 * PSX_Util_NumerativeTest
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   tests
 * @version    $Revision: 480 $
 */
class PSX_Util_NumerativeTest extends PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
	}

	protected function tearDown()
	{
	}

	public function testNumerativeBin()
	{
		$this->assertEquals(20, PSX_Util_Numerative::bin2oct(10000));
		$this->assertEquals(16, PSX_Util_Numerative::bin2dez(10000));
		$this->assertEquals(10, PSX_Util_Numerative::bin2hex(10000));
	}

	public function testNumerativeOct()
	{
		$this->assertEquals(10000, PSX_Util_Numerative::oct2bin(20));
		$this->assertEquals(16, PSX_Util_Numerative::oct2dez(20));
		$this->assertEquals(10, PSX_Util_Numerative::oct2hex(20));
	}

	public function testNumerativeDez()
	{
		$this->assertEquals(10000, PSX_Util_Numerative::dez2bin(16));
		$this->assertEquals(20, PSX_Util_Numerative::dez2oct(16));
		$this->assertEquals(10, PSX_Util_Numerative::dez2hex(16));
	}

	public function testNumerativeHex()
	{
		$this->assertEquals(10000, PSX_Util_Numerative::hex2bin(10));
		$this->assertEquals(20, PSX_Util_Numerative::hex2oct(10));
		$this->assertEquals(16, PSX_Util_Numerative::hex2dez(10));
	}
}