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

use PSX\Test\CommandTestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * SchemaCommandTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class SchemaCommandTest extends CommandTestCase
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
		$command = $this->getMockBuilder('PSX\Console\Generate\SchemaCommand')
			->setConstructorArgs(array(getContainer()->get('connection')))
			->setMethods(array('makeDir', 'writeFile'))
			->getMock();

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

	public function testCommandWithoutTable()
	{
		$command = $this->getMockBuilder('PSX\Console\Generate\SchemaCommand')
			->setConstructorArgs(array(getContainer()->get('connection')))
			->setMethods(array('makeDir', 'writeFile'))
			->getMock();

		$command->expects($this->once())
			->method('writeFile')
			->with(
				$this->equalTo('library' . DIRECTORY_SEPARATOR . 'Acme' . DIRECTORY_SEPARATOR . 'Foo' . DIRECTORY_SEPARATOR . 'Bar.php'), 
				$this->callback(function($source){
					$this->assertSource($this->getExpectedSourceNoTable(), $source);
					return true;
				})
			);

		$commandTester = new CommandTester($command);
		$commandTester->execute(array(
			'namespace' => 'Acme\Foo\Bar',
		));
	}

	public function testCommandAvailable()
	{
		$command = getContainer()->get('console')->find('generate:schema');

		$this->assertInstanceOf('PSX\Console\Generate\SchemaCommand', $command);
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

use PSX\Data\SchemaAbstract;

/**
 * Bar
 *
 * @see http://phpsx.org/doc/concept/schema.html
 */
class Bar extends SchemaAbstract
{
	public function getDefinition()
	{
		$sb = $this->getSchemaBuilder('bar');
		$sb->integer('id');
		$sb->integer('col_bigint');
		$sb->string('col_blob');
		$sb->boolean('col_boolean');
		$sb->dateTime('col_datetime');
		$sb->dateTime('col_datetimetz');
		$sb->string('col_date');
		$sb->float('col_decimal');
		$sb->float('col_float');
		$sb->integer('col_integer');
		$sb->integer('col_smallint');
		$sb->string('col_text');
		$sb->string('col_time');
		$sb->string('col_string');
		$sb->string('col_array');
		$sb->string('col_object');

		return $sb->getProperty();
	}
}

PHP;
	}

	protected function getExpectedSourceNoTable()
	{
		return <<<'PHP'
<?php

namespace Acme\Foo;

use PSX\Data\SchemaAbstract;

/**
 * Bar
 *
 * @see http://phpsx.org/doc/concept/schema.html
 */
class Bar extends SchemaAbstract
{
	public function getDefinition()
	{
		$sb = $this->getSchemaBuilder('bar');
		$sb->integer('id');
		$sb->string('title');
		$sb->dateTime('date');

		return $sb->getProperty();
	}
}

PHP;
	}
}

