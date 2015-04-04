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
 * DiscoveryControllerTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class DiscoveryControllerTest extends ControllerTestCase
{
	public function testIndex()
	{
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/discovery'), 'GET');
		$response = new Response();
		$response->setBody($body);

		$config     = getContainer()->get('config');
		$basePath   = $config['psx_url'] . '/' . $config['psx_dispatch'];
		$controller = $this->loadController($request, $response);
		$json       = (string) $body;

		$expect = <<<JSON
{
    "links": [
        {
            "rel": "api",
            "href": "__BASE_PATH__"
        },
        {
            "rel": "routing",
            "href": "__BASE_PATH__routing"
        },
        {
            "rel": "command",
            "href": "__BASE_PATH__command"
        },
        {
            "rel": "documentation",
            "href": "__BASE_PATH__doc"
        },
        {
            "rel": "raml",
            "href": "__BASE_PATH__raml"
        },
        {
            "rel": "wsdl",
            "href": "__BASE_PATH__wsdl"
        },
        {
            "rel": "swagger",
            "href": "__BASE_PATH__swagger"
        }
    ]
}
JSON;

		$this->assertJsonStringEqualsJsonString(str_replace('__BASE_PATH__', $basePath, $expect), $json);
	}

	protected function getPaths()
	{
		return array(
			[['GET'], '/discovery', 'PSX\Controller\Tool\DiscoveryController'],
			[['GET'], '/routing', 'PSX\Controller\Tool\RoutingController'],
			[['GET', 'POST'], '/command', 'PSX\Controller\Tool\CommandController'],
			[['GET'], '/rest_client', 'PSX\Controller\Tool\RestClientController'],
			[['GET'], '/doc', 'PSX\Controller\Tool\DocumentationController::doIndex'],
			[['GET'], '/doc/:version/*path', 'PSX\Controller\Tool\DocumentationController::doDetail'],
            [['GET'], '/raml', 'PSX\Controller\Tool\RamlGeneratorController'],
            [['GET'], '/wsdl', 'PSX\Controller\Tool\WsdlGeneratorController'],
            [['GET'], '/swagger', 'PSX\Controller\Tool\SwaggerGeneratorController::doDetail'],
		);
	}
}
