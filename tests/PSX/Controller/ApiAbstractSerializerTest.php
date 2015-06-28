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

namespace PSX\Controller;

use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Http\Stream\TempStream;
use PSX\Json;
use PSX\Test\ControllerTestCase;
use PSX\Url;

/**
 * Tests the API controller in combination with the serializer. We simply return
 * some objects and serialoze them with the serializer and check whether this 
 * can be transformed in the right json format
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ApiAbstractSerializerTest extends ControllerTestCase
{
	public function testAll()
	{
		$response = $this->sendRequest('http://127.0.0.1/api', 'GET');
		$body     = (string) $response->getBody();

		$expect = <<<JSON
{
    "entry": [
        {
            "title": "foo",
            "author": {
                "name": "bar"
            },
            "contributors": [
                {
                    "name": "bar"
                },
                {
                    "name": "bar"
                }
            ],
            "tags": [
                "foo",
                "bar"
            ]
        },
        {
            "title": "bar",
            "author": {
                "name": "bar"
            },
            "contributors": [
                {
                    "name": "bar"
                },
                {
                    "name": "bar"
                }
            ],
            "tags": [
                "foo",
                "bar"
            ]
        }
    ]
}
JSON;

		$this->assertJsonStringEqualsJsonString($expect, $body, $body);
	}

	protected function getPaths()
	{
		return array(
			[['GET'], '/api', 'PSX\Controller\Foo\Application\TestApiSerializeController::doAll'],
		);
	}
}
