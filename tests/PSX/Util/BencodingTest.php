<?php
/*
 *  $Id: BencodingTest.php 480 2012-05-01 18:13:54Z k42b3.x@googlemail.com $
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
 * PSX_Util_BencodingTest
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   tests
 * @version    $Revision: 480 $
 */
class PSX_Util_BencodingTest extends PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
	}

	protected function tearDown()
	{
	}

	public function testBencodingEncode()
	{
		$this->assertEquals('3:foo', PSX_Util_Bencoding::encode('foo'));
		$this->assertEquals('i6e', PSX_Util_Bencoding::encode(6));
		$this->assertEquals('l3:foo3:bare', PSX_Util_Bencoding::encode(array('foo', 'bar')));
		$this->assertEquals('d3:foo3:bare', PSX_Util_Bencoding::encode(array('foo' => 'bar')));
		$this->assertEquals('d3:fool3:bar4:testee', PSX_Util_Bencoding::encode(array('foo' => array('bar', 'test'))));
	}

	public function testBencodingDecode()
	{
		$this->assertEquals('foo', PSX_Util_Bencoding::decode('3:foo'));
		$this->assertEquals(6, PSX_Util_Bencoding::decode('i6e'));
		$this->assertEquals(array('foo', 'bar'), PSX_Util_Bencoding::decode('l3:foo3:bare'));
		$this->assertEquals(array('foo' => 'bar'), PSX_Util_Bencoding::decode('d3:foo3:bare'));
		$this->assertEquals(array('foo' => array('bar', 'test')), PSX_Util_Bencoding::decode('d3:fool3:bar4:testee'));
	}
}