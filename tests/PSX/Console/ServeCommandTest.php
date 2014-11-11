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

use Symfony\Component\Console\Tester\CommandTester;
use PSX\Test\ControllerTestCase;

/**
 * ServeCommandTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ServeCommandTest extends ControllerTestCase
{
	public function testCommand()
	{
		$stream = fopen('php://memory', 'r+');
		fwrite($stream, 'GET /api HTTP/1.1' . "\n" . 'Accept: application/xml' . "\n" . "\x04");
		rewind($stream);

		$command = getContainer()->get('console')->find('serve');
		$command->setReader(new Reader\Stdin($stream));

		ob_start();

		$commandTester = new CommandTester($command);
		$commandTester->execute(array(
			'command' => $command->getName(),
		));

		$response = ob_get_contents();

		ob_end_clean();

		$expect = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<foo>
 <bar>foo</bar>
</foo>
XML;

		$this->assertXmlStringEqualsXmlString($expect, $response);
	}

	protected function getPaths()
	{
		return array(
			[['GET'], '/api', 'PSX\Controller\Foo\Application\TestApiController::doIndex'],
		);
	}
}
