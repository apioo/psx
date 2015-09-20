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
                            "code": 200,
                            "message": "200 response",
                            "responseModel": "POST-200-response"
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
                }
            ]
        }
    ],
    "models": {
        "ref8c9e003f6d4ea9f9c0ebc8c466a780b7": {
            "id": "ref8c9e003f6d4ea9f9c0ebc8c466a780b7",
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
        "refb06e4990004303fabe7a828ef449cdb3": {
            "id": "refb06e4990004303fabe7a828ef449cdb3",
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
                    }
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
                        "$ref": "ref993f4bb37f524889fc963fedd6381458"
                    }
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
        "POST-200-response": {
            "id": "POST-200-response",
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
