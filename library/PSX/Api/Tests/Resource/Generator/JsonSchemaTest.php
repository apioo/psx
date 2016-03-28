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

namespace PSX\Api\Tests\Resource\Generator;

use PSX\Api\Resource\Generator\JsonSchema;

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
        "ref324d9c87eb6ee494de5207f005abddb8": {
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
            "additionalProperties": true
        },
        "ref85f5cb99d4cb24e97943e04989396c8e": {
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
            "additionalProperties": true
        },
        "ref7bde1c36c5f13fd4cf10c2864f8e8a75": {
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
        "refae7d4b5627a9dbac0c99945ecef66e17": {
            "type": "object",
            "title": "collection",
            "properties": {
                "entry": {
                    "type": "array",
                    "title": "entry",
                    "items": {
                        "$ref": "#\/definitions\/ref7bde1c36c5f13fd4cf10c2864f8e8a75"
                    }
                }
            },
            "additionalProperties": false
        },
        "ref70152cdfc48a8a3969f10e9e4fe3b239": {
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
        "ref31ead4d236fd038a7d55a40e2ca1171e": {
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
        "ref774a7a4ece700fad7bb605e81c61fea7": {
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
            "$ref": "#\/definitions\/ref324d9c87eb6ee494de5207f005abddb8"
        },
        "GET-query": {
            "$ref": "#\/definitions\/ref85f5cb99d4cb24e97943e04989396c8e"
        },
        "GET-200-response": {
            "$ref": "#\/definitions\/refae7d4b5627a9dbac0c99945ecef66e17"
        },
        "POST-request": {
            "$ref": "#\/definitions\/ref70152cdfc48a8a3969f10e9e4fe3b239"
        },
        "POST-201-response": {
            "$ref": "#\/definitions\/ref31ead4d236fd038a7d55a40e2ca1171e"
        },
        "PUT-request": {
            "$ref": "#\/definitions\/ref774a7a4ece700fad7bb605e81c61fea7"
        },
        "PUT-200-response": {
            "$ref": "#\/definitions\/ref31ead4d236fd038a7d55a40e2ca1171e"
        },
        "DELETE-request": {
            "$ref": "#\/definitions\/ref774a7a4ece700fad7bb605e81c61fea7"
        },
        "DELETE-200-response": {
            "$ref": "#\/definitions\/ref31ead4d236fd038a7d55a40e2ca1171e"
        },
        "PATCH-request": {
            "$ref": "#\/definitions\/ref774a7a4ece700fad7bb605e81c61fea7"
        },
        "PATCH-200-response": {
            "$ref": "#\/definitions\/ref31ead4d236fd038a7d55a40e2ca1171e"
        }
    }
}
JSON;

        $this->assertJsonStringEqualsJsonString($expect, $json);
    }
}
