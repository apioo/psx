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

namespace PSX\Schema\Tests\Generator;

use PSX\Schema\Generator\JsonSchema;

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
        $generator = new JsonSchema();
        $result    = $generator->generate($this->getSchema());

        $expect = <<<'JSON'
{
    "$schema": "http:\/\/json-schema.org\/draft-04\/schema#",
    "id": "urn:schema.phpsx.org#",
    "type": "object",
    "title": "news",
    "description": "An general news entry",
    "definitions": {
        "ref156478d0880470b0a7122d05a3bd2153": {
            "type": "object",
            "title": "config",
            "patternProperties": {
                "^[a-zA-Z_\\x7f-\\xff][a-zA-Z0-9_\\x7f-\\xff]+$": {
                    "type": "string"
                }
            },
            "additionalProperties": false
        },
        "refbb8502281f7ea91de9f753ecbfd59e2b": {
            "type": "object",
            "properties": {
                "lat": {
                    "type": "integer"
                },
                "long": {
                    "type": "integer"
                }
            },
            "title": "origin",
            "description": "Location of the person",
            "additionalProperties": false
        },
        "ref98ac42f0f6f1ba965117d40b752bbb92": {
            "type": "object",
            "properties": {
                "title": {
                    "type": "string",
                    "pattern": "[A-z]{3,16}"
                },
                "email": {
                    "type": "string",
                    "description": "We will send no spam to this addresss"
                },
                "categories": {
                    "type": "array",
                    "items": {
                        "type": "string"
                    },
                    "title": "categories",
                    "maxItems": 8
                },
                "locations": {
                    "type": "array",
                    "items": {
                        "$ref": "#\/definitions\/refbb8502281f7ea91de9f753ecbfd59e2b"
                    },
                    "title": "locations",
                    "description": "Array of locations"
                },
                "origin": {
                    "$ref": "#\/definitions\/refbb8502281f7ea91de9f753ecbfd59e2b"
                }
            },
            "title": "author",
            "description": "An simple author element with some description",
            "required": [
                "title"
            ],
            "additionalProperties": false
        },
        "ref061fe430f3242fc808d8eb1859bb9cf3": {
            "type": "object",
            "properties": {
                "name": {
                    "type": "string"
                },
                "url": {
                    "type": "string"
                }
            },
            "title": "web",
            "description": "An application",
            "additionalProperties": false
        },
        "reff78af2fbd7e56ca137acc2dc418bdb98": {
            "oneOf": [
                {
                    "$ref": "#\/definitions\/refbb8502281f7ea91de9f753ecbfd59e2b"
                },
                {
                    "$ref": "#\/definitions\/ref061fe430f3242fc808d8eb1859bb9cf3"
                }
            ],
            "title": "resource"
        },
        "refde9ec7b57ff66baf264101526a8dc7d3": {
            "oneOf": [
                {
                    "$ref": "#\/definitions\/ref98ac42f0f6f1ba965117d40b752bbb92"
                },
                {
                    "$ref": "#\/definitions\/ref061fe430f3242fc808d8eb1859bb9cf3"
                }
            ],
            "title": "source"
        }
    },
    "properties": {
        "config": {
            "$ref": "#\/definitions\/ref156478d0880470b0a7122d05a3bd2153"
        },
        "tags": {
            "type": "array",
            "items": {
                "type": "string"
            },
            "title": "tags",
            "minItems": 1,
            "maxItems": 6
        },
        "receiver": {
            "type": "array",
            "items": {
                "$ref": "#\/definitions\/ref98ac42f0f6f1ba965117d40b752bbb92"
            },
            "title": "receiver",
            "minItems": 1
        },
        "resources": {
            "type": "array",
            "items": {
                "$ref": "#\/definitions\/reff78af2fbd7e56ca137acc2dc418bdb98"
            },
            "title": "resources"
        },
        "read": {
            "type": "boolean"
        },
        "source": {
            "$ref": "#\/definitions\/refde9ec7b57ff66baf264101526a8dc7d3"
        },
        "author": {
            "$ref": "#\/definitions\/ref98ac42f0f6f1ba965117d40b752bbb92"
        },
        "sendDate": {
            "type": "string"
        },
        "readDate": {
            "type": "string"
        },
        "expires": {
            "type": "string"
        },
        "price": {
            "type": "number",
            "minimum": 1,
            "maximum": 100
        },
        "rating": {
            "type": "integer",
            "minimum": 1,
            "maximum": 5
        },
        "content": {
            "type": "string",
            "description": "Contains the main content of the news entry",
            "minLength": 3,
            "maxLength": 512
        },
        "question": {
            "type": "string",
            "enum": [
                "foo",
                "bar"
            ]
        },
        "coffeeTime": {
            "type": "string"
        }
    },
    "required": [
        "receiver",
        "author",
        "price",
        "content"
    ],
    "additionalProperties": false
}
JSON;

        $this->assertJsonStringEqualsJsonString($expect, $result, $result);
    }
}
