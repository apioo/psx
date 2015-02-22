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
 * RamlTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class RamlTest extends GeneratorTestCase
{
	public function testGenerate()
	{
		$generator = new Raml('foobar', 1, 'http://api.phpsx.org', 'urn:schema.phpsx.org#');
		$raml      = $generator->generate($this->getView());

		$expect = <<<'RAML'
#%RAML 0.8
---
baseUri: http://api.phpsx.org
version: v1
title: foobar
/foo/bar:
  get:
    responses:
      200:
        body:
          application/json:
            schema: |
              {
                  "$schema": "http:\/\/json-schema.org\/draft-04\/schema#",
                  "id": "urn:schema.phpsx.org#",
                  "type": "object",
                  "definitions": {
                      "ref7738db4616810154ab42db61b65f74aa": {
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
                          "additionalProperties": false
                      }
                  },
                  "properties": {
                      "entry": {
                          "type": "array",
                          "items": {
                              "$ref": "#\/definitions\/ref7738db4616810154ab42db61b65f74aa"
                          }
                      }
                  },
                  "additionalProperties": false
              }
  post:
    body:
      application/json:
        schema: |
          {
              "$schema": "http:\/\/json-schema.org\/draft-04\/schema#",
              "id": "urn:schema.phpsx.org#",
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
              "required": [
                  "title",
                  "date"
              ],
              "additionalProperties": false
          }
    responses:
      200:
        body:
          application/json:
            schema: |
              {
                  "$schema": "http:\/\/json-schema.org\/draft-04\/schema#",
                  "id": "urn:schema.phpsx.org#",
                  "type": "object",
                  "properties": {
                      "success": {
                          "type": "boolean"
                      },
                      "message": {
                          "type": "string"
                      }
                  },
                  "additionalProperties": false
              }
  put:
    body:
      application/json:
        schema: |
          {
              "$schema": "http:\/\/json-schema.org\/draft-04\/schema#",
              "id": "urn:schema.phpsx.org#",
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
              "required": [
                  "id"
              ],
              "additionalProperties": false
          }
    responses:
      200:
        body:
          application/json:
            schema: |
              {
                  "$schema": "http:\/\/json-schema.org\/draft-04\/schema#",
                  "id": "urn:schema.phpsx.org#",
                  "type": "object",
                  "properties": {
                      "success": {
                          "type": "boolean"
                      },
                      "message": {
                          "type": "string"
                      }
                  },
                  "additionalProperties": false
              }
  delete:
    body:
      application/json:
        schema: |
          {
              "$schema": "http:\/\/json-schema.org\/draft-04\/schema#",
              "id": "urn:schema.phpsx.org#",
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
              "required": [
                  "id"
              ],
              "additionalProperties": false
          }
    responses:
      200:
        body:
          application/json:
            schema: |
              {
                  "$schema": "http:\/\/json-schema.org\/draft-04\/schema#",
                  "id": "urn:schema.phpsx.org#",
                  "type": "object",
                  "properties": {
                      "success": {
                          "type": "boolean"
                      },
                      "message": {
                          "type": "string"
                      }
                  },
                  "additionalProperties": false
              }

RAML;

		$this->assertEquals(str_replace(array("\r\n", "\r"), "\n", $expect), $raml);
	}
}