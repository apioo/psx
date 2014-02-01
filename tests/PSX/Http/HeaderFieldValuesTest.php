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

namespace PSX\Http;

use PSX\Http;
use PSX\Http\Stream\StringStream;
use PSX\Url;

/**
 * HeaderFieldValuesTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class HeaderFieldValuesTest extends \PHPUnit_Framework_TestCase
{
	public function testAppend()
	{
		$value = new HeaderFieldValues('foo');

		$this->assertEquals('foo', (string) $value);
		$this->assertEquals(array('foo'), $value->getValue());
		$this->assertEquals(1, count($value));
		$this->assertEquals('foo', $value[0]);
		$this->assertEquals(null, $value[1]);

		$value->append('bar');

		$this->assertEquals('foo, bar', (string) $value);
		$this->assertEquals(array('foo', 'bar'), $value->getValue());

		$value->append(new HeaderFieldValues('bar'));

		$this->assertEquals('foo, bar, bar', (string) $value);
		$this->assertEquals(array('foo', 'bar', 'bar'), $value->getValue());

		$value->append(new HeaderFieldValues(array('foo', 'bar')));

		$this->assertEquals('foo, bar, bar, foo, bar', (string) $value);
		$this->assertEquals(array('foo', 'bar', 'bar', 'foo', 'bar'), $value->getValue());
	}

	public function testCountable()
	{
		$value = new HeaderFieldValues(array('foo', 'bar'));

		$this->assertEquals(2, count($value));

		$value = new HeaderFieldValues('foo');

		$this->assertEquals(1, count($value));
	}

	public function testArrayAccess()
	{
		$value = new HeaderFieldValues(array('foo', 'bar'));

		$this->assertEquals('foo', $value[0]);
		$this->assertEquals('bar', $value[1]);
		$this->assertEquals(null, $value[2]);

		unset($value[1]);

		$this->assertEquals(1, count($value));

		$this->assertEquals('foo', $value[0]);
		$this->assertEquals(null, $value[1]);
		$this->assertEquals(null, $value[2]);

		// we can only modify values which are available we cant create new
		// entries through array access
		$value[0] = 'test';
		$value[1] = 'test';

		$this->assertEquals('test', $value[0]);
		$this->assertEquals(null, $value[1]);

		$this->assertTrue(isset($value[0]));
		$this->assertFalse(isset($value[1]));
	}

	public function testTraversable()
	{
		$data  = array('foo', 'bar');
		$value = new HeaderFieldValues($data);
		$i     = 0;

		foreach($value as $key => $val)
		{
			$this->assertEquals($i, $key);
			$this->assertEquals($data[$i], $val);

			$i++;
		}
	}

	public function testToString()
	{
		$value = new HeaderFieldValues(array('foo', 'bar'));

		$this->assertEquals('foo, bar', $value);

		$value = new HeaderFieldValues('foo');

		$this->assertEquals('foo', $value);
	}

	/**
	 * If we give an array to the constructor all values of the array are casted
	 * to strings
	 */
	public function testConstructorArrayToString()
	{
		$value = new HeaderFieldValues(array('foo', new Url('http://localhost.com')));

		$this->assertEquals('foo', $value[0]);
		$this->assertEquals('http://localhost.com', $value[1]);
	}
}
