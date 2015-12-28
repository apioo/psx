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
 * DocumentationControllerTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class DocumentationControllerTest extends ControllerTestCase
{
    public function testIndex()
    {
        $response = $this->sendRequest('http://127.0.0.1/doc', 'GET', ['Accept' => 'application/json']);
        $json     = (string) $response->getBody();

        $expect = <<<'JSON'
{
    "routings": [
        {
            "path": "\/api",
            "methods": [
                "GET",
                "POST",
                "PUT",
                "DELETE",
                "PATCH"
            ],
            "version": "*"
        }
    ],
    "links": [
        {
            "rel": "self",
            "href": "http:\/\/127.0.0.1\/doc"
        },
        {
            "rel": "detail",
            "href": "http:\/\/127.0.0.1\/doc\/{version}\/{path}"
        },
        {
            "rel": "api",
            "href": "http:\/\/127.0.0.1\/"
        }
    ]
}
JSON;

        $this->assertEquals(null, $response->getStatusCode(), $json);
        $this->assertJsonStringEqualsJsonString($expect, $json, $json);
    }

    public function testDetail()
    {
        $response = $this->sendRequest('http://127.0.0.1/doc/1/api', 'GET', ['Accept' => 'application/json']);
        $json     = (string) $response->getBody();

        $expect = <<<'JSON'
{
    "path": "\/api",
    "version": "1",
    "status": 1,
    "schema": {
        "$schema": "http:\/\/json-schema.org\/draft-04\/schema#",
        "id": "urn:schema.phpsx.org#",
        "type": "object",
        "definitions": {
            "ref993f4bb37f524889fc963fedd6381458": {
                "type": "object",
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
                },
                "title": "item",
                "additionalProperties": false
            },
            "refe80c8b9e68244cea3401d3b7aff00733": {
                "type": "object",
                "title": "collection",
                "properties": {
                    "entry": {
                        "type": "array",
                        "items": {
                            "$ref": "#\/definitions\/ref993f4bb37f524889fc963fedd6381458"
                        },
                        "title": "entry"
                    }
                },
                "additionalProperties": false
            },
            "ref3934915b538d8557d87031925d29ac0d": {
                "type": "object",
                "title": "item",
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
                },
                "required": [
                    "title",
                    "date"
                ],
                "additionalProperties": false
            },
            "ref3a0bf597c698b671859e2c0ca2640825": {
                "type": "object",
                "title": "message",
                "properties": {
                    "success": {
                        "type": "boolean"
                    },
                    "message": {
                        "type": "string"
                    }
                },
                "additionalProperties": false
            },
            "ref3368bc12f3927997f38dc1bea49554be": {
                "type": "object",
                "title": "item",
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
                },
                "required": [
                    "id"
                ],
                "additionalProperties": false
            },
            "GET-200-response": {
                "$ref": "#\/definitions\/refe80c8b9e68244cea3401d3b7aff00733"
            },
            "POST-request": {
                "$ref": "#\/definitions\/ref3934915b538d8557d87031925d29ac0d"
            },
            "POST-201-response": {
                "$ref": "#\/definitions\/ref3a0bf597c698b671859e2c0ca2640825"
            },
            "PUT-request": {
                "$ref": "#\/definitions\/ref3368bc12f3927997f38dc1bea49554be"
            },
            "PUT-200-response": {
                "$ref": "#\/definitions\/ref3a0bf597c698b671859e2c0ca2640825"
            },
            "DELETE-request": {
                "$ref": "#\/definitions\/ref3368bc12f3927997f38dc1bea49554be"
            },
            "DELETE-200-response": {
                "$ref": "#\/definitions\/ref3a0bf597c698b671859e2c0ca2640825"
            },
            "PATCH-request": {
                "$ref": "#\/definitions\/ref3368bc12f3927997f38dc1bea49554be"
            },
            "PATCH-200-response": {
                "$ref": "#\/definitions\/ref3a0bf597c698b671859e2c0ca2640825"
            }
        }
    },
    "versions": [
        {
            "version": 1,
            "status": 1
        }
    ],
    "methods": {
        "GET": {
            "responses": {
                "200": "#\/definitions\/GET-200-response"
            }
        },
        "POST": {
            "request": "#\/definitions\/POST-request",
            "responses": {
                "201": "#\/definitions\/POST-201-response"
            }
        },
        "PUT": {
            "request": "#\/definitions\/PUT-request",
            "responses": {
                "200": "#\/definitions\/PUT-200-response"
            }
        },
        "DELETE": {
            "request": "#\/definitions\/DELETE-request",
            "responses": {
                "200": "#\/definitions\/DELETE-200-response"
            }
        },
        "PATCH": {
            "request": "#\/definitions\/PATCH-request",
            "responses": {
                "200": "#\/definitions\/PATCH-200-response"
            }
        }
    }
}
JSON;

        $this->assertEquals(null, $response->getStatusCode(), $json);
        $this->assertJsonStringEqualsJsonString($expect, $json, $json);
    }

    protected function getPaths()
    {
        return array(
            [['GET'], '/doc', 'PSX\Controller\Tool\DocumentationController::doIndex'],
            [['GET'], '/doc/:version/*path', 'PSX\Controller\Tool\DocumentationController::doDetail'],
            [['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/api', 'PSX\Controller\Foo\Application\TestSchemaApiController'],
        );
    }
}
