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
 * Base64Test
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Base64Test extends \PHPUnit_Framework_TestCase
{
	public function testEncode()
	{
		$this->assertEquals('', Base64::encode(''));
		$this->assertEquals('Zg==', Base64::encode('f'));
		$this->assertEquals('Zm8=', Base64::encode('fo'));
		$this->assertEquals('Zm9v', Base64::encode('foo'));
		$this->assertEquals('Zm9vYg==', Base64::encode('foob'));
		$this->assertEquals('Zm9vYmE=', Base64::encode('fooba'));
		$this->assertEquals('Zm9vYmFy', Base64::encode('foobar'));

		for($i = 0; $i < 16; $i++)
		{
			$data = hash('md5', $i, true);

			$this->assertEquals(base64_encode($data), Base64::encode($data));
		}
	}

	public function testDecode()
	{
		$this->assertEquals('f', Base64::decode('Zg=='));
		$this->assertEquals('fo', Base64::decode('Zm8='));
		$this->assertEquals('foo', Base64::decode('Zm9v'));
		$this->assertEquals('foob', Base64::decode('Zm9vYg=='));
		$this->assertEquals('fooba', Base64::decode('Zm9vYmE='));
		$this->assertEquals('foobar', Base64::decode('Zm9vYmFy'));
	}
}
