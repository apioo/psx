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
use Symfony\Component\Console\Tester\CommandTester;

/**
 * CommandCommandTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class CommandCommandTest extends CommandTestCase
{
	public function testCommand()
	{
		$command = $this->getMockBuilder('PSX\Console\Generate\CommandCommand')
			->setConstructorArgs(array(getContainer()))
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
			'services'  => 'connection,template'
		));
	}

	/**
	 * @expectedException RuntimeException
	 */
	public function testCommandFileExists()
	{
		$command = $this->getMockBuilder('PSX\Console\Generate\CommandCommand')
			->setConstructorArgs(array(getContainer()))
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
			'services'  => 'connection,template'
		));
	}

	public function testCommandAvailable()
	{
		$command = getContainer()->get('console')->find('generate:command');

		$this->assertInstanceOf('PSX\Console\Generate\CommandCommand', $command);
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

use PSX\CommandAbstract;
use PSX\Command\Parameter;
use PSX\Command\Parameters;
use PSX\Command\OutputInterface;

/**
 * Bar
 *
 * @see http://phpsx.org/doc/design/command.html
 */
class Bar extends CommandAbstract
{
	/**
	 * @Inject
	 * @var Doctrine\DBAL\Connection
	 */
	protected $connection;

	/**
	 * @Inject
	 * @var PSX\TemplateInterface
	 */
	protected $template;

	public function onExecute(Parameters $parameters, OutputInterface $output)
	{
		$foo = $parameters->get('foo');
		$bar = $parameters->get('bar');

		// @TODO do a task

		$output->writeln('This is an PSX sample command');
	}

	public function getParameters()
	{
		return $this->getParameterBuilder()
			->setDescription('Displays informations about an foo command')
			->addOption('foo', Parameter::TYPE_REQUIRED, 'The foo parameter')
			->addOption('bar', Parameter::TYPE_OPTIONAL, 'The bar parameter')
			->getParameters();
	}
}

PHP;
	}
}

