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

namespace PSX\Console;

use PSX\Command\Output;
use PSX\Test\CommandTestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * CommandCommandTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class CommandCommandTest extends CommandTestCase
{
	public function testCommandParameter()
	{
		$memory = new Output\Memory();

		getContainer()->set('command_output', $memory);

		$command = getContainer()->get('console')->find('command');

		$commandTester = new CommandTester($command);
		$commandTester->execute(array(
			'cmd'        => 'PSX\Command\Foo\Command\FooCommand',
			'parameters' => array('foo:test'),
		));

		$messages = $memory->getMessages();

		$this->assertEquals(2, count($messages));
		$this->assertEquals('Some foo informations', rtrim($messages[0]));
		$this->assertEquals('Hello test', rtrim($messages[1]));
	}

	/**
	 * @expectedException PSX\Command\MissingParameterException
	 */
	public function testCommandParameterEmpty()
	{
		$memory = new Output\Memory();

		getContainer()->set('command_output', $memory);

		$command = getContainer()->get('console')->find('command');

		$commandTester = new CommandTester($command);
		$commandTester->execute(array(
			'cmd'        => 'PSX\Command\Foo\Command\FooCommand',
			'parameters' => array(),
		));
	}

	public function testCommandStdin()
	{
		$memory = new Output\Memory();

		getContainer()->set('command_output', $memory);

		$stream = fopen('php://memory', 'r+');
		fwrite($stream, '{"foo": "test"}');
		rewind($stream);

		$command = getContainer()->get('console')->find('command');
		$command->setReader(new Reader\Stdin($stream));

		$commandTester = new CommandTester($command);
		$commandTester->execute(array(
			'cmd'     => 'PSX\Command\Foo\Command\FooCommand',
			'--stdin' => true,
		));

		$messages = $memory->getMessages();

		$this->assertEquals(2, count($messages));
		$this->assertEquals('Some foo informations', rtrim($messages[0]));
		$this->assertEquals('Hello test', rtrim($messages[1]));
	}

	/**
	 * @expectedException PSX\Command\MissingParameterException
	 */
	public function testCommandStdinEmptyBody()
	{
		$stream = fopen('php://memory', 'r+');
		fwrite($stream, '');
		rewind($stream);

		$command = getContainer()->get('console')->find('command');
		$command->setReader(new Reader\Stdin($stream));

		$commandTester = new CommandTester($command);
		$commandTester->execute(array(
			'cmd'     => 'PSX\Command\Foo\Command\FooCommand',
			'--stdin' => true,
		));
	}

	/**
	 * @expectedException PSX\Exception
	 */
	public function testCommandStdinInvalidJson()
	{
		$stream = fopen('php://memory', 'r+');
		fwrite($stream, 'foobar');
		rewind($stream);

		$command = getContainer()->get('console')->find('command');
		$command->setReader(new Reader\Stdin($stream));

		$commandTester = new CommandTester($command);
		$commandTester->execute(array(
			'cmd'     => 'PSX\Command\Foo\Command\FooCommand',
			'--stdin' => true,
		));
	}
}
