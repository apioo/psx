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
 * @license http://www.apache.org/licenses/LICENSE-2.0
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
