<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2016 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Framework\Tests\Console\Generate;

use PSX\Framework\Test\ControllerTestCase;
use PSX\Framework\Test\Environment;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * SchemaCommandTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class SchemaCommandTest extends ControllerTestCase
{
    protected function setUp()
    {
        parent::setUp();

        if (!Environment::hasConnection()) {
            $this->markTestSkipped('Database connection not available');
        }
    }

    public function testCommand()
    {
        $command = $this->getMockBuilder('PSX\Framework\Console\Generate\SchemaCommand')
            ->setConstructorArgs(array(Environment::getService('connection')))
            ->setMethods(array('makeDir', 'writeFile', 'isDir', 'isFile'))
            ->getMock();

        $command->expects($this->at(0))
            ->method('isDir')
            ->with($this->equalTo(PSX_PATH_LIBRARY . DIRECTORY_SEPARATOR . 'Acme' . DIRECTORY_SEPARATOR . 'Foo'))
            ->will($this->returnValue(false));

        $command->expects($this->at(1))
            ->method('makeDir')
            ->with($this->equalTo(PSX_PATH_LIBRARY . DIRECTORY_SEPARATOR . 'Acme' . DIRECTORY_SEPARATOR . 'Foo'));

        $command->expects($this->at(2))
            ->method('isFile')
            ->with($this->equalTo(PSX_PATH_LIBRARY . DIRECTORY_SEPARATOR . 'Acme' . DIRECTORY_SEPARATOR . 'Foo' . DIRECTORY_SEPARATOR . 'Bar.php'))
            ->will($this->returnValue(false));

        $command->expects($this->at(3))
            ->method('writeFile')
            ->with(
                $this->equalTo(PSX_PATH_LIBRARY . DIRECTORY_SEPARATOR . 'Acme' . DIRECTORY_SEPARATOR . 'Foo' . DIRECTORY_SEPARATOR . 'Bar.php'),
                $this->callback(function ($source) {
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
     * @expectedException \RuntimeException
     */
    public function testCommandFileExists()
    {
        $command = $this->getMockBuilder('PSX\Framework\Console\Generate\SchemaCommand')
            ->setConstructorArgs(array(Environment::getService('connection')))
            ->setMethods(array('makeDir', 'writeFile', 'isDir', 'isFile'))
            ->getMock();

        $command->expects($this->at(0))
            ->method('isDir')
            ->with($this->equalTo(PSX_PATH_LIBRARY . DIRECTORY_SEPARATOR . 'Acme' . DIRECTORY_SEPARATOR . 'Foo'))
            ->will($this->returnValue(false));

        $command->expects($this->at(1))
            ->method('makeDir')
            ->with($this->equalTo(PSX_PATH_LIBRARY . DIRECTORY_SEPARATOR . 'Acme' . DIRECTORY_SEPARATOR . 'Foo'));

        $command->expects($this->at(2))
            ->method('isFile')
            ->with($this->equalTo(PSX_PATH_LIBRARY . DIRECTORY_SEPARATOR . 'Acme' . DIRECTORY_SEPARATOR . 'Foo' . DIRECTORY_SEPARATOR . 'Bar.php'))
            ->will($this->returnValue(true));

        $command->expects($this->never())
            ->method('writeFile');

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'namespace' => 'Acme\Foo\Bar',
            'table'     => 'psx_table_command_test'
        ));
    }

    public function testCommandWithoutTable()
    {
        $command = $this->getMockBuilder('PSX\Framework\Console\Generate\SchemaCommand')
            ->setConstructorArgs(array(Environment::getService('connection')))
            ->setMethods(array('makeDir', 'writeFile'))
            ->getMock();

        $command->expects($this->at(1))
            ->method('writeFile')
            ->with(
                $this->equalTo(PSX_PATH_LIBRARY . DIRECTORY_SEPARATOR . 'Acme' . DIRECTORY_SEPARATOR . 'Foo' . DIRECTORY_SEPARATOR . 'Bar.php'),
                $this->callback(function ($source) {
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
        $command = Environment::getService('console')->find('generate:schema');

        $this->assertInstanceOf('PSX\Framework\Console\Generate\SchemaCommand', $command);
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

use PSX\Schema\SchemaAbstract;

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

use PSX\Schema\SchemaAbstract;

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
