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
 * BencodingTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class BencodingTest extends \PHPUnit_Framework_TestCase
{
	public function testBencodingEncode()
	{
		$this->assertEquals('3:foo', Bencoding::encode('foo'));
		$this->assertEquals('21:foofoofoofoofoofoofoo', Bencoding::encode('foofoofoofoofoofoofoo'));
		$this->assertEquals('i6e', Bencoding::encode(6));
		$this->assertEquals('l3:foo3:bare', Bencoding::encode(array('foo', 'bar')));
		$this->assertEquals('d3:foo3:bare', Bencoding::encode(array('foo' => 'bar')));
		$this->assertEquals('d3:fool3:bar4:testee', Bencoding::encode(array('foo' => array('bar', 'test'))));
	}

	public function testBencodingDecode()
	{
		$this->assertEquals('foo', Bencoding::decode('3:foo'));
		$this->assertEquals(6, Bencoding::decode('i6e'));
		$this->assertEquals(array('foo', 'bar'), Bencoding::decode('l3:foo3:bare'));
		$this->assertEquals(array('foo' => 'bar'), Bencoding::decode('d3:foo3:bare'));
		$this->assertEquals(array('foo' => array('bar', 'test')), Bencoding::decode('d3:fool3:bar4:testee'));
	}

	public function testBencodingDecodeInvalid()
	{
		$this->assertFalse(Bencoding::decode('foobar'));
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testBencodingEncodeInvalidType()
	{
		Bencoding::encode(new \stdClass);
	}
}