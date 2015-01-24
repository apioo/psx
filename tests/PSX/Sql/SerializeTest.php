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

namespace PSX\Sql;

use PSX\Config;
use PSX\Data\Record;
use PSX\DateTime;
use PSX\Sql\TableInterface;
use PSX\Test\TableDataSet;

/**
 * SerializeTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class SerializeTest extends DbTestCase
{
	public function getDataSet()
	{
		return $this->createFlatXMLDataSet(__DIR__ . '/table_fixture.xml');
	}

	public function testSerialize()
	{
		$table = getContainer()->get('table_manager')->getTable('PSX\Sql\TestTableCommand');
		$row   = $table->get(1);

		$this->assertInternalType('string', $row['col_bigint']);
		$this->assertEquals('68719476735', $row['col_bigint']);
		$this->assertInternalType('resource', $row['col_blob']);
		$this->assertEquals('foobar', stream_get_contents($row['col_blob']));
		$this->assertInternalType('boolean', $row['col_boolean']);
		$this->assertEquals(true, $row['col_boolean']);
		$this->assertInstanceOf('DateTime', $row['col_datetime']);
		$this->assertEquals('2015-01-21 23:59:59', $row['col_datetime']->format('Y-m-d H:i:s'));
		$this->assertEquals('UTC', $row['col_datetime']->getTimezone()->getName());
		$this->assertInstanceOf('DateTime', $row['col_datetimetz']);
		$this->assertEquals('2015-01-21 23:59:59', $row['col_datetimetz']->format('Y-m-d H:i:s'));
		// mysql does not support timezones
		//$this->assertEquals('+01:00', $row['col_datetimetz']->getTimezone()->getName());
		$this->assertInstanceOf('DateTime', $row['col_date']);
		$this->assertEquals('2015-01-21', $row['col_date']->format('Y-m-d'));
		$this->assertInternalType('string', $row['col_decimal']);
		$this->assertEquals('10', $row['col_decimal']);
		$this->assertInternalType('float', $row['col_float']);
		$this->assertEquals(10.37, $row['col_float']);
		$this->assertInternalType('integer', $row['col_integer']);
		$this->assertEquals(2147483647, $row['col_integer']);
		$this->assertInternalType('integer', $row['col_smallint']);
		$this->assertEquals(255, $row['col_smallint']);
		$this->assertInternalType('string', $row['col_text']);
		$this->assertEquals('foobar', $row['col_text']);
		$this->assertInstanceOf('DateTime', $row['col_time']);
		$this->assertEquals('23:59:59', $row['col_time']->format('H:i:s'));
		$this->assertInternalType('string', $row['col_string']);
		$this->assertEquals('foobar', $row['col_string']);

		$array  = array('foo' => 'bar');
		$object = new \stdClass();
		$object->foo = 'bar';

		$this->assertInternalType('array', $row['col_array']);
		$this->assertEquals($array, $row['col_array']);
		$this->assertInstanceOf('stdClass', $row['col_object']);
		$this->assertEquals($object, $row['col_object']);
	}
}

