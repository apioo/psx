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

use Symfony\Component\Console\Tester\CommandTester;
use PSX\Test\ControllerTestCase;
use PSX\Dispatch\Sender\Memory;

/**
 * ServeCommandTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ServeCommandTest extends ControllerTestCase
{
	protected function setUp()
	{
		parent::setUp();

		// use memory sender
		getContainer()->set('dispatch_sender', new Memory());
	}

	public function testCommand()
	{
		$stream = fopen('php://memory', 'r+');
		fwrite($stream, 'GET /api HTTP/1.1' . "\n" . 'Accept: application/xml' . "\n" . "\x04");
		rewind($stream);

		$command = getContainer()->get('console')->find('serve');
		$command->setReader(new Reader\Stdin($stream));

		$commandTester = new CommandTester($command);
		$commandTester->execute(array(
		));

		$expect = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<foo>
 <bar>foo</bar>
</foo>
XML;

		$this->assertXmlStringEqualsXmlString($expect, getContainer()->get('dispatch_sender')->getResponse());
	}

	protected function getPaths()
	{
		return array(
			[['GET'], '/api', 'PSX\Controller\Foo\Application\TestApiController::doIndex'],
		);
	}
}
