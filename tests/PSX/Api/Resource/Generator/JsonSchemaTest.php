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

namespace PSX\Api\Resource\Generator;

/**
 * JsonSchemaTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class JsonSchemaTest extends GeneratorTestCase
{
    public function testGenerate()
    {
        $generator = new JsonSchema('foo', 'http://api.phpsx.org', 'http://foo.phpsx.org');
        $json      = $generator->generate($this->getResource());

        $expect = <<<'JSON'
{
    "$schema": "http:\/\/json-schema.org\/draft-04\/schema#",
    "id": "foo",
    "type": "object",
    "definitions": {
        "ref8c9e003f6d4ea9f9c0ebc8c466a780b7": {
            "type": "object",
            "title": "path",
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
            },
            "additionalProperties": false
        },
        "refb06e4990004303fabe7a828ef449cdb3": {
            "type": "object",
            "title": "query",
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
            },
            "additionalProperties": false
        },
        "ref993f4bb37f524889fc963fedd6381458": {
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
            "additionalProperties": false
        },
        "refe80c8b9e68244cea3401d3b7aff00733": {
            "type": "object",
            "title": "collection",
            "properties": {
                "entry": {
                    "type": "array",
                    "title": "entry",
                    "items": {
                        "$ref": "#\/definitions\/ref993f4bb37f524889fc963fedd6381458"
                    }
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
        "path": {
            "$ref": "#\/definitions\/ref8c9e003f6d4ea9f9c0ebc8c466a780b7"
        },
        "GET-query": {
            "$ref": "#\/definitions\/refb06e4990004303fabe7a828ef449cdb3"
        },
        "GET-200-response": {
            "$ref": "#\/definitions\/refe80c8b9e68244cea3401d3b7aff00733"
        },
        "POST-request": {
            "$ref": "#\/definitions\/ref3934915b538d8557d87031925d29ac0d"
        },
        "POST-200-response": {
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
        }
    }
}
JSON;

        $this->assertJsonStringEqualsJsonString($expect, $json);
    }
}
