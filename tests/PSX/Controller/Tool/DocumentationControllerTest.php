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

use PSX\Http\Stream\TempStream;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Json;
use PSX\Test\ControllerTestCase;
use PSX\Url;

/**
 * DocumentationControllerTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class DocumentationControllerTest extends ControllerTestCase
{
	public function testIndex()
	{
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/doc'), 'GET');
		$request->addHeader('Accept', 'application/json');
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);
		$data       = Json::decode((string) $body);

		$this->assertArrayHasKey('routings', $data);

		$routing = current($data['routings']);

		$this->assertEquals('/api', $routing['path']);
		$this->assertEquals(1, $routing['version']);

		$this->assertArrayHasKey('links', $data);

		$links = $data['links'];

		$this->assertTrue(is_array($links));
		$this->assertEquals(2, count($links));
		$this->assertArrayHasKey('rel', $links[0]);
		$this->assertArrayHasKey('href', $links[0]);
		$this->assertEquals('self', $links[0]['rel']);
		$this->assertEquals('/doc', substr($links[0]['href'], -4));
		$this->assertArrayHasKey('rel', $links[1]);
		$this->assertArrayHasKey('href', $links[1]);
		$this->assertEquals('detail', $links[1]['rel']);
		$this->assertEquals('/doc/{version}/{path}', substr($links[1]['href'], -21));
	}

	public function testDetail()
	{
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/doc/1/api'), 'GET');
		$request->addHeader('Accept', 'application/json');
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);
		$data       = Json::decode((string) $body);

		$this->assertArrayHasKey('method', $data);
		$this->assertEquals(array('GET', 'POST', 'PUT', 'DELETE'), $data['method']);
		$this->assertArrayHasKey('path', $data);
		$this->assertEquals('/api', $data['path']);
		$this->assertArrayHasKey('controller', $data);
		$this->assertEquals('PSX\Controller\Foo\Application\TestSchemaApiController', $data['controller']);
		$this->assertArrayHasKey('versions', $data);
		$this->assertArrayHasKey('see_others', $data);
		$this->assertArrayHasKey('view', $data);

		$version = current($data['versions']);

		$this->assertArrayHasKey('version', $version);
		$this->assertEquals(1, $version['version']);
		$this->assertArrayHasKey('status', $version);
		$this->assertEquals(0, $version['status']);

		$view = $data['view'];

		$this->assertArrayHasKey('version', $view);
		$this->assertEquals(1, $view['version']);
		$this->assertArrayHasKey('status', $view);
		$this->assertEquals(0, $view['status']);
		$this->assertArrayHasKey('data', $view);

		$data = $view['data'];

		$this->assertArrayHasKey('Schema', $data);

		$schema = $data['Schema'];

		$this->assertArrayHasKey('GET', $schema);
		$this->assertArrayHasKey('POST', $schema);
		$this->assertArrayHasKey('PUT', $schema);
		$this->assertArrayHasKey('DELETE', $schema);
	}

	protected function getPaths()
	{
		return array(
			[['GET'], '/doc', 'PSX\Controller\Tool\DocumentationController::doIndex'],
			[['GET'], '/doc/:version/*path', 'PSX\Controller\Tool\DocumentationController::doDetail'],
			[['GET', 'POST', 'PUT', 'DELETE'], '/api', 'PSX\Controller\Foo\Application\TestSchemaApiController'],
		);
	}
}
