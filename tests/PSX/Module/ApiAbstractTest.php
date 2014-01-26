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

namespace PSX\Module;

use PSX\Data\Writer;
use PSX\Data\WriterInterface;
use PSX\Data\Record;
use PSX\Loader\Location;
use ReflectionClass;
use PSX\Http\Request;
use PSX\Url;

/**
 * ApiAbstractTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ApiAbstractTest extends ModuleTestCase
{
	protected function setUp()
	{
		parent::setUp();

		// add sepcial writer wich has no content type so no header is sent in
		// setResponse
		getContainer()->get('writerFactory')->addWriter(new NoContentTypeJsonWriter());
	}

	public function testSetResponse()
	{
		$path    = '/api';
		$request = new Request(new Url('http://127.0.0.1' . $path), 'GET');

		$controller = $this->loadModule($path, $request);

		$this->assertJsonStringEqualsJsonString($controller->processResponse(null), json_encode(array('bar' => 'foo')));
	}

	public function testImport()
	{
		$path    = '/api';
		$body    = json_encode(array('title' => 'foo', 'user' => 'bar'));
		$request = new Request(new Url('http://127.0.0.1' . $path), 'POST', array('Content-Type: application/json'), $body);

		$controller = $this->loadModule($path, $request);

		$this->assertJsonStringEqualsJsonString($controller->processResponse(null), json_encode(array('title' => 'foo', 'user' => 'bar')));
	}

	public function testInnerApi()
	{
		$path    = '/api/inspect';
		$request = new Request(new Url('http://127.0.0.1' . $path), 'GET');

		$controller = $this->loadModule($path, $request);
	}

	protected function getPaths()
	{
		return array(
			'/api' => 'PSX\Module\Foo\Application\TestApiModule',
		);
	}
}

class NoContentTypeJsonWriter extends Writer\Json
{
	public function getContentType()
	{
		return null;
	}
}
