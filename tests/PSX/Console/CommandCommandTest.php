<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2014 Christoph Kappestein <k42b3.x@gmail.com>
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
use Symfony\Component\Console\Tester\CommandTester;

/**
 * CommandCommandTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class CommandCommandTest extends \PHPUnit_Framework_TestCase
{
	public function testCommand()
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
			'command' => $command->getName(),
			'cmd' => 'PSX\Command\Foo\Command\FooCommand',
		));

		$messages = $memory->getMessages();

		$this->assertEquals(2, count($messages));
		$this->assertEquals('Some foo informations', rtrim($messages[0]));
		$this->assertEquals('Hello test', rtrim($messages[1]));
	}
}
