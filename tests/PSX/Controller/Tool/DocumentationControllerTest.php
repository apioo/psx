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

		$this->assertEquals(array('GET', 'POST', 'PUT', 'DELETE'), $routing['method']);
		$this->assertEquals('/api', $routing['path']);
		$this->assertEquals('PSX\Controller\Foo\Application\TestSchemaApiController', $routing['controller']);
	}

	public function testDetail()
	{
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/doc?path=' . urlencode('/api')), 'GET');
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

		$version = current($data['versions']);

		$this->assertArrayHasKey('version', $version);
		$this->assertEquals(1, $version['version']);
		$this->assertArrayHasKey('status', $version);
		$this->assertEquals(0, $version['status']);
		$this->assertArrayHasKey('view', $version);

		$view = $version['view'];

		$this->assertArrayHasKey('get_response', $view);
		$this->assertArrayHasKey('post_request', $view);
		$this->assertArrayHasKey('post_response', $view);
		$this->assertArrayHasKey('put_request', $view);
		$this->assertArrayHasKey('put_response', $view);
		$this->assertArrayHasKey('delete_request', $view);
		$this->assertArrayHasKey('delete_response', $view);
	}

	public function testExportXsd()
	{
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/doc?path=' . urlencode('/api') . '&export=1&version=1&method=GET&type=1'), 'GET');
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);

		$dom = new \DOMDocument();
		$dom->loadXML((string) $body);

		$elements = $dom->getElementsByTagNameNS('http://www.w3.org/2001/XMLSchema', 'element');

		$this->assertEquals('collection', $elements->item(0)->getAttribute('name'));
		$this->assertEquals('entry', $elements->item(1)->getAttribute('name'));
		$this->assertEquals('id', $elements->item(2)->getAttribute('name'));
	}

	public function testExportJsonSchema()
	{
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/doc?path=' . urlencode('/api') . '&export=2&version=1&method=GET&type=1'), 'GET');
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);

		$data = Json::decode((string) $body);

		$this->assertEquals('entry', key($data['properties']));
	}

	protected function getPaths()
	{
		return array(
			[['GET'], '/doc', 'PSX\Controller\Tool\DocumentationController'],
			[['GET', 'POST', 'PUT', 'DELETE'], '/api', 'PSX\Controller\Foo\Application\TestSchemaApiController'],
		);
	}
}
