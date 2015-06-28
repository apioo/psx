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

use PSX\Http\Stream\TempStream;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Json;
use PSX\Test\ControllerTestCase;
use PSX\Test\Environment;
use PSX\Url;

/**
 * RamlControllerTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class RamlControllerTest extends ControllerTestCase
{
	public function testIndex()
	{
		$response = $this->sendRequest('http://127.0.0.1/raml/1/api', 'GET', ['Accept' => 'application/json']);
		$raml     = (string) $response->getBody();

		$expect = <<<'RAML'
#%RAML 0.8
---
baseUri: 'http://127.0.0.1/'
version: v1
title: Api
/api:
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
                          "additionalProperties": false
                      }
                  },
                  "properties": {
                      "entry": {
                          "type": "array",
                          "items": {
                              "$ref": "#\/definitions\/ref993f4bb37f524889fc963fedd6381458"
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
      201:
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

		$this->assertEquals(null, $response->getStatusCode(), $raml);
		$this->assertEquals('application/raml+yaml', $response->getHeader('Content-Type'), $raml);
		$this->assertEquals(str_replace(array("\r\n", "\r"), "\n", $expect), $raml, $raml);
	}

	protected function getPaths()
	{
		return array(
			[['GET'], '/raml/:version/*path', 'PSX\Controller\Tool\RamlGeneratorController'],
			[['GET', 'POST', 'PUT', 'DELETE'], '/api', 'PSX\Controller\Foo\Application\TestSchemaApiController'],
		);
	}
}
