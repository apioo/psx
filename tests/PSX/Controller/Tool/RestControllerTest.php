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
 * @license http://www.apache.org/licenses/LICENSE-2.0
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
