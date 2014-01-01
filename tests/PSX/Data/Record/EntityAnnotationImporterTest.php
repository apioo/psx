<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2013 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Data\Record;

use PSX\Data\Record;
use PSX\Data\RecordAbstract;
use PSX\Data\FactoryInterface;
use PSX\Data\BuilderInterface;
use PSX\Data\Reader;
use PSX\Data\Writer;
use PSX\Exception;
use PSX\Http\Message;

/**
 * EntityAnnotationImporterTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class EntityAnnotationImporterTest extends \PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
	}

	protected function tearDown()
	{
	}

	public function testImportJson()
	{
		$body = <<<DATA
{
	"id": 1,
	"title": "foobar",
	"active": 1,
	"count": 12,
	"rating": 12.45,
	"date": "2014-01-01T12:34:47+01:00",
	"foobar": "foo",
	"person": {
		"title": "Foo"
	},
	"tags": [{
		"title": "bar"
	},{
		"title": "foo"
	},{
		"title": "test"
	}],
	"achievment": {
		"type": "foo"
	},
	"payment": {
		"type": "paypal"
	}
}
DATA;

		// read json
		$news     = new NewsEntity();
		$reader   = new Reader\Json();
		$importer = new EntityAnnotationImporter();
		$record   = $importer->import($news, $reader->read(new Message(array(), $body)));

		$this->assertEquals(1, $record->getId());
		$this->assertInternalType('integer', $record->getId());
		$this->assertEquals('foobar', $record->getTitle());
		$this->assertInternalType('string', $record->getTitle());
		$this->assertEquals(true, $record->getActive());
		$this->assertInternalType('boolean', $record->getActive());
		$this->assertEquals(12, $record->getCount());
		$this->assertInternalType('integer', $record->getCount());
		$this->assertEquals(12.45, $record->getRating());
		$this->assertInternalType('float', $record->getRating());
		$this->assertInstanceOf('PSX\Data\RecordInterface', $record->getPerson());
		$this->assertEquals(true, is_array($record->getTags()));
		$this->assertInstanceOf('PSX\Data\RecordInterface', $record->getAchievment());

		foreach($record->getTags() as $tag)
		{
			$this->assertInstanceOf('PSX\Data\RecordInterface', $tag);
		}
	}

	public function testExportJson()
	{
		$body = <<<DATA
{
	"id": 1,
	"title": "foobar",
	"active": 1,
	"count": 12,
	"rating": 12.45,
	"date": "2014-01-01T12:34:47+01:00",
	"foobar": "foo",
	"person": {
		"title": "Foo"
	},
	"tags": [{
		"title": "bar"
	},{
		"title": "foo"
	},{
		"title": "test"
	}],
	"achievment": {
		"type": "foo"
	},
	"payment": {
		"type": "paypal"
	}
}
DATA;

		// read json
		$news     = new NewsEntity();
		$reader   = new Reader\Json();
		$importer = new EntityAnnotationImporter();
		$record   = $importer->import($news, $reader->read(new Message(array(), $body)));

		$writer = new Writer\Json();
		$resp   = $writer->write($record);

		// remove unknown value
		$body = str_replace('"foobar": "foo",', '', $body);

		$this->assertJsonStringEqualsJsonString($body, $resp);
	}

	public function testImportXml()
	{
		$body = <<<DATA
<?xml version="1.0" encoding="UTF-8"?>
<newsEntity>
	<id>1</id>
	<title>foobar</title>
	<active>1</active>
	<count>12</count>
	<rating>12.45</rating>
	<date>2014-01-01T12:34:47+01:00</date>
	<foobar>foo</foobar>
	<person>
		<title>Foo</title>
	</person>
	<tags>
		<title>bar</title>
	</tags>
	<tags>
		<title>foo</title>
	</tags>
	<tags>
		<title>test</title>
	</tags>
	<achievment>
		<type>foo</type>
	</achievment>
	<payment>
		<type>paypal</type>
	</payment>
</newsEntity>
DATA;

		// read xml
		$news     = new NewsEntity();
		$reader   = new Reader\Xml();
		$importer = new EntityAnnotationImporter();
		$record   = $importer->import($news, $reader->read(new Message(array(), $body)));

		$this->assertEquals(1, $record->getId());
		$this->assertEquals('foobar', $record->getTitle());
		$this->assertEquals(true, $record->getActive());
		$this->assertEquals(12, $record->getCount());
		$this->assertEquals(12.45, $record->getRating());
		$this->assertInstanceOf('PSX\Data\RecordInterface', $record->getPerson());
		$this->assertEquals(true, is_array($record->getTags()));
		$this->assertInstanceOf('PSX\Data\RecordInterface', $record->getAchievment());

		foreach($record->getTags() as $tag)
		{
			$this->assertInstanceOf('PSX\Data\RecordInterface', $tag);
		}
	}

	public function testExportXml()
	{
		$body = <<<DATA
<?xml version="1.0" encoding="UTF-8"?>
<newsEntity>
	<id>1</id>
	<title>foobar</title>
	<active>true</active>
	<count>12</count>
	<rating>12.45</rating>
	<date>2014-01-01T12:34:47+01:00</date>
	<foobar>foo</foobar>
	<person>
		<title>Foo</title>
	</person>
	<tags>
		<title>bar</title>
	</tags>
	<tags>
		<title>foo</title>
	</tags>
	<tags>
		<title>test</title>
	</tags>
	<achievment>
		<type>foo</type>
	</achievment>
	<payment>
		<type>paypal</type>
	</payment>
</newsEntity>
DATA;

		// read json
		$news     = new NewsEntity();
		$reader   = new Reader\Xml();
		$importer = new EntityAnnotationImporter();
		$record   = $importer->import($news, $reader->read(new Message(array(), $body)));

		$writer = new Writer\Xml();
		$resp   = $writer->write($record);

		// remove unknown value
		$body = str_replace('<foobar>foo</foobar>', '', $body);

		$this->assertXmlStringEqualsXmlString($body, $resp);
	}
}

/**
 * @Entity
 * @Table(name="news")
 */
