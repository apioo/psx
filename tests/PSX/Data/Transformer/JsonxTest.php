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

namespace PSX\Data\Transformer;

use PSX\Http\MediaType;
use PSX\Rss;

/**
 * JsonxTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class JsonxTest extends \PHPUnit_Framework_TestCase
{
	public function testTransform()
	{
		$body = <<<INPUT
<json:object xmlns:json="http://www.ibm.com/xmlns/prod/2009/jsonx">
 <json:number name="id">1</json:number>
 <json:string name="title">foobar</json:string>
 <json:boolean name="active">true</json:boolean>
 <json:boolean name="disabled">false</json:boolean>
 <json:number name="rating">12.45</json:number>
 <json:string name="date">2014-01-01T12:34:47+01:00</json:string>
 <json:string name="href">http://foo.com</json:string>
 <json:null name="empty" />
 <json:object name="person">
  <json:string name="title">Foo</json:string>
 </json:object>
 <json:object name="category">
  <json:object name="general">
   <json:object name="news">
    <json:string name="technic">Foo</json:string>
   </json:object>
  </json:object>
 </json:object>
 <json:array name="tags">
  <json:string>bar</json:string>
  <json:string>foo</json:string>
  <json:string>test</json:string>
 </json:array>
 <json:array name="entry">
  <json:object>
   <json:string name="title">bar</json:string>
  </json:object>
  <json:object>
   <json:string name="title">foo</json:string>
  </json:object>
 </json:array>
</json:object>
INPUT;

		$dom = new \DOMDocument();
		$dom->loadXML($body);

		$transformer = new Jsonx();

		$person = new \stdClass();
		$person->title = 'Foo';

		$category = new \stdClass();
		$category->general = new \stdClass();
		$category->general->news = new \stdClass();
		$category->general->news->technic = 'Foo';

		$entry = array();
		$entry[0] = new \stdClass();
		$entry[0]->title = 'bar';
		$entry[1] = new \stdClass();
		$entry[1]->title = 'foo';

		$expect = new \stdClass();
		$expect->id = 1;
		$expect->title = 'foobar';
		$expect->active = true;
		$expect->disabled = false;
		$expect->rating = 12.45;
		$expect->date = '2014-01-01T12:34:47+01:00';
		$expect->href = 'http://foo.com';
		$expect->person = $person;
		$expect->category = $category;
		$expect->tags = array('bar', 'foo', 'test');
		$expect->entry = $entry;
		$expect->empty = null;

		$data = $transformer->transform($dom);

		$this->assertInstanceOf('stdClass', $data);
		$this->assertEquals($expect, $data);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testInvalidData()
	{
		$transformer = new Jsonx();
		$transformer->transform(array());
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testInvalidElementName()
	{
		$body = '<json:foo xmlns:json="http://www.ibm.com/xmlns/prod/2009/jsonx" />';

		$dom = new \DOMDocument();
		$dom->loadXML($body);

		$transformer = new Jsonx();
		$transformer->transform($dom);
	}

	public function testAccept()
	{
		$transformer = new Jsonx();

		$this->assertTrue($transformer->accept(new MediaType('application/jsonx+xml')));
	}

	public function testAcceptInvalid()
	{
		$transformer = new Jsonx();

		$this->assertFalse($transformer->accept(new MediaType('text/plain')));
	}
}
