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

namespace PSX\Data\Record;

use PSX\Data\RecordInterface;

/**
 * ImporterTestCase
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
trait ImporterTestCase
{
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

	public function testAccept()
	{
		$news     = $this->getRecord();
		$importer = $this->getImporter();

		$this->assertTrue($importer->accept($news));
	}

	public function testAcceptInvalid()
	{
		$importer = $this->getImporter();

		$this->assertFalse($importer->accept('foo'));
	}

	public function testImport()
	{
		$news     = $this->getRecord();
		$body     = $this->getPayload();
		$importer = $this->getImporter();

		$record = $importer->import($news, $body);

		$this->assertRecord($record);
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testImportInvalidData()
	{
		$news     = $this->getRecord();
		$importer = $this->getImporter();
		$importer->import($news, 'foo');
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testImportInvalidSource()
	{
		$news     = $this->getRecord();
		$body     = $this->getPayload();
		$importer = $this->getImporter();
		$importer->import('foo', $body);
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
			$this->assertInstanceOf('PSX\Data\Record\Importer\Test\Person', $record->getPerson());
			$this->assertEquals('Foo', $record->getPerson()->getTitle());

			$this->assertEquals(true, is_array($record->getTags()));
			$this->assertEquals(3, count($record->getTags()));
			$this->assertEquals('bar', $record->getTags()[0]);
			$this->assertEquals('foo', $record->getTags()[1]);
			$this->assertEquals('test', $record->getTags()[2]);

			$this->assertEquals(true, is_array($record->getEntry()));
			$this->assertEquals(3, count($record->getEntry()));
			$this->assertEquals('bar', $record->getEntry()[0]->getTitle());
			$this->assertEquals('foo', $record->getEntry()[1]->getTitle());
			$this->assertEquals('test', $record->getEntry()[2]->getTitle());

			foreach($record->getEntry() as $entry)
			{
				$this->assertInstanceOf('PSX\Data\Record\Importer\Test\Entry', $entry);
			}

			$this->assertInstanceOf('stdClass', $record->getToken());
			$this->assertEquals('bar', $record->getToken()->value);

			$this->assertInstanceOf('PSX\Url', $record->getUrl());
			$this->assertEquals('http://google.com', $record->getUrl()->__toString());
		}
	}

	protected function getPayload()
	{
		return $this->canImportComplexRecord() ? $this->getComplexPayload() : $this->getSimplePayload();
	}

	protected function getComplexPayload()
	{
		return array(
			'id' => '1',
			'title' => 'foobar',
			'active' => 'true',
			'disabled' => 'false',
			'count' => '12',
			'rating' => '12.45',
			'foobar' => 'foo',
			'date' => '2014-01-01T12:34:47+01:00',
			'person' => array(
				'title' => 'Foo',
			),
			'tags' => array('bar', 'foo', 'test'),
			'entry' => array(
				array(
					'title' => 'bar'
				),
				array(
					'title' => 'foo'
				),
				array(
					'title' => 'test'
				),
			),
			'token' => array(
				'sig' => 'bar',
				'alg' => 'foo',
			),
			'url' => 'http://google.com'
		);
	}

	protected function getSimplePayload()
	{
		return array(
			'id' => '1',
			'title' => 'foobar',
			'active' => 'true',
			'disabled' => 'false',
			'count' => '12',
			'rating' => '12.45',
			'foobar' => 'foo',
			'date' => '2014-01-01T12:34:47+01:00',
		);
	}
}