class NewsEntity
{
	/**
	 * @Column(type="integer")
	 */
	protected $id;

	/**
	 * @Column(type="string")
	 */
	protected $title;

	/**
	 * @Column(type="boolean")
	 */
	protected $active;

	/**
	 * @Column(type="integer")
	 */
	protected $count;

	/**
	 * @Column(type="float")
	 */
	protected $rating;

	/**
	 * @Column(type="datetime")
	 */
	protected $date;

	/**
	 * @ManyToOne(targetEntity="PSX\Data\Record\PersonEntity", inversedBy="news")
	 * @JoinColumn(name="person_id", referencedColumnName="id")
	 */
	protected $person;

	/**
	 * @OneToMany(targetEntity="PSX\Data\Record\TagEntity", mappedBy="news")
	 */
	protected $tags;

	/**
	 * @ManyToOne(targetEntity="PSX\Data\Record\AchievmentEntity", inversedBy="news")
	 * @JoinColumn(name="achievment_id", referencedColumnName="id")
	 * @DataFactory PSX\Data\Record\AchievmentEntityFactory
	 */
	protected $achievment;

	/**
	 * @ManyToOne(targetEntity="PSX\Data\Record\PaymentEntity", inversedBy="news")
	 * @JoinColumn(name="payment_id", referencedColumnName="id")
	 * @DataBuilder PSX\Data\Record\PaymentEntityBuilder
	 */
	protected $payment;
}

/**
 * @Entity
 * @Table(name="person")
 */
class PersonEntity
{
	/**
	 * @Column(type="string")
	 */
	protected $title;
}

/**
 * @Entity
 * @Table(name="tag")
 */
class TagEntity
{
	/**
	 * @Column(type="string")
	 */
	protected $title;
}

class AchievmentEntityFactory implements FactoryInterface
{
	public function factory($data)
	{
		if(isset($data['type']))
		{
			$class = 'PSX\Data\Record\AchievmentEntity' . ucfirst($data['type']);

			if(class_exists($class))
			{
				return new $class();
			}
		}

		return null;
	}
}

interface AchievmentEntity
{
}

/**
 * @Entity
 * @Table(name="achievment")
 */
class AchievmentEntityFoo implements AchievmentEntity
{
	/**
	 * @Column(type="string")
	 */
	protected $type;
}

class PaymentEntityBuilder implements BuilderInterface
{
	public function build($data)
	{
		// this is the place to build complex records depending on the content
		// if the default importer fits not your need

		return new Record('payment', array(
			'type' => 'paypal',
		));
	}
}

