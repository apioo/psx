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

namespace PSX\Sql;

use PSX\Config;
use PSX\Data\Record;
use PSX\DateTime;
use PSX\Test\DbTestCase;
use PSX\Test\Environment;

/**
 * SerializeTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
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
		$table = Environment::getService('table_manager')->getTable('PSX\Sql\TestTableCommand');
		$row   = $table->get(1);

		$this->assertInternalType('string', $row->getCol_bigint());
		$this->assertEquals('68719476735', $row->getCol_bigint());
		$this->assertInternalType('resource', $row->getCol_blob());
		$this->assertEquals('foobar', stream_get_contents($row->getCol_blob()));
		$this->assertInternalType('boolean', $row->getCol_boolean());
		$this->assertEquals(true, $row->getCol_boolean());
		$this->assertInstanceOf('DateTime', $row->getCol_datetime());
		$this->assertEquals('2015-01-21 23:59:59', $row->getCol_datetime()->format('Y-m-d H:i:s'));
		$this->assertEquals('UTC', $row->getCol_datetime()->getTimezone()->getName());
		$this->assertInstanceOf('DateTime', $row->getCol_datetimetz());
		$this->assertEquals('2015-01-21 23:59:59', $row->getCol_datetimetz()->format('Y-m-d H:i:s'));
		// mysql does not support timezones
		//$this->assertEquals('+01:00', $row->getCol_datetimetz())>getTimezone()->getName());
		$this->assertInstanceOf('DateTime', $row->getCol_date());
		$this->assertEquals('2015-01-21', $row->getCol_date()->format('Y-m-d'));
		$this->assertInternalType('string', $row->getCol_decimal());
		$this->assertEquals('10', $row->getCol_decimal());
		$this->assertInternalType('float', $row->getCol_float());
		$this->assertEquals(10.37, $row->getCol_float());
		$this->assertInternalType('integer', $row->getCol_integer());
		$this->assertEquals(2147483647, $row->getCol_integer());
		$this->assertInternalType('integer', $row->getCol_smallint());
		$this->assertEquals(255, $row->getCol_smallint());
		$this->assertInternalType('string', $row->getCol_text());
		$this->assertEquals('foobar', $row->getCol_text());
		$this->assertInstanceOf('DateTime', $row->getCol_time());
		$this->assertEquals('23:59:59', $row->getCol_time()->format('H:i:s'));
		$this->assertInternalType('string', $row->getCol_string());
		$this->assertEquals('foobar', $row->getCol_string());

		$array  = array('foo' => 'bar');
		$object = new \stdClass();
		$object->foo = 'bar';

		$this->assertInternalType('array', $row->getCol_array());
		$this->assertEquals($array, $row->getCol_array());
		$this->assertInstanceOf('stdClass', $row->getCol_object());
		$this->assertEquals($object, $row->getCol_object());
	}
}

