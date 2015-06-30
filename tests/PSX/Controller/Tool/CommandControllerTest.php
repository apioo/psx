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
use PSX\Json;
use PSX\Test\ControllerTestCase;
use PSX\Test\Environment;

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
		Environment::getService('executor')->addAlias('foo', 'PSX\Command\Foo\Command\FooCommand');

		$response = $this->sendRequest('http://127.0.0.1/command', 'GET', ['Accept' => 'application/json']);
		$json     = (string) $response->getBody();

		$expect = <<<'JSON'
{
    "commands": {
        "foo": "PSX\\Command\\Foo\\Command\\FooCommand"
    }
}
JSON;

		$this->assertEquals(null, $response->getStatusCode(), $json);
		$this->assertJsonStringEqualsJsonString($expect, $json, $json);
	}

	public function testDetail()
	{
		$response = $this->sendRequest('http://127.0.0.1/command?command=' . urlencode('PSX\Command\Foo\Command\FooCommand'), 'GET', ['Accept' => 'application/json']);
		$json     = (string) $response->getBody();

		$expect = <<<'JSON'
{
    "command": "PSX\\Command\\Foo\\Command\\FooCommand",
    "description": "Displays informations about an foo command",
    "parameters": [
        {
            "name": "foo",
            "description": "The foo parameter",
            "type": 2
        },
        {
            "name": "bar",
            "description": "The bar parameter",
            "type": 1
        }
    ]
}
JSON;

		$this->assertEquals(null, $response->getStatusCode(), $json);
		$this->assertJsonStringEqualsJsonString($expect, $json, $json);
	}

	public function testExecute()
	{
		$memory = new Output\Memory();
		$output = new Output\Composite(array($memory, new Output\Logger(Environment::getService('logger'))));

		Environment::getContainer()->set('command_output', $output);

		$response = $this->sendRequest('http://127.0.0.1/command?command=' . urlencode('PSX\Command\Foo\Command\FooCommand'), 'POST', [
			'Content-Type' => 'application/json',
			'Accept'       => 'application/json',
		], '{"foo": "bar"}');
		$data     = Json::decode((string) $response->getBody());
		$messages = $memory->getMessages();

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
