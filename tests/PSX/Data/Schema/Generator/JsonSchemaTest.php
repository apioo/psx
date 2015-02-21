<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
 *
 * This file is part of psx. psx is free software: you can
 * redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or any later version.
 *
 * psx is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with psx. If not, see <http://www.gnu.org/licenses/>.
 */

namespace PSX\Data\Schema\Generator;

/**
 * JsonSchemaTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
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
  "$schema": "http://json-schema.org/draft-04/schema#",
  "id": "urn:schema.phpsx.org#",
  "description": "An general news entry",
  "definitions": {
    "ref80f95cf5fd279866b5859c275abd7fa2": {
      "type": "object",
      "description": "An simple author element with some description",
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
          }
        },
        "locations": {
          "type": "array",
          "items": {
            "$ref": "#/definitions/ref93ef595df6d9e735702cba3611adba27"
          }
        },
        "origin": {
          "$ref": "#/definitions/ref93ef595df6d9e735702cba3611adba27"
        }
      },
      "required": [
        "title"
      ],
      "additionalProperties": false
    },
    "ref93ef595df6d9e735702cba3611adba27": {
      "type": "object",
      "description": "Location of the person",
      "properties": {
        "lat": {
          "type": "integer"
        },
        "long": {
          "type": "integer"
        }
      },
      "additionalProperties": false
    }
  },
  "type": "object",
  "properties": {
    "tags": {
      "type": "array",
      "items": {
        "type": "string"
      },
      "minItems": 1
    },
    "receiver": {
      "type": "array",
      "items": {
        "$ref": "#/definitions/ref80f95cf5fd279866b5859c275abd7fa2"
      },
      "minItems": 1
    },
    "read": {
      "type": "boolean"
    },
    "author": {
      "$ref": "#/definitions/ref80f95cf5fd279866b5859c275abd7fa2"
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
      "minLength": 3,
      "maxLength": 512,
      "description": "Contains the main content of the news entry"
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

		$this->assertJsonStringEqualsJsonString($expect, $result);
	}
}
