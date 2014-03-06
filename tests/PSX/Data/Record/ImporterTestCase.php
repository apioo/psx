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

namespace PSX\Data\Record;

use PDOException;
use PSX\Data\Record;
use PSX\Data\RecordAbstract;
use PSX\Data\RecordInterface;
use PSX\Data\FactoryInterface;
use PSX\Data\BuilderInterface;
use PSX\Data\Reader;
use PSX\Data\Writer;
use PSX\Exception;
use PSX\Http\Message;

/**
 * ImporterTestCase
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class ImporterTestCase extends \PHPUnit_Framework_TestCase
{
	/**
	 * Returns the importer on which the test should operate
	 *
	 * @return PSX\Data\Record\ImporterInterface
	 */
	abstract protected function getImporter();

	/**
	 * Returns the source record
	 *
	 * @return mixed
	 */
	abstract protected function getRecord();

	/**
	 * Tells the test whether the importer can import complex records if yes
	 * the importer gets a more complex payload
	 *
	 * @return boolean
	 */
	protected function canImportComplexRecord()
	{
		return true;
	}

	/**
	 * Tells the test whether the importer can determine an type if yes we 
	 * strictly check for that else we only compare the strings
	 *
	 * @return boolean
	 */
	protected function canDetermineType()
	{
		return true;
	}

	public function testImportJson()
	{
		// read json
		$news     = $this->getRecord();
		$body     = $this->getJsonPayload();
		$reader   = new Reader\Json();
		$importer = $this->getImporter();
		$record   = $importer->import($news, $reader->read(new Message(array(), $body)));

		$this->assertRecord($record);
	}

	public function testImportXml()
	{
		// read xml
		$news     = $this->getRecord();
		$body     = $this->getXmlPayload();
		$reader   = new Reader\Xml();
		$importer = $this->getImporter();
		$record   = $importer->import($news, $reader->read(new Message(array(), $body)));

		$this->assertRecord($record);
	}

	protected function assertRecord(RecordInterface $record)
	{
		// check available fields
		$this->assertTrue($record->getRecordInfo()->hasField('id'));
		$this->assertTrue($record->getRecordInfo()->hasField('title'));
		$this->assertTrue($record->getRecordInfo()->hasField('active'));
		$this->assertTrue($record->getRecordInfo()->hasField('disabled'));
		$this->assertTrue($record->getRecordInfo()->hasField('count'));
		$this->assertTrue($record->getRecordInfo()->hasField('rating'));
		$this->assertFalse($record->getRecordInfo()->hasField('foobar'));
		$this->assertFalse($record->getRecordInfo()->hasField('foo'));

		if($this->canDetermineType())
		{
			$this->assertEquals(1, $record->getId());
			$this->assertEquals('foobar', $record->getTitle());
			$this->assertTrue($record->getActive());
			$this->assertFalse($record->getDisabled());
			$this->assertEquals(12, $record->getCount());
			$this->assertEquals(12.45, $record->getRating());

			$this->assertInternalType('integer', $record->getId());
			$this->assertInternalType('string', $record->getTitle());
			$this->assertInternalType('boolean', $record->getActive());
			$this->assertInternalType('boolean', $record->getDisabled());
			$this->assertInternalType('integer', $record->getCount());
			$this->assertInternalType('float', $record->getRating());
			$this->assertInstanceOf('DateTime', $record->getDate());
		}
		else
		{
			$this->assertEquals('1', $record->getId());
			$this->assertEquals('foobar', $record->getTitle());
			// the json reader returns the real php type the xml reader returns
			// the string true or false
			$this->assertTrue($record->getActive() === true || $record->getActive() === 'true');
			$this->assertTrue($record->getDisabled() === false || $record->getDisabled() === 'false');
			$this->assertEquals('12', $record->getCount());
			$this->assertEquals('12.45', $record->getRating());
			$this->assertEquals('2014-01-01T12:34:47+01:00', $record->getDate());
		}

		if($this->canImportComplexRecord())
		{
			$this->assertInstanceOf('PSX\Data\RecordInterface', $record->getPerson());
			$this->assertEquals('Foo', $record->getPerson()->getTitle());

			$this->assertInstanceOf('PSX\Data\RecordInterface', $record->getPayment());
			$this->assertEquals('paypal', $record->getPayment()->getType());
			$this->assertEquals('foobar', $record->getPayment()->getCustom());

			$this->assertEquals(true, is_array($record->getAchievment()));
			$this->assertEquals(2, count($record->getAchievment()));
			$this->assertEquals('bar', $record->getAchievment()[0]->getFoo());
			$this->assertEquals('foo', $record->getAchievment()[1]->getBar());

			$this->assertEquals(true, is_array($record->getTags()));
			$this->assertEquals(3, count($record->getTags()));
			$this->assertEquals('bar', $record->getTags()[0]->getTitle());
			$this->assertEquals('foo', $record->getTags()[1]->getTitle());
			$this->assertEquals('test', $record->getTags()[2]->getTitle());

			foreach($record->getTags() as $tag)
			{
				$this->assertInstanceOf('PSX\Data\RecordInterface', $tag);
			}
		}
	}

	protected function getJsonPayload()
	{
		return $this->canImportComplexRecord() ? $this->getComplexJsonPayload() : $this->getSimpleJsonPayload();
	}

	protected function getXmlPayload()
	{
		return $this->canImportComplexRecord() ? $this->getComplexXmlPayload() : $this->getSimpleXmlPayload();
	}

	protected function getSimpleJsonPayload()
	{
		return <<<DATA
{
	"id": 1,
	"title": "foobar",
	"active": true,
	"disabled": false,
	"count": 12,
	"rating": 12.45,
	"foobar": "foo",
	"date": "2014-01-01T12:34:47+01:00"
}
DATA;
	}

	protected function getComplexJsonPayload()
	{
		return <<<DATA
{
	"id": 1,
	"title": "foobar",
	"active": true,
	"disabled": false,
	"count": 12,
	"rating": 12.45,
	"foobar": "foo",
	"date": "2014-01-01T12:34:47+01:00",
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
	"achievment": [{
		"type": "foo",
		"foo": "bar"
	},{
		"type": "bar",
		"bar": "foo"
	}],
	"payment": {
		"type": "paypal"
	}
}
DATA;
	}

	protected function getSimpleXmlPayload()
	{
		return <<<DATA
<?xml version="1.0" encoding="UTF-8"?>
<news>
	<id>1</id>
	<title>foobar</title>
	<active>true</active>
	<disabled>false</disabled>
	<count>12</count>
	<rating>12.45</rating>
	<foobar>foo</foobar>
	<date>2014-01-01T12:34:47+01:00</date>
</news>
DATA;
	}

	protected function getComplexXmlPayload()
	{
		return <<<DATA
<?xml version="1.0" encoding="UTF-8"?>
<news>
	<id>1</id>
	<title>foobar</title>
	<active>true</active>
	<disabled>false</disabled>
	<count>12</count>
	<rating>12.45</rating>
	<foobar>foo</foobar>
	<date>2014-01-01T12:34:47+01:00</date>
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
		<foo>bar</foo>
	</achievment>
	<achievment>
		<type>bar</type>
		<bar>foo</bar>
	</achievment>
	<payment>
		<type>paypal</type>
	</payment>
</news>
DATA;
	}
}
