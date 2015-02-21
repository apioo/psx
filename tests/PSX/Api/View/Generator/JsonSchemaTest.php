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

namespace PSX\Api\View\Generator;

use DOMDocument;
use PSX\Api\View;

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
		$generator = new JsonSchema('foo', 'http://api.phpsx.org', 'http://foo.phpsx.org');
		$json      = $generator->generate($this->getView());

		$expect = <<<'JSON'
{
  "$schema": "http://json-schema.org/draft-04/schema#",
  "id": "foo",
  "type": "object",
  "definitions": {"ref7738db4616810154ab42db61b65f74aa": {
      "type": "object",
      "properties": {
        "id": {"type": "integer"},
        "userId": {"type": "integer"},
        "title": {
          "type": "string",
          "minLength": 3,
          "maxLength": 16,
          "pattern": "[A-z]+"
        },
        "date": {"type": "string"}
      },
      "additionalProperties": false
    }},
  "properties": {
    "getResponse": {
      "type": "object",
      "properties": {"entry": {
          "type": "array",
          "items": {"$ref": "#/definitions/ref7738db4616810154ab42db61b65f74aa"}
        }},
      "additionalProperties": false
    },
    "postRequest": {
      "type": "object",
      "properties": {
        "id": {"type": "integer"},
        "userId": {"type": "integer"},
        "title": {
          "type": "string",
          "minLength": 3,
          "maxLength": 16,
          "pattern": "[A-z]+"
        },
        "date": {"type": "string"}
      },
      "required": [
        "title",
        "date"
      ],
      "additionalProperties": false
    },
    "postResponse": {
      "type": "object",
      "properties": {
        "success": {"type": "boolean"},
        "message": {"type": "string"}
      },
      "additionalProperties": false
    },
    "putRequest": {
      "type": "object",
      "properties": {
        "id": {"type": "integer"},
        "userId": {"type": "integer"},
        "title": {
          "type": "string",
          "minLength": 3,
          "maxLength": 16,
          "pattern": "[A-z]+"
        },
        "date": {"type": "string"}
      },
      "required": ["id"],
      "additionalProperties": false
    },
    "putResponse": {
      "type": "object",
      "properties": {
        "success": {"type": "boolean"},
        "message": {"type": "string"}
      },
      "additionalProperties": false
    },
    "deleteRequest": {
      "type": "object",
      "properties": {
        "id": {"type": "integer"},
        "userId": {"type": "integer"},
        "title": {
          "type": "string",
          "minLength": 3,
          "maxLength": 16,
          "pattern": "[A-z]+"
        },
        "date": {"type": "string"}
      },
      "required": ["id"],
      "additionalProperties": false
    },
    "deleteResponse": {
      "type": "object",
      "properties": {
        "success": {"type": "boolean"},
        "message": {"type": "string"}
      },
      "additionalProperties": false
    }
  }
}
JSON;

		$this->assertJsonStringEqualsJsonString($expect, $json);
	}
}
