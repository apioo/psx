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

use PSX\Loader\Location;
use PSX\Loader\LocationFinder\CallbackMethod;
use PSX\Loader\InvalidPathException;
use PSX\Http\Stream\TempStream;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Url;
use ReflectionClass;

/**
 * ViewAbstractTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ViewAbstractTest extends ControllerTestCase
{
	public function testAutomaticTemplateDetection()
	{
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/view'), 'GET');
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);
		$response   = simplexml_load_string((string) $body);

		$render = (float) $response->render;
		$base   = getContainer()->get('base');
		$config = getContainer()->get('config');

		$this->assertEquals('bar', $response->foo);
		$this->assertTrue(!empty($response->self));
		$this->assertEquals($config['psx_url'] . '/' . $config['psx_dispatch'], $response->url);
		$this->assertEquals($base->getPath(), $response->base);
		$this->assertTrue($render > 0);
		$this->assertEquals('tests/PSX/Controller/Foo/Resource', $response->location);
	}

	public function testImplicitTemplate()
	{
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/view/detail'), 'GET');
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);
		$response   = simplexml_load_string((string) $body);

		$render = (float) $response->render;
		$base   = getContainer()->get('base');
		$config = getContainer()->get('config');

		$this->assertEquals('bar', $response->foo);
		$this->assertTrue(!empty($response->self));
		$this->assertEquals($config['psx_url'] . '/' . $config['psx_dispatch'], $response->url);
		$this->assertEquals($base->getPath(), $response->base);
		$this->assertTrue($render > 0);
		$this->assertEquals('tests/PSX/Controller/Foo/Resource', $response->location);
	}

	public function testExplicitTemplate()
	{
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/view/explicit'), 'GET');
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);
		$response   = simplexml_load_string((string) $body);

		$render = (float) $response->render;
		$base   = getContainer()->get('base');
		$config = getContainer()->get('config');

		$this->assertEquals('bar', $response->foo);
		$this->assertTrue(!empty($response->self));
		$this->assertEquals($config['psx_url'] . '/' . $config['psx_dispatch'], $response->url);
		$this->assertEquals($base->getPath(), $response->base);
		$this->assertTrue($render > 0);
		$this->assertEquals('tests/PSX/Controller/Foo/Resource', $response->location);
	}

	protected function getPaths()
	{
		return array(
			'/view' => 'PSX\Controller\Foo\Application\TestViewController',
		);
	}
}
