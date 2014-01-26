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

use PDOException;
use PSX\Data\Record;
use PSX\Data\RecordAbstract;
use PSX\Data\FactoryInterface;
use PSX\Data\BuilderInterface;
use PSX\Data\Reader;
use PSX\Data\Writer;
use PSX\Exception;
use PSX\Http\Message;
use PSX\Sql\TableAbstract;
use PSX\Sql\TableInterface;

/**
 * TableImporterTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class TableImporterTest extends \PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
		try
		{
			$this->sql = getContainer()->get('sql');
		}
		catch(PDOException $e)
		{
			$this->markTestSkipped($e->getMessage());
		}
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
	"foobar": "foo",
	"date": "2014-01-01T12:34:47+01:00"
}
DATA;

		// read json
		$table    = new TestTable($this->sql);
		$reader   = new Reader\Json();
		$importer = new TableImporter();
		$record   = $importer->import($table, $reader->read(new Message(array(), $body)));

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
		$this->assertInstanceOf('DateTime', $record->getDate());
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
	"foobar": "foo",
	"date": "2014-01-01T12:34:47+01:00"
}
DATA;

		// read json
		$table    = new TestTable($this->sql);
		$reader   = new Reader\Json();
		$importer = new TableImporter();
		$record   = $importer->import($table, $reader->read(new Message(array(), $body)));

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
<news>
	<id>1</id>
	<title>foobar</title>
	<active>1</active>
	<count>12</count>
	<rating>12.45</rating>
	<date>2014-01-01T12:34:47+01:00</date>
	<foobar>foo</foobar>
</news>
DATA;

		// read xml
		$table    = new TestTable($this->sql);
		$reader   = new Reader\Xml();
		$importer = new TableImporter();
		$record   = $importer->import($table, $reader->read(new Message(array(), $body)));

		$this->assertEquals(1, $record->getId());
		$this->assertEquals('foobar', $record->getTitle());
		$this->assertEquals(true, $record->getActive());
		$this->assertEquals(12, $record->getCount());
		$this->assertEquals(12.45, $record->getRating());
		$this->assertInstanceOf('DateTime', $record->getDate());
	}

	public function testExportXml()
	{
		$body = <<<DATA
<?xml version="1.0" encoding="UTF-8"?>
<news>
	<id>1</id>
	<title>foobar</title>
	<active>true</active>
	<count>12</count>
	<rating>12.45</rating>
	<date>2014-01-01T12:34:47+01:00</date>
	<foobar>foo</foobar>
</news>
DATA;

		// read json
		$table    = new TestTable($this->sql);
		$reader   = new Reader\Xml();
		$importer = new TableImporter();
		$record   = $importer->import($table, $reader->read(new Message(array(), $body)));

		$writer = new Writer\Xml();
		$resp   = $writer->write($record);

		// remove unknown value
		$body = str_replace('<foobar>foo</foobar>', '', $body);

		$this->assertXmlStringEqualsXmlString($body, $resp);
	}
}

class TestTable extends TableAbstract
{
	public function getName()
	{
		return 'news';
	}

	public function getColumns()
	{
		return array(
			'id'     => TableInterface::TYPE_INT | 10 | TableInterface::PRIMARY_KEY | TableInterface::AUTO_INCREMENT,
			'title'  => TableInterface::TYPE_VARCHAR | 16,
			'active' => TableInterface::TYPE_BOOLEAN,
			'count'  => TableInterface::TYPE_INT,
			'rating' => TableInterface::TYPE_FLOAT,
			'date'   => TableInterface::TYPE_DATETIME,
		);
	}
}

