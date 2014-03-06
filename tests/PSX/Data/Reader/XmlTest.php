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

namespace PSX\Data\Reader;

use PSX\Data\ReaderInterface;
use PSX\Http\Message;
use SimpleXMLElement;

/**
 * XmlTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link	http://phpsx.org
 */
class XmlTest extends \PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
	}

	protected function tearDown()
	{
	}

	public function testRead()
	{
		$body = <<<INPUT
<test>
	<foo>bar</foo>
	<bar>blub</bar>
	<bar>bla</bar>
	<test>
		<foo>bar</foo>
	</test>
	<item>
		<foo>
			<bar>
				<title>foo</title>
			</bar>
		</foo>
	</item>
	<items>
		<item>
			<title>foo</title>
			<text>bar</text>
		</item>
		<item>
			<title>foo</title>
			<text>bar</text>
		</item>
	</items>
</test>
INPUT;

		$reader  = new Xml();
		$message = new Message(array(), $body);
		$xml	 = $reader->read($message);

		$expect = array(
			'foo' => 'bar', 
			'bar' => array('blub', 'bla'), 
			'test' => array('foo' => 'bar'),
			'item' => array('foo' => array('bar' => array('title' => 'foo'))),
			'items' => array('item' => array(array('title' => 'foo', 'text' => 'bar'), array('title' => 'foo', 'text' => 'bar'))),
		);

		$this->assertEquals(true, is_array($xml));
		$this->assertEquals($expect, $xml);
	}
}