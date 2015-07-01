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

namespace PSX\Console\Schema;

use PSX\Test\ControllerTestCase;
use PSX\Test\Environment;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * SwaggerCommandTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class SwaggerCommandTest extends ControllerTestCase
{
    public function testCommand()
    {
        $command = new SwaggerCommand(Environment::getService('config'), Environment::getService('resource_listing'));

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'path' => '/api'
        ));

        $expect = <<<'JSON'
{
    "swaggerVersion": "1.2",
    "apiVersion": 3,
    "basePath": "http:\/\/127.0.0.1\/",
    "resourcePath": "\/api",
    "apis": [
        {
            "path": "\/api",
            "description": "lorem ipsum",
            "operations": [
                {
                    "method": "GET",
                    "nickname": "getCollection",
                    "summary": "Returns a collection",
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
                        "$ref": "ref993f4bb37f524889fc963fedd6381458"
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
        }
    }
}
JSON;

        $this->assertJsonStringEqualsJsonString($expect, $commandTester->getDisplay());
    }

    public function testCommandAvailable()
    {
        $command = Environment::getService('console')->find('schema:swagger');

        $this->assertInstanceOf('PSX\Console\Schema\SwaggerCommand', $command);
    }

    protected function getPaths()
    {
        return [
            [['GET', 'POST', 'PUT', 'DELETE'], '/api', 'PSX\Controller\Foo\Application\SchemaApi\VersionViewController']
        ];
    }
}
