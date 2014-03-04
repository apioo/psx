<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2013 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Controller;

use PSX\Data\Writer;
use PSX\Data\WriterInterface;
use PSX\Data\Record;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Http\Stream\TempStream;
use PSX\Loader\Location;
use PSX\Url;
use ReflectionClass;

/**
 * ApiAbstractTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ApiAbstractTest extends ControllerTestCase
{
	public function testSetResponse()
	{
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/api'), 'GET');
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);

		$this->assertJsonStringEqualsJsonString(json_encode(array('bar' => 'foo')), (string) $response->getBody());
	}

	public function testImport()
	{
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/api'), 'POST', array('Content-Type: application/json'), json_encode(array('title' => 'foo', 'user' => 'bar')));
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);

		$this->assertJsonStringEqualsJsonString(json_encode(array('title' => 'foo', 'user' => 'bar')), (string) $response->getBody());
	}

	public function testInnerApi()
	{
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/api/inspect?format=json&fields=foo,bar&updatedSince=2014-01-26&count=8&filterBy=id&filterOp=equals&filterValue=12&sortBy=id&sortOrder=desc&startIndex=4'), 'GET');
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);
	}

	protected function getPaths()
	{
		return array(
			'/api' => 'PSX\Controller\Foo\Application\TestApiController',
		);
	}
}
