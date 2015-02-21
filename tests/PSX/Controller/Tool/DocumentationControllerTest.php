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
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
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
