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

namespace PSX;

/**
 * JsonTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class JsonTest extends \PHPUnit_Framework_TestCase
{
	public function testJsonEncode()
	{
		$val = array('foo' => 'bar');

		$this->assertEquals('{"foo":"bar"}', Json::encode($val));
	}

	public function testJsonDecode()
	{
		$val = '{"foo":"bar"}';

		$this->assertEquals(array('foo' => 'bar'), Json::decode($val));
	}

	/**
	 * @expectedException Exception
	 */
	public function testJsonDecodeMalformed()
	{
		$val = '{"foo":"bar"';

		Json::decode($val);
	}

	/**
	 * @expectedException Exception
	 */
	public function testJsonDecodeControlCharacter()
	{
		$val = '{"foo' . "\x02" . '":"bar"}';

		Json::decode($val);
	}
}
