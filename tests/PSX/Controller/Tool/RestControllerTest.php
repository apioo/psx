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

use DOMDocument;
use PSX\Http\Stream\TempStream;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Json;
use PSX\Test\ControllerTestCase;
use PSX\Url;

/**
 * RestControllerTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class RestControllerTest extends ControllerTestCase
{
	/**
	 * The rest console is only an html template which makes AJAX to api 
	 * endpoints
	 */
	public function testIndex()
	{
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/rest'), 'GET');
		$response = new Response();
		$response->setBody($body);

		$config     = getContainer()->get('config');
		$basePath   = rtrim(parse_url($config['psx_url'], PHP_URL_PATH), '/') . '/' . $config['psx_dispatch'];
		$controller = $this->loadController($request, $response);
		$json       = (string) $body;

		$expect = array(
			'links' => array(
				array(
					'rel'  => 'self',
					'href' => $basePath . 'rest',
				),
				array(
					'rel'  => 'router',
					'href' => $basePath . 'routing',
				)
			),
		);

		$this->assertJsonStringEqualsJsonString(json_encode($expect), $json);
	}

	public function testIndexHtml()
	{
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/rest'), 'GET', array('Accept' => 'text/html'));
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);
		$html       = (string) $body;

		$this->assertTrue(strlen($html) > 2048);
	}

	protected function getPaths()
	{
		return array(
			[['GET'], '/rest', 'PSX\Controller\Tool\RestController'],
			[['GET'], '/routing', 'PSX\Controller\Tool\RoutingController'],
		);
	}
}
