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

namespace PSX\Console\Generate;

use Doctrine\DBAL\Types\Type;
use PSX\Test\CommandTestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * TableCommandTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class TableCommandTest extends CommandTestCase
{
	protected function setUp()
	{
		parent::setUp();

		if(!hasConnection())
		{
			$this->markTestSkipped('Database connection not available');
		}
	}

	public function testCommand()
	{
		$command = $this->getMockBuilder('PSX\Console\Generate\TableCommand')
			->setConstructorArgs(array(getContainer()->get('connection')))
			->setMethods(array('makeDir', 'writeFile', 'isDir', 'isFile'))
			->getMock();

		$command->expects($this->once())
			->method('isDir')
			->with($this->equalTo('library' . DIRECTORY_SEPARATOR . 'Acme' . DIRECTORY_SEPARATOR . 'Foo'))
			->will($this->returnValue(false));

		$command->expects($this->once())
			->method('makeDir')
			->with($this->equalTo('library' . DIRECTORY_SEPARATOR . 'Acme' . DIRECTORY_SEPARATOR . 'Foo'));

		$command->expects($this->once())
			->method('isFile')
			->with($this->equalTo('library' . DIRECTORY_SEPARATOR . 'Acme' . DIRECTORY_SEPARATOR . 'Foo' . DIRECTORY_SEPARATOR . 'Bar.php'))
			->will($this->returnValue(false));

		$command->expects($this->once())
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
			->setConstructorArgs(array(getContainer()->get('connection')))
			->setMethods(array('makeDir', 'writeFile', 'isDir', 'isFile'))
			->getMock();

		$command->expects($this->once())
			->method('isDir')
			->with($this->equalTo('library' . DIRECTORY_SEPARATOR . 'Acme' . DIRECTORY_SEPARATOR . 'Foo'))
			->will($this->returnValue(false));

		$command->expects($this->once())
			->method('makeDir')
			->with($this->equalTo('library' . DIRECTORY_SEPARATOR . 'Acme' . DIRECTORY_SEPARATOR . 'Foo'));

		$command->expects($this->once())
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
		$command = getContainer()->get('console')->find('generate:table');

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

