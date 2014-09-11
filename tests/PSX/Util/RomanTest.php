<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2014 Christoph Kappestein <k42b3.x@gmail.com>
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
 * RomanTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class RomanTest extends \PHPUnit_Framework_TestCase
{
	public function testEncode()
	{
		$this->assertEquals('I', Roman::encode(1));
		$this->assertEquals('XVI', Roman::encode(16));
		$this->assertEquals('MCMXLXLVI', Roman::encode(1986));
	}

	public function testDecode()
	{
		$this->assertEquals(1, Roman::decode('I'));
		$this->assertEquals(16, Roman::decode('XVI'));
		$this->assertEquals(1986, Roman::decode('MCMXLXLVI'));
	}

	/**
	 * @expectedException UnexpectedValueException
	 */
	public function testEncodeZero()
	{
		$this->assertEquals('', Roman::encode(0));
	}

	/**
	 * @expectedException UnexpectedValueException
	 */
	public function testEncodeNegativeNumber()
	{
		Roman::encode(-1);
	}

	/**
	 * @expectedException UnexpectedValueException
	 */
	public function testDecodeInvalidInput()
	{
		Roman::decode('foo');
	}
}