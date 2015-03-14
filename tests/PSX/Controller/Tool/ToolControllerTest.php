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
 * ToolControllerTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ToolControllerTest extends ControllerTestCase
{
	public function testIndex()
	{
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/tool'), 'GET');
		$response = new Response();
		$response->setBody($body);

		$config     = getContainer()->get('config');
		$basePath   = rtrim(parse_url($config['psx_url'], PHP_URL_PATH), '/') . '/' . $config['psx_dispatch'];
		$controller = $this->loadController($request, $response);
		$json       = (string) $body;

		$expect = <<<JSON
{
    "paths": {
        "general": [
            {
                "title": "Routing",
                "path ": "__BASE_PATH__routing"
            },
            {
                "title": "Command",
                "path ": "__BASE_PATH__command"
            }
        ],
        "api": [
            {
                "title": "Console",
                "path ": "__BASE_PATH__rest"
            },
            {
                "title": "Documentation",
                "path ": "__BASE_PATH__doc"
            }
        ]
    },
    "current": {
        "title": "Routing",
        "path ": "__BASE_PATH__routing"
    }
}
JSON;

		$this->assertJsonStringEqualsJsonString(str_replace('__BASE_PATH__', $basePath, $expect), $json);
	}

	protected function getPaths()
	{
		return array(
			[['GET'], '/tool', 'PSX\Controller\Tool\ToolController'],
			[['GET'], '/routing', 'PSX\Controller\Tool\RoutingController'],
			[['GET', 'POST'], '/command', 'PSX\Controller\Tool\CommandController'],
			[['GET'], '/rest', 'PSX\Controller\Tool\RestController'],
			[['GET'], '/doc', 'PSX\Controller\Tool\DocumentationController::doIndex'],
			[['GET'], '/doc/:version/*path', 'PSX\Controller\Tool\DocumentationController::doDetail'],
		);
	}
}
