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
        $response = $this->sendRequest('http://127.0.0.1/swagger', 'GET', ['Accept' => 'application/json']);
        $json     = (string) $response->getBody();

        $expect = <<<'JSON'
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

        $this->assertEquals(null, $response->getStatusCode(), $json);
        $this->assertEquals('application/json', $response->getHeader('Content-Type'), $json);
        $this->assertJsonStringEqualsJsonString($expect, $json, $json);
    }

    public function testDetail()
    {
        $response = $this->sendRequest('http://127.0.0.1/swagger/1/api', 'GET', ['Accept' => 'application/json']);
        $json     = (string) $response->getBody();

        $expect = <<<'JSON'
{
    "swaggerVersion": "1.2",
    "apiVersion": "1",
    "basePath": "http:\/\/127.0.0.1\/",
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
                            "message": "200 response",
                            "responseModel": "GET-200-response"
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
                            "type": "POST-request"
                        }
                    ],
                    "responseMessages": [
                        {
                            "code": 201,
                            "message": "201 response",
                            "responseModel": "POST-201-response"
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
                            "type": "PUT-request"
                        }
                    ],
                    "responseMessages": [
                        {
                            "code": 200,
                            "message": "200 response",
                            "responseModel": "PUT-200-response"
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
                            "type": "DELETE-request"
                        }
                    ],
                    "responseMessages": [
                        {
                            "code": 200,
                            "message": "200 response",
                            "responseModel": "DELETE-200-response"
                        }
                    ]
                },
                {
                    "method": "PATCH",
                    "nickname": "patchItem",
                    "parameters": [
                        {
                            "paramType": "body",
                            "name": "body",
                            "required": true,
                            "type": "PATCH-request"
                        }
                    ],
                    "responseMessages": [
                        {
                            "code": 200,
                            "message": "200 response",
                            "responseModel": "PATCH-200-response"
                        }
                    ]
                }
            ]
        }
    ],
    "models": {
        "ref993f4bb37f524889fc963fedd6381458": {
            "id": "ref993f4bb37f524889fc963fedd6381458",
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
        "refe80c8b9e68244cea3401d3b7aff00733": {
            "id": "refe80c8b9e68244cea3401d3b7aff00733",
            "properties": {
                "entry": {
                    "type": "array",
                    "items": {
                        "$ref": "ref993f4bb37f524889fc963fedd6381458"
                    },
                    "title": "entry"
                }
            }
        },
        "ref3934915b538d8557d87031925d29ac0d": {
            "id": "ref3934915b538d8557d87031925d29ac0d",
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
        "ref3a0bf597c698b671859e2c0ca2640825": {
            "id": "ref3a0bf597c698b671859e2c0ca2640825",
            "properties": {
                "success": {
                    "type": "boolean"
                },
                "message": {
                    "type": "string"
                }
            }
        },
        "ref3368bc12f3927997f38dc1bea49554be": {
            "id": "ref3368bc12f3927997f38dc1bea49554be",
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
        "GET-200-response": {
            "id": "GET-200-response",
            "properties": {
                "entry": {
                    "type": "array",
                    "items": {
                        "$ref": "ref993f4bb37f524889fc963fedd6381458"
                    },
                    "title": "entry"
                }
            }
        },
        "POST-request": {
            "id": "POST-request",
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
        "POST-201-response": {
            "id": "POST-201-response",
            "properties": {
                "success": {
                    "type": "boolean"
                },
                "message": {
                    "type": "string"
                }
            }
        },
        "PUT-request": {
            "id": "PUT-request",
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
        "PUT-200-response": {
            "id": "PUT-200-response",
            "properties": {
                "success": {
                    "type": "boolean"
                },
                "message": {
                    "type": "string"
                }
            }
        },
        "DELETE-request": {
            "id": "DELETE-request",
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
        "DELETE-200-response": {
            "id": "DELETE-200-response",
            "properties": {
                "success": {
                    "type": "boolean"
                },
                "message": {
                    "type": "string"
                }
            }
        },
        "PATCH-request": {
            "id": "PATCH-request",
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
        "PATCH-200-response": {
            "id": "PATCH-200-response",
            "properties": {
                "success": {
                    "type": "boolean"
                },
                "message": {
                    "type": "string"
                }
            }
        }
    }
}
JSON;

        $this->assertEquals(null, $response->getStatusCode(), $json);
        $this->assertEquals('application/json', $response->getHeader('Content-Type'), $json);
        $this->assertJsonStringEqualsJsonString($expect, $json, $json);
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
