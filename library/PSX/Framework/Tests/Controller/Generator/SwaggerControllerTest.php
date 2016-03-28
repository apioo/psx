<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2016 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Framework\Tests\Controller\Generator;

use PSX\Framework\Test\ControllerTestCase;

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
    "apiVersion": 1,
    "basePath": "http:\/\/127.0.0.1\/",
    "resourcePath": "\/api",
    "apis": [
        {
            "path": "\/api",
            "description": "lorem ipsum",
            "operations": [
                {
                    "method": "GET",
                    "summary": "Returns a collection",
                    "nickname": "getCollection",
                    "parameters": [
                        {
                            "paramType": "path",
                            "name": "name",
                            "description": "Name parameter",
                            "required": false,
                            "type": "string",
                            "minimum": 0,
                            "maximum": 16
                        },
                        {
                            "paramType": "path",
                            "name": "type",
                            "type": "string",
                            "enum": [
                                "foo",
                                "bar"
                            ]
                        },
                        {
                            "paramType": "query",
                            "name": "startIndex",
                            "description": "startIndex parameter",
                            "required": false,
                            "type": "integer",
                            "minimum": 0,
                            "maximum": 32
                        },
                        {
                            "paramType": "query",
                            "name": "float",
                            "type": "number"
                        },
                        {
                            "paramType": "query",
                            "name": "boolean",
                            "type": "boolean"
                        },
                        {
                            "paramType": "query",
                            "name": "date",
                            "type": "string",
                            "format": "date"
                        },
                        {
                            "paramType": "query",
                            "name": "datetime",
                            "type": "string",
                            "format": "date-time"
                        }
                    ],
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
                            "paramType": "path",
                            "name": "name",
                            "description": "Name parameter",
                            "required": false,
                            "type": "string",
                            "minimum": 0,
                            "maximum": 16
                        },
                        {
                            "paramType": "path",
                            "name": "type",
                            "type": "string",
                            "enum": [
                                "foo",
                                "bar"
                            ]
                        },
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
                            "paramType": "path",
                            "name": "name",
                            "description": "Name parameter",
                            "required": false,
                            "type": "string",
                            "minimum": 0,
                            "maximum": 16
                        },
                        {
                            "paramType": "path",
                            "name": "type",
                            "type": "string",
                            "enum": [
                                "foo",
                                "bar"
                            ]
                        },
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
                            "paramType": "path",
                            "name": "name",
                            "description": "Name parameter",
                            "required": false,
                            "type": "string",
                            "minimum": 0,
                            "maximum": 16
                        },
                        {
                            "paramType": "path",
                            "name": "type",
                            "type": "string",
                            "enum": [
                                "foo",
                                "bar"
                            ]
                        },
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
                            "paramType": "path",
                            "name": "name",
                            "description": "Name parameter",
                            "required": false,
                            "type": "string",
                            "minimum": 0,
                            "maximum": 16
                        },
                        {
                            "paramType": "path",
                            "name": "type",
                            "type": "string",
                            "enum": [
                                "foo",
                                "bar"
                            ]
                        },
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
        "ref324d9c87eb6ee494de5207f005abddb8": {
            "id": "ref324d9c87eb6ee494de5207f005abddb8",
            "properties": {
                "name": {
                    "type": "string",
                    "description": "Name parameter",
                    "maxLength": 16,
                    "pattern": "[A-z]+"
                },
                "type": {
                    "type": "string",
                    "enum": [
                        "foo",
                        "bar"
                    ]
                }
            }
        },
        "ref85f5cb99d4cb24e97943e04989396c8e": {
            "id": "ref85f5cb99d4cb24e97943e04989396c8e",
            "properties": {
                "startIndex": {
                    "type": "integer",
                    "description": "startIndex parameter",
                    "maximum": 32
                },
                "float": {
                    "type": "number"
                },
                "boolean": {
                    "type": "boolean"
                },
                "date": {
                    "type": "string"
                },
                "datetime": {
                    "type": "string"
                }
            }
        },
        "ref7bde1c36c5f13fd4cf10c2864f8e8a75": {
            "id": "ref7bde1c36c5f13fd4cf10c2864f8e8a75",
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
        "refae7d4b5627a9dbac0c99945ecef66e17": {
            "id": "refae7d4b5627a9dbac0c99945ecef66e17",
            "properties": {
                "entry": {
                    "type": "array",
                    "items": {
                        "$ref": "ref7bde1c36c5f13fd4cf10c2864f8e8a75"
                    },
                    "title": "entry"
                }
            }
        },
        "ref70152cdfc48a8a3969f10e9e4fe3b239": {
            "id": "ref70152cdfc48a8a3969f10e9e4fe3b239",
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
        "ref31ead4d236fd038a7d55a40e2ca1171e": {
            "id": "ref31ead4d236fd038a7d55a40e2ca1171e",
            "properties": {
                "success": {
                    "type": "boolean"
                },
                "message": {
                    "type": "string"
                }
            }
        },
        "ref774a7a4ece700fad7bb605e81c61fea7": {
            "id": "ref774a7a4ece700fad7bb605e81c61fea7",
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
        "path": {
            "id": "path",
            "properties": {
                "name": {
                    "type": "string",
                    "description": "Name parameter",
                    "maxLength": 16,
                    "pattern": "[A-z]+"
                },
                "type": {
                    "type": "string",
                    "enum": [
                        "foo",
                        "bar"
                    ]
                }
            }
        },
        "GET-query": {
            "id": "GET-query",
            "properties": {
                "startIndex": {
                    "type": "integer",
                    "description": "startIndex parameter",
                    "maximum": 32
                },
                "float": {
                    "type": "number"
                },
                "boolean": {
                    "type": "boolean"
                },
                "date": {
                    "type": "string"
                },
                "datetime": {
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
                        "$ref": "ref7bde1c36c5f13fd4cf10c2864f8e8a75"
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
            [['GET'], '/swagger', 'PSX\Framework\Controller\Generator\SwaggerController::doIndex'],
            [['GET'], '/swagger/:version/*path', 'PSX\Framework\Controller\Generator\SwaggerController::doDetail'],
            [['GET', 'POST', 'PUT', 'DELETE'], '/api', 'PSX\Framework\Tests\Controller\Foo\Application\TestSchemaApiController'],
        );
    }
}
