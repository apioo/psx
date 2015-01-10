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

namespace PSX\Data;

/**
 * RecordTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class RecordTest extends \PHPUnit_Framework_TestCase
{
	public function testGetMagicMethods()
	{
		$record = new Record('foo', array(
			'id'    => 1,
			'title' => 'bar',
		));

		$this->assertEquals(1, $record->getId());
		$this->assertEquals('bar', $record->getTitle());
	}

	public function testSetMagicMethods()
	{
		$record = new Record('foo', array(
			'id'    => 1,
			'title' => 'bar',
		));

		$this->assertEquals(1, $record->getId());
		$this->assertEquals('bar', $record->getTitle());

		$record->setId(2);
		$record->setTitle('foo');

		$this->assertEquals(2, $record->getId());
		$this->assertEquals('foo', $record->getTitle());
	}

	public function testGetRecordInfo()
	{
		$fields = array(
			'id'    => 1,
			'title' => 'bar',
		);
		$record = new Record('foo', $fields);

		$this->assertEquals('foo', $record->getRecordInfo()->getName());
		$this->assertEquals($fields, $record->getRecordInfo()->getFields());
		$this->assertEquals(1, $record->getRecordInfo()->hasFields(array('id', 'title')));
		$this->assertEquals(true, $record->getRecordInfo()->hasField('id'));
	}

	public function testSerialize()
	{
		$record = new Record('foo', array(
			'id'    => 1,
			'title' => 'bar',
		));

		$this->assertEquals(1, $record->getId());
		$this->assertEquals('bar', $record->getTitle());

		$record = unserialize(serialize($record));

		$this->assertEquals(1, $record->getId());
		$this->assertEquals('bar', $record->getTitle());
	}

	/**
	 * @expectedException BadMethodCallException
	 */
	public function testBadMethodCall()
	{
		$record = new Record('foo', array(
			'id'    => 1,
			'title' => 'bar',
		));

		$record->foo();
	}
}
