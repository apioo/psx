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

namespace PSX\Controller\Tool;

use PSX\Command\Output;
use PSX\Http\Stream\TempStream;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Json;
use PSX\Test\ControllerTestCase;
use PSX\Url;

/**
 * CommandControllerTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class CommandControllerTest extends ControllerTestCase
{
	public function testIndex()
	{
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/command'), 'GET');
		$request->setHeader('Accept', 'application/json');
		$response = new Response();
		$response->setBody($body);

		getContainer()->get('executor')->addAlias('foo', 'PSX\Command\Foo\Command\FooCommand');

		$controller = $this->loadController($request, $response);
		$data       = Json::decode((string) $body);

		$this->assertArrayHasKey('commands', $data);
		$this->assertEquals(array('foo' => 'PSX\Command\Foo\Command\FooCommand'), $data['commands']);
	}

	public function testDetail()
	{
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/command?command=' . urlencode('PSX\Command\Foo\Command\FooCommand')), 'GET');
		$request->setHeader('Accept', 'application/json');
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);
		$data       = Json::decode((string) $body);

		$this->assertArrayHasKey('command', $data);
		$this->assertEquals('PSX\Command\Foo\Command\FooCommand', $data['command']);
		$this->assertArrayHasKey('description', $data);
		$this->assertEquals('Displays informations about an foo command', $data['description']);
		$this->assertArrayHasKey('parameters', $data);

		$expect = array(
			array(
				'name' => 'foo',
				'description' => 'The foo parameter',
				'type' => 2,
			),
			array(
				'name' => 'bar',
				'description' => 'The bar parameter',
				'type' => 1,
			)
		);

		$this->assertEquals($expect, $data['parameters']);
	}

	public function testExecute()
	{
		$request  = new Request(new Url('http://127.0.0.1/command?command=' . urlencode('PSX\Command\Foo\Command\FooCommand')), 'POST', array(), '{"foo": "bar"}');
		$request->setHeader('Content-Type', 'application/json');
		$request->setHeader('Accept', 'application/json');

		$body     = new TempStream(fopen('php://memory', 'r+'));
		$response = new Response();
		$response->setBody($body);

		$memory = new Output\Memory();
		$output = new Output\Composite(array($memory, new Output\Logger(getContainer()->get('logger'))));

		getContainer()->set('command_output', $output);

		$controller = $this->loadController($request, $response);
		$data       = Json::decode((string) $body);
		$messages   = $memory->getMessages();

		$this->assertArrayHasKey('output', $data);
		$this->assertEquals(2, count($messages));

		$lines = explode("\n", trim($data['output']));

		foreach($lines as $key => $line)
		{
			$this->assertArrayHasKey($key, $messages);
			$this->assertContains(trim($messages[$key]), $line);
		}
	}

	protected function getPaths()
	{
		return array(
			[['GET', 'POST'], '/command', 'PSX\Controller\Tool\CommandController'],
		);
	}
}
