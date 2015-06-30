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

namespace PSX\Data\Schema;

use PSX\Data\Schema\Property;
use PSX\Data\SchemaAbstract;
use PSX\Test\Environment;

/**
 * AssimilatorTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class AssimilatorTest extends \PHPUnit_Framework_TestCase
{
	protected $assimilator;
	protected $schema;

	protected function setUp()
	{
		$this->assimilator = Environment::getService('schema_assimilator');
		$this->schema      = Environment::getService('schema_manager')->getSchema('PSX\Data\Schema\AssimilatorSchema');
	}

	public function testAssimilate()
	{
		$data = [
			'content' => 'foo',
			'rating' => 12,
			'price' => 12.23,
			'read' => false,
			'location' => [
				'lat' => 40.711485,
				'long' => -74.013624,
			],
			'receiver' => [[
				'title' => 'foo',
				'email' => 'foo@foo.com',
			],[
				'title' => 'bar',
				'email' => 'foo@bar.com',
			]],
			'tags' => ['foo', 'bar'],
			'date' => '2014-08-07',
		];

		$data = $this->assimilator->assimilate($this->schema, $data);

		$this->assertEquals('foo', $data->getContent());
		$this->assertEquals(12, $data->getRating());
		$this->assertEquals(12.23, $data->getPrice());
		$this->assertEquals(false, $data->getRead());
		$this->assertEquals(40.711485, $data->getLocation()->getLat());
		$this->assertEquals(-74.013624, $data->getLocation()->getLong());
		$this->assertEquals('foo', $data->getReceiver()[0]->getTitle());
		$this->assertEquals('foo@foo.com', $data->getReceiver()[0]->getEmail());
		$this->assertEquals('bar', $data->getReceiver()[1]->getTitle());
		$this->assertEquals('foo@bar.com', $data->getReceiver()[1]->getEmail());
		$this->assertEquals(['foo', 'bar'], $data->getTags());
		$this->assertInstanceOf('DateTime', $data->getDate());
	}

	public function testAssimilateObject()
	{
		$data = new \stdClass();
		$data->content = 'foo';
		$data->rating = 12;
		$data->price = 12.23;
		$data->read = false;
		$data->location = new \stdClass();
		$data->location->lat = 40.711485;
		$data->location->long = -74.013624;
		$data->receiver = [];
		$data->receiver[0] = new \stdClass();
		$data->receiver[0]->title = 'foo';
		$data->receiver[0]->email = 'foo@foo.com';
		$data->receiver[1] = new \stdClass();
		$data->receiver[1]->title = 'bar';
		$data->receiver[1]->email = 'foo@bar.com';
		$data->tags = ['foo', 'bar'];
		$data->date = '2014-08-07';

		$data = $this->assimilator->assimilate($this->schema, $data);

		$this->assertEquals('foo', $data->getContent());
		$this->assertEquals(12, $data->getRating());
		$this->assertEquals(12.23, $data->getPrice());
		$this->assertEquals(false, $data->getRead());
		$this->assertEquals(40.711485, $data->getLocation()->getLat());
		$this->assertEquals(-74.013624, $data->getLocation()->getLong());
		$this->assertEquals('foo', $data->getReceiver()[0]->getTitle());
		$this->assertEquals('foo@foo.com', $data->getReceiver()[0]->getEmail());
		$this->assertEquals('bar', $data->getReceiver()[1]->getTitle());
		$this->assertEquals('foo@bar.com', $data->getReceiver()[1]->getEmail());
		$this->assertEquals(['foo', 'bar'], $data->getTags());
		$this->assertInstanceOf('DateTime', $data->getDate());
	}

	/**
	 * @expectedException \PSX\Data\Schema\ValidationException
	 */
	public function testAssimilateMissingRequired()
	{
		$data = [
		];

		$data = $this->assimilator->assimilate($this->schema, $data);
	}

	public function testAssimilateRemoveUnknownParameter()
	{
		$data = [
			'content' => 'foo',
			'foo' => 'foo',
		];

		$data = $this->assimilator->assimilate($this->schema, $data);

		$this->assertEquals(array('content' => 'foo'), $data->getRecordInfo()->getFields());
	}

	/**
	 * @expectedException \PSX\Data\Schema\ValidationException
	 */
	public function testAssimilateComplexTypeString()
	{
		$data = [
			'content' => 'foo',
			'location' => 'foo',
		];

		$data = $this->assimilator->assimilate($this->schema, $data);
	}

	/**
	 * @expectedException \PSX\Data\Schema\ValidationException
	 */
	public function testAssimilateArrayTypeString()
	{
		$data = [
			'content' => 'foo',
			'receiver' => 'foo',
		];

		$data = $this->assimilator->assimilate($this->schema, $data);
	}

	/**
	 * @expectedException \ErrorException
	 */
	public function testAssimilateCastArrayToString()
	{
		$data = [
			'content' => array(),
		];

		$data = $this->assimilator->assimilate($this->schema, $data);
	}

	/**
	 * @expectedException \ErrorException
	 */
	public function testAssimilateCastObjectToString()
	{
		$data = [
			'content' => new \stdClass(),
		];

		$data = $this->assimilator->assimilate($this->schema, $data);
	}

	public function testAssimilateCastStringToString()
	{
		$data = [
			'content' => 'foo',
		];

		$data = $this->assimilator->assimilate($this->schema, $data);

		$this->assertEquals('foo', $data->getContent());
		$this->assertInternalType('string', $data->getContent());
	}

	public function testAssimilateCastStringToInteger()
	{
		$data = [
			'content' => 'foo',
			'rating' => '12',
		];

		$data = $this->assimilator->assimilate($this->schema, $data);

		$this->assertEquals(12, $data->getRating());
		$this->assertInternalType('integer', $data->getRating());
	}

	public function testAssimilateCastStringToFloat()
	{
		$data = [
			'content' => 'foo',
			'price' => '13.37',
		];

		$data = $this->assimilator->assimilate($this->schema, $data);

		$this->assertEquals(13.37, $data->getPrice());
		$this->assertInternalType('float', $data->getPrice());
	}

	public function testAssimilateCastStringToBoolean()
	{
		$data = [
			'content' => 'foo',
			'read' => '1',
		];

		$data = $this->assimilator->assimilate($this->schema, $data);

		$this->assertEquals(true, $data->getRead());
		$this->assertInternalType('boolean', $data->getRead());
	}
}

class AssimilatorSchema extends SchemaAbstract
{
	public function getDefinition()
	{
		$sb = $this->getSchemaBuilder('location');
		$sb->string('lat');
		$sb->string('long');
		$location = $sb->getProperty();

		$sb = $this->getSchemaBuilder('author');
		$sb->string('title');
		$sb->string('email');
		$sb->complexType($location);
		$author = $sb->getProperty();

		$sb = $this->getSchemaBuilder('news');
		$sb->string('content')->setRequired(true);
		$sb->integer('rating');
		$sb->float('price');
		$sb->boolean('read');
		$sb->complexType($location);
		$sb->arrayType('receiver')
			->setPrototype($author);
		$sb->arrayType('tags')
			->setPrototype(Property::getString('tag'));
		$sb->date('date');

		return $sb->getProperty();
	}
}
