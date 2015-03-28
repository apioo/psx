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

use PSX\Http\Stream\TempStream;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Json;
use PSX\Test\ControllerTestCase;
use PSX\Url;

/**
 * SwaggerControllerTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class SwaggerControllerTest extends ControllerTestCase
{
	public function testIndex()
	{
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/swagger'), 'GET');
		$request->addHeader('Accept', 'application/json');
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);

		$this->assertEquals('application/json', $response->getHeader('Content-Type'));

		$expect = <<<JSON
{
    "swaggerVersion": "1.2",
    "apiVersion": "1.0",
    "apis": [
        {
            "path": "\/*\/api"
        }
    ]
}
JSON;

		$this->assertJsonStringEqualsJsonString($expect, (string) $body);
	}

	public function testDetail()
	{
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/swagger/1/api'), 'GET');
		$request->addHeader('Accept', 'application/json');
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);

		$this->assertEquals('application/json', $response->getHeader('Content-Type'));

		$config   = getContainer()->getConfig();
		$basePath = $config['psx_url'] . '/' . $config['psx_dispatch'];
		$expect   = <<<JSON
{
    "swaggerVersion": "1.2",
    "apiVersion": 1,
    "basePath": "{$basePath}",
    "resourcePath": "\/api",
    "apis": [
        {
            "path": "\/api",
            "operations": [
                {
                    "method": "GET",
                    "nickname": "getCollection",
                    "parameters": [],
                    "responseMessages": [
                        {
                            "code": 200,
                            "message": "Response",
                            "responseModel": "getResponse"
                        }
                    ]
                },
                {
                    "method": "POST",
                    "nickname": "postItem",
                    "parameters": [
                        {
                            "paramType": "body",
                            "name": "body",
                            "required": true,
                            "type": "postRequest"
                        }
                    ],
                    "responseMessages": [
                        {
                            "code": 200,
                            "message": "Response",
                            "responseModel": "postResponse"
                        }
                    ]
                },
                {
                    "method": "PUT",
                    "nickname": "putItem",
                    "parameters": [
                        {
                            "paramType": "body",
                            "name": "body",
                            "required": true,
                            "type": "putRequest"
                        }
                    ],
                    "responseMessages": [
                        {
                            "code": 200,
                            "message": "Response",
                            "responseModel": "putResponse"
                        }
                    ]
                },
                {
                    "method": "DELETE",
                    "nickname": "deleteItem",
                    "parameters": [
                        {
                            "paramType": "body",
                            "name": "body",
                            "required": true,
                            "type": "deleteRequest"
                        }
                    ],
                    "responseMessages": [
                        {
                            "code": 200,
                            "message": "Response",
                            "responseModel": "deleteResponse"
                        }
                    ]
                }
            ]
        }
    ],
    "models": {
        "getResponse": {
            "id": "getResponse",
            "properties": {
                "entry": {
                    "type": "array",
                    "items": {
                        "\$ref": "ref7738db4616810154ab42db61b65f74aa"
                    }
                }
            }
        },
        "postRequest": {
            "id": "postRequest",
            "required": [
                "title",
                "date"
            ],
            "properties": {
                "id": {
                    "type": "integer"
                },
                "userId": {
                    "type": "integer"
                },
                "title": {
                    "type": "string",
                    "minLength": 3,
                    "maxLength": 16,
                    "pattern": "[A-z]+"
                },
                "date": {
                    "type": "string"
                }
            }
        },
        "postResponse": {
            "id": "postResponse",
            "properties": {
                "success": {
                    "type": "boolean"
                },
                "message": {
                    "type": "string"
                }
            }
        },
        "putRequest": {
            "id": "putRequest",
            "required": [
                "id"
            ],
            "properties": {
                "id": {
                    "type": "integer"
                },
                "userId": {
                    "type": "integer"
                },
                "title": {
                    "type": "string",
                    "minLength": 3,
                    "maxLength": 16,
                    "pattern": "[A-z]+"
                },
                "date": {
                    "type": "string"
                }
            }
        },
        "putResponse": {
            "id": "putResponse",
            "properties": {
                "success": {
                    "type": "boolean"
                },
                "message": {
                    "type": "string"
                }
            }
        },
        "deleteRequest": {
            "id": "deleteRequest",
            "required": [
                "id"
            ],
            "properties": {
                "id": {
                    "type": "integer"
                },
                "userId": {
                    "type": "integer"
                },
                "title": {
                    "type": "string",
                    "minLength": 3,
                    "maxLength": 16,
                    "pattern": "[A-z]+"
                },
                "date": {
                    "type": "string"
                }
            }
        },
        "deleteResponse": {
            "id": "deleteResponse",
            "properties": {
                "success": {
                    "type": "boolean"
                },
                "message": {
                    "type": "string"
                }
            }
        },
        "ref7738db4616810154ab42db61b65f74aa": {
            "id": "ref7738db4616810154ab42db61b65f74aa",
            "properties": {
                "id": {
                    "type": "integer"
                },
                "userId": {
                    "type": "integer"
                },
                "title": {
                    "type": "string",
                    "minLength": 3,
                    "maxLength": 16,
                    "pattern": "[A-z]+"
                },
                "date": {
                    "type": "string"
                }
            }
        }
    }
}
JSON;

		$this->assertJsonStringEqualsJsonString($expect, (string) $body);
	}

	protected function getPaths()
	{
		return array(
			[['GET'], '/swagger', 'PSX\Controller\Tool\SwaggerGeneratorController::doIndex'],
			[['GET'], '/swagger/:version/*path', 'PSX\Controller\Tool\SwaggerGeneratorController::doDetail'],
			[['GET', 'POST', 'PUT', 'DELETE'], '/api', 'PSX\Controller\Foo\Application\TestSchemaApiController'],
		);
	}
}
