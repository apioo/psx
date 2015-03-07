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
 * BencodingTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
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