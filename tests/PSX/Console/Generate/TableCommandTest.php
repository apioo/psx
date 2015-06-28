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

namespace PSX\Console\Generate;

use PSX\Test\CommandTestCase;
use PSX\Test\Environment;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * TableCommandTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class TableCommandTest extends CommandTestCase
{
	protected function setUp()
	{
		parent::setUp();

		if(!Environment::hasConnection())
		{
			$this->markTestSkipped('Database connection not available');
		}
	}

	public function testCommand()
	{
		$command = $this->getMockBuilder('PSX\Console\Generate\TableCommand')
			->setConstructorArgs(array(Environment::getService('connection')))
			->setMethods(array('makeDir', 'writeFile', 'isDir', 'isFile'))
			->getMock();

		$command->expects($this->at(0))
			->method('isDir')
			->with($this->equalTo('library' . DIRECTORY_SEPARATOR . 'Acme' . DIRECTORY_SEPARATOR . 'Foo'))
			->will($this->returnValue(false));

		$command->expects($this->at(1))
			->method('makeDir')
			->with($this->equalTo('library' . DIRECTORY_SEPARATOR . 'Acme' . DIRECTORY_SEPARATOR . 'Foo'));

		$command->expects($this->at(2))
			->method('isFile')
			->with($this->equalTo('library' . DIRECTORY_SEPARATOR . 'Acme' . DIRECTORY_SEPARATOR . 'Foo' . DIRECTORY_SEPARATOR . 'Bar.php'))
			->will($this->returnValue(false));

		$command->expects($this->at(3))
			->method('writeFile')
			->with(
				$this->equalTo('library' . DIRECTORY_SEPARATOR . 'Acme' . DIRECTORY_SEPARATOR . 'Foo' . DIRECTORY_SEPARATOR . 'Bar.php'), 
				$this->callback(function($source){
					$this->assertSource($this->getExpectedSource(), $source);
					return true;
				})
			);

		$commandTester = new CommandTester($command);
		$commandTester->execute(array(
			'namespace' => 'Acme\Foo\Bar',
			'table'     => 'psx_table_command_test'
		));
	}

	/**
	 * @expectedException RuntimeException
	 */
	public function testCommandFileExists()
	{
		$command = $this->getMockBuilder('PSX\Console\Generate\TableCommand')
			->setConstructorArgs(array(Environment::getService('connection')))
			->setMethods(array('makeDir', 'writeFile', 'isDir', 'isFile'))
			->getMock();

		$command->expects($this->at(0))
			->method('isDir')
			->with($this->equalTo('library' . DIRECTORY_SEPARATOR . 'Acme' . DIRECTORY_SEPARATOR . 'Foo'))
			->will($this->returnValue(false));

		$command->expects($this->at(1))
			->method('makeDir')
			->with($this->equalTo('library' . DIRECTORY_SEPARATOR . 'Acme' . DIRECTORY_SEPARATOR . 'Foo'));

		$command->expects($this->at(2))
			->method('isFile')
			->with($this->equalTo('library' . DIRECTORY_SEPARATOR . 'Acme' . DIRECTORY_SEPARATOR . 'Foo' . DIRECTORY_SEPARATOR . 'Bar.php'))
			->will($this->returnValue(true));

		$command->expects($this->never())
			->method('writeFile');

		$commandTester = new CommandTester($command);
		$commandTester->execute(array(
			'namespace' => 'Acme\Foo\Bar',
			'table'     => 'psx_table_command_test'
		));
	}

	public function testCommandAvailable()
	{
		$command = Environment::getService('console')->find('generate:table');

		$this->assertInstanceOf('PSX\Console\Generate\TableCommand', $command);
	}

	protected function assertSource($expect, $actual)
	{
		$expect = str_replace(array("\r\n", "\n", "\r"), "\n", $expect);
		$actual = str_replace(array("\r\n", "\n", "\r"), "\n", $actual);

		$this->assertEquals($expect, $actual);
	}

	protected function getExpectedSource()
	{
		return <<<'PHP'
<?php

namespace Acme\Foo;

use PSX\Sql\TableAbstract;

/**
 * Bar
 *
 * @see http://phpsx.org/doc/concept/table.html
 */
class Bar extends TableAbstract
{
	public function getName()
	{
		return 'psx_table_command_test';
	}

	public function getColumns()
	{
		return array(
			'id' => self::TYPE_INT | self::AUTO_INCREMENT | self::PRIMARY_KEY,
			'col_bigint' => self::TYPE_BIGINT,
			'col_blob' => self::TYPE_BLOB,
			'col_boolean' => self::TYPE_BOOLEAN,
			'col_datetime' => self::TYPE_DATETIME,
			'col_datetimetz' => self::TYPE_DATETIME,
			'col_date' => self::TYPE_DATE,
			'col_decimal' => self::TYPE_DECIMAL,
			'col_float' => self::TYPE_FLOAT,
			'col_integer' => self::TYPE_INT,
			'col_smallint' => self::TYPE_SMALLINT,
			'col_text' => self::TYPE_TEXT,
			'col_time' => self::TYPE_TIME,
			'col_string' => self::TYPE_VARCHAR,
			'col_array' => self::TYPE_TEXT,
			'col_object' => self::TYPE_TEXT,
		);
	}
}

PHP;
	}
}

