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

namespace PSX\Data\Reader;

use PSX\Data\ReaderInterface;
use PSX\Http\Message;

/**
 * JsonTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
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

		$expect = new \stdClass();
		$expect->foo = 'bar';
		$expect->bar = ['blub', 'bla'];
		$expect->test = new \stdClass();
		$expect->test->foo = 'bar';
		$expect->item = new \stdClass();
		$expect->item->foo = new \stdClass();
		$expect->item->foo->bar = new \stdClass();
		$expect->item->foo->bar->title = 'foo';
		$expect->items = new \stdClass();
		$expect->items->item = [];
		$expect->items->item[0] = new \stdClass();
		$expect->items->item[0]->title = 'foo';
		$expect->items->item[0]->text = 'bar';
		$expect->items->item[1] = new \stdClass();
		$expect->items->item[1]->title = 'foo';
		$expect->items->item[1]->text = 'bar';

		$this->assertInstanceOf('stdClass', $json);
		$this->assertEquals($expect, $json);
	}
}