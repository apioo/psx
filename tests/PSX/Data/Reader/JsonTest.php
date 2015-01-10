<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
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

/**
 * JsonTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class JsonTest extends \PHPUnit_Framework_TestCase
{
	public function testRead()
	{
		$body = <<<INPUT
{
	"foo": "bar",
	"bar": ["blub","bla"],
	"test": {"foo": "bar"},
	"item": {
		"foo": {
			"bar": {
				"title":"foo"
			}
		}
	},
	"items": {
		"item": [{
			"title": "foo",
			"text": "bar"
		},{
			"title": "foo",
			"text": "bar"
		}]
	}
}
INPUT;

		$reader  = new Json();
		$message = new Message(array(), $body);
		$json    = $reader->read($message);

		$expect = array(
			'foo' => 'bar', 
			'bar' => array('blub', 'bla'), 
			'test' => array('foo' => 'bar'),
			'item' => array('foo' => array('bar' => array('title' => 'foo'))),
			'items' => array('item' => array(array('title' => 'foo', 'text' => 'bar'), array('title' => 'foo', 'text' => 'bar'))),
		);

		$this->assertEquals(true, is_array($json));
		$this->assertEquals($expect, $json);
	}
}