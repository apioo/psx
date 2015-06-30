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

use PSX\Json;
use PSX\Test\ControllerTestCase;

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
		$response = $this->sendRequest('http://127.0.0.1/discovery', 'GET');
		$json     = (string) $response->getBody();

		$expect = <<<'JSON'
{
    "links": [
        {
            "rel": "api",
            "href": "http:\/\/127.0.0.1\/"
        },
        {
            "rel": "routing",
            "href": "http:\/\/127.0.0.1\/routing"
        },
        {
            "rel": "command",
            "href": "http:\/\/127.0.0.1\/command"
        },
        {
            "rel": "documentation",
            "href": "http:\/\/127.0.0.1\/doc"
        },
        {
            "rel": "raml",
            "href": "http:\/\/127.0.0.1\/raml"
        },
        {
            "rel": "wsdl",
            "href": "http:\/\/127.0.0.1\/wsdl"
        },
        {
            "rel": "swagger",
            "href": "http:\/\/127.0.0.1\/swagger"
        }
    ]
}
JSON;

		$this->assertEquals(null, $response->getStatusCode(), $json);
		$this->assertJsonStringEqualsJsonString($expect, $json, $json);
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
