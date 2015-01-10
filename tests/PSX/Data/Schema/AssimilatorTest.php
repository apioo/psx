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

namespace PSX\Data\Schema;

use PSX\Data\SchemaAbstract;
use PSX\Data\Schema\Property;

/**
 * AssimilatorTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
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

		$data = $this->assimilator->assimilate($data, $this->schema);

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

		$data = $this->assimilator->assimilate($data, $this->schema);
	}

	public function testAssimilateRemoveUnknownParameter()
	{
		$data = [[
			'content' => 'foo',
			'foo' => 'foo',
		]];

		$data = $this->assimilator->assimilate($data, $this->schema);

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

		$data = $this->assimilator->assimilate($data, $this->schema);
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

		$data = $this->assimilator->assimilate($data, $this->schema);
	}

	/**
	 * @expectedException ErrorException
	 */
	public function testAssimilateCastArrayToString()
	{
		$data = [[
			'content' => array(),
		]];

		$data = $this->assimilator->assimilate($data, $this->schema);
	}

	/**
	 * @expectedException ErrorException
	 */
	public function testAssimilateCastObjectToString()
	{
		$data = [[
			'content' => new \stdClass(),
		]];

		$data = $this->assimilator->assimilate($data, $this->schema);
	}

	public function testAssimilateCastStringToString()
	{
		$data = [[
			'content' => 'foo',
		]];

		$data = $this->assimilator->assimilate($data, $this->schema);

		$this->assertEquals('foo', $data[0]->getContent());
		$this->assertInternalType('string', $data[0]->getContent());
	}

	public function testAssimilateCastStringToInteger()
	{
		$data = [[
			'content' => 'foo',
			'rating' => '12',
		]];

		$data = $this->assimilator->assimilate($data, $this->schema);

		$this->assertEquals(12, $data[0]->getRating());
		$this->assertInternalType('integer', $data[0]->getRating());
	}

	public function testAssimilateCastStringToFloat()
	{
		$data = [[
			'content' => 'foo',
			'price' => '13.37',
		]];

		$data = $this->assimilator->assimilate($data, $this->schema);

		$this->assertEquals(13.37, $data[0]->getPrice());
		$this->assertInternalType('float', $data[0]->getPrice());
	}

	public function testAssimilateCastStringToBoolean()
	{
		$data = [[
			'content' => 'foo',
			'read' => '1',
		]];

		$data = $this->assimilator->assimilate($data, $this->schema);

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
