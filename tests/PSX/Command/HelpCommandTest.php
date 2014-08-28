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

namespace PSX\Command;

use PSX\Command\Executor;
use PSX\Command\ParameterParser\Map;
use PSX\Command\Output\Memory;
use PSX\Test\CommandTestCase;

/**
 * HelpCommandTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class HelpCommandTest extends CommandTestCase
{
	public function testExecute()
	{
		$output   = new Memory();
		$executor = new Executor(getContainer()->get('command_factory'), $output);
		$executor->run(new Map('PSX\Command\HelpCommand', array()));

		$this->assertEquals(array(
			"Usage:" . PHP_EOL,
			"  psx PSX\Command\HelpCommand [options]" . PHP_EOL,
			PHP_EOL,
			"Description:" . PHP_EOL,
			"  Displays informations about an command" . PHP_EOL,
			PHP_EOL,
			"Options:" . PHP_EOL,
			"  -c <value>  The name of the class or the alias" . PHP_EOL,
			PHP_EOL,
		), $output->getMessages());
	}

	public function testExecuteClass()
	{
		$output   = new Memory();
		$executor = new Executor(getContainer()->get('command_factory'), $output);
		$executor->run(new Map('PSX\Command\HelpCommand', array('c' => 'PSX\Command\ListCommand')));

		$this->assertEquals(array(
			"Usage:" . PHP_EOL,
			"  psx PSX\Command\ListCommand [options]" . PHP_EOL,
			PHP_EOL,
			"Description:" . PHP_EOL,
			"  Lists all available commands" . PHP_EOL,
			PHP_EOL,
			"Options:" . PHP_EOL,
			"  No parameters available" . PHP_EOL,
			PHP_EOL,
		), $output->getMessages());
	}
}
