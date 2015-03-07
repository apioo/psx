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

use PSX\Data\SchemaAbstract;
use PSX\Data\Schema\Property;

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
		$this->assimilator = new Assimilator();
		$this->schema      = getContainer()->get('schema_manager')->getSchema('PSX\Data\Schema\AssimilatorSchema');
	}

	public function testAssimilate()
	{
		$data = [[
			'content' => 'foo',
			'author' => [
				'title' => 'foo',
				'email' => 'foo@foo.com',
			],
			'location' => [
			],
			'receiver' => [[
				'title' => 'foo',
				'email' => 'foo@foo.com',
			],[
				'title' => 'bar',
				'email' => 'foo@bar.com',
				'location' => [
					'lat' => 40.711485,
					'long' => -74.013624,
				]
			]],
			'tags' => ['foo', 'bar'],
			'date' => '2014-08-07',
		]];

		$data = $this->assimilator->assimilate($this->schema, $data);

		$this->assertEquals('foo', $data[0]->getContent());
		$this->assertEquals('foo', $data[0]->getAuthor()->getTitle());
		$this->assertEquals('foo@foo.com', $data[0]->getAuthor()->getEmail());
		$this->assertEquals('foo', $data[0]->getReceiver()[0]->getTitle());
		$this->assertEquals('foo@foo.com', $data[0]->getReceiver()[0]->getEmail());
		$this->assertEquals('bar', $data[0]->getReceiver()[1]->getTitle());
		$this->assertEquals('foo@bar.com', $data[0]->getReceiver()[1]->getEmail());
		$this->assertEquals('40.711485', $data[0]->getReceiver()[1]->getLocation()->getLat());
		$this->assertEquals('-74.013624', $data[0]->getReceiver()[1]->getLocation()->getLong());
		$this->assertEquals(['foo', 'bar'], $data[0]->getTags());
		$this->assertInstanceOf('DateTime', $data[0]->getDate());
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testAssimilateMissingRequired()
	{
		$data = [[
		]];

		$data = $this->assimilator->assimilate($this->schema, $data);
	}

	public function testAssimilateRemoveUnknownParameter()
	{
		$data = [[
			'content' => 'foo',
			'foo' => 'foo',
		]];

		$data = $this->assimilator->assimilate($this->schema, $data);

		$this->assertEquals(array('content' => 'foo'), $data[0]->getRecordInfo()->getFields());
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testAssimilateComplexTypeString()
	{
		$data = [[
			'content' => 'foo',
			'author' => 'foo',
		]];

		$data = $this->assimilator->assimilate($this->schema, $data);
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testAssimilateArrayTypeString()
	{
		$data = [[
			'content' => 'foo',
			'receiver' => 'foo',
		]];

		$data = $this->assimilator->assimilate($this->schema, $data);
	}

	/**
	 * @expectedException ErrorException
	 */
	public function testAssimilateCastArrayToString()
	{
		$data = [[
			'content' => array(),
		]];

		$data = $this->assimilator->assimilate($this->schema, $data);
	}

	/**
	 * @expectedException ErrorException
	 */
	public function testAssimilateCastObjectToString()
	{
		$data = [[
			'content' => new \stdClass(),
		]];

		$data = $this->assimilator->assimilate($this->schema, $data);
	}

	public function testAssimilateCastStringToString()
	{
		$data = [[
			'content' => 'foo',
		]];

		$data = $this->assimilator->assimilate($this->schema, $data);

		$this->assertEquals('foo', $data[0]->getContent());
		$this->assertInternalType('string', $data[0]->getContent());
	}

	public function testAssimilateCastStringToInteger()
	{
		$data = [[
			'content' => 'foo',
			'rating' => '12',
		]];

		$data = $this->assimilator->assimilate($this->schema, $data);

		$this->assertEquals(12, $data[0]->getRating());
		$this->assertInternalType('integer', $data[0]->getRating());
	}

	public function testAssimilateCastStringToFloat()
	{
		$data = [[
			'content' => 'foo',
			'price' => '13.37',
		]];

		$data = $this->assimilator->assimilate($this->schema, $data);

		$this->assertEquals(13.37, $data[0]->getPrice());
		$this->assertInternalType('float', $data[0]->getPrice());
	}

	public function testAssimilateCastStringToBoolean()
	{
		$data = [[
			'content' => 'foo',
			'read' => '1',
		]];

		$data = $this->assimilator->assimilate($this->schema, $data);

		$this->assertEquals(true, $data[0]->getRead());
		$this->assertInternalType('boolean', $data[0]->getRead());
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
		$sb->complexType($author);
		$sb->complexType($location);
		$sb->arrayType('receiver')
			->setPrototype($author);
		$sb->arrayType('tags')
			->setPrototype(new Property\String('tag'));
		$sb->dateTime('date');
		$news = $sb->getProperty();

		$root = new Property\ArrayType('entries');
		$root->setPrototype($news);

		return $root;
	}
}
