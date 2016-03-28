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

namespace PSX\Framework\Tests\Console;

use PSX\Data\Exporter;
use PSX\Framework\Console\ResourceCommand;
use PSX\Framework\Test\Assert;
use PSX\Framework\Test\ControllerTestCase;
use PSX\Framework\Test\Environment;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * ResourceCommandTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ResourceCommandTest extends ControllerTestCase
{
    /**
     * @var \PSX\Framework\Console\ResourceCommand
     */
    protected $command;

    protected function setUp()
    {
        parent::setUp();

        $this->command = new ResourceCommand(
            Environment::getService('config'),
            Environment::getService('resource_listing'),
            new Exporter\Popo(Environment::getService('annotation_reader'))
        );
    }

    public function testJsonSchema()
    {
        $commandTester = new CommandTester($this->command);
        $commandTester->execute(array(
            'path'   => '/api',
            'format' => 'jsonschema'
        ));

        $expect = <<<'JSON'
{
    "$schema": "http:\/\/json-schema.org\/draft-04\/schema#",
    "id": "urn:schema.phpsx.org#",
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
        "refae7d4b5627a9dbac0c99945ecef66e17": {
            "type": "object",
            "title": "collection",
            "properties": {
                "entry": {
                    "type": "array",
                    "items": {
                        "$ref": "#\/definitions\/ref7bde1c36c5f13fd4cf10c2864f8e8a75"
                    },
                    "title": "entry"
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

        $this->assertJsonStringEqualsJsonString($expect, $commandTester->getDisplay(), $commandTester->getDisplay());
    }

    public function testRaml()
    {
        $commandTester = new CommandTester($this->command);
        $commandTester->execute(array(
            'path'   => '/api',
            'format' => 'raml'
        ));

        $expect = <<<'YAML'
#%RAML 0.8
---
baseUri: 'http://127.0.0.1/'
version: v1
title: foo
/api:
  description: 'lorem ipsum'
  uriParameters:
    name:
      type: string
      description: 'Name parameter'
      required: false
      minLength: 0
      maxLength: 16
      pattern: '[A-z]+'
    type:
      type: string
      required: false
      enum: [foo, bar]
  get:
    description: 'Returns a collection'
    queryParameters:
      startIndex:
        type: integer
        description: 'startIndex parameter'
        required: false
        minimum: 0
        maximum: 32
      float:
        type: number
        required: false
      boolean:
        type: boolean
        required: false
      date:
        type: date
        required: false
      datetime:
        type: date
        required: false
    responses:
      200:
        body:
          application/json:
            schema: |
              {
                  "$schema": "http:\/\/json-schema.org\/draft-04\/schema#",
                  "id": "urn:schema.phpsx.org#",
                  "type": "object",
                  "title": "collection",
                  "definitions": {
                      "ref7bde1c36c5f13fd4cf10c2864f8e8a75": {
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
                      }
                  },
                  "properties": {
                      "entry": {
                          "type": "array",
                          "items": {
                              "$ref": "#\/definitions\/ref7bde1c36c5f13fd4cf10c2864f8e8a75"
                          },
                          "title": "entry"
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
              }
  put:
    body:
      application/json:
        schema: |
          {
              "$schema": "http:\/\/json-schema.org\/draft-04\/schema#",
              "id": "urn:schema.phpsx.org#",
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
              }
  delete:
    body:
      application/json:
        schema: |
          {
              "$schema": "http:\/\/json-schema.org\/draft-04\/schema#",
              "id": "urn:schema.phpsx.org#",
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
              }
  patch:
    body:
      application/json:
        schema: |
          {
              "$schema": "http:\/\/json-schema.org\/draft-04\/schema#",
              "id": "urn:schema.phpsx.org#",
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
              }

YAML;

        Assert::assertStringMatchIgnoreWhitespace($expect, $commandTester->getDisplay());
    }

    public function testSwagger()
    {
        $commandTester = new CommandTester($this->command);
        $commandTester->execute(array(
            'path'   => '/api',
            'format' => 'swagger',
        ));

        $expect = <<<'JSON'
{
    "swaggerVersion": "1.2",
    "apiVersion": 1,
    "basePath": "http:\/\/127.0.0.1\/",
    "resourcePath": "\/api",
    "apis": [
        {
            "path": "\/api",
            "description": "lorem ipsum",
            "operations": [
                {
                    "method": "GET",
                    "summary": "Returns a collection",
                    "nickname": "getCollection",
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
                            "code": 201,
                            "message": "201 response",
                            "responseModel": "POST-201-response"
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
                },
                {
                    "method": "PATCH",
                    "nickname": "patchItem",
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
                            "type": "PATCH-request"
                        }
                    ],
                    "responseMessages": [
                        {
                            "code": 200,
                            "message": "200 response",
                            "responseModel": "PATCH-200-response"
                        }
                    ]
                }
            ]
        }
    ],
    "models": {
        "ref324d9c87eb6ee494de5207f005abddb8": {
            "id": "ref324d9c87eb6ee494de5207f005abddb8",
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
        "ref85f5cb99d4cb24e97943e04989396c8e": {
            "id": "ref85f5cb99d4cb24e97943e04989396c8e",
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
        "ref7bde1c36c5f13fd4cf10c2864f8e8a75": {
            "id": "ref7bde1c36c5f13fd4cf10c2864f8e8a75",
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
        "refae7d4b5627a9dbac0c99945ecef66e17": {
            "id": "refae7d4b5627a9dbac0c99945ecef66e17",
            "properties": {
                "entry": {
                    "type": "array",
                    "items": {
                        "$ref": "ref7bde1c36c5f13fd4cf10c2864f8e8a75"
                    },
                    "title": "entry"
                }
            }
        },
        "ref70152cdfc48a8a3969f10e9e4fe3b239": {
            "id": "ref70152cdfc48a8a3969f10e9e4fe3b239",
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
        "ref31ead4d236fd038a7d55a40e2ca1171e": {
            "id": "ref31ead4d236fd038a7d55a40e2ca1171e",
            "properties": {
                "success": {
                    "type": "boolean"
                },
                "message": {
                    "type": "string"
                }
            }
        },
        "ref774a7a4ece700fad7bb605e81c61fea7": {
            "id": "ref774a7a4ece700fad7bb605e81c61fea7",
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
                        "$ref": "ref7bde1c36c5f13fd4cf10c2864f8e8a75"
                    },
                    "title": "entry"
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
        "POST-201-response": {
            "id": "POST-201-response",
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
        },
        "PATCH-request": {
            "id": "PATCH-request",
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
        "PATCH-200-response": {
            "id": "PATCH-200-response",
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

        $this->assertJsonStringEqualsJsonString($expect, $commandTester->getDisplay(), $commandTester->getDisplay());
    }

    public function testWsdl()
    {
        $commandTester = new CommandTester($this->command);
        $commandTester->execute(array(
            'path'   => '/api',
            'format' => 'wsdl',
        ));

        $expect = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<wsdl:definitions xmlns:xs="http://www.w3.org/2001/XMLSchema" name="foo" targetNamespace="http://phpsx.org/2014/data" xmlns:tns="http://phpsx.org/2014/data" xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/">
  <wsdl:types xmlns:xs="http://www.w3.org/2001/XMLSchema">
    <xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema" targetNamespace="http://phpsx.org/2014/data" elementFormDefault="qualified" xmlns:tns="http://phpsx.org/2014/data">
      <xs:element name="getRequest" type="tns:void"/>
      <xs:element name="getResponse">
        <xs:complexType>
          <xs:sequence>
            <xs:element name="entry" type="tns:type7bde1c36c5f13fd4cf10c2864f8e8a75" minOccurs="0" maxOccurs="unbounded"/>
          </xs:sequence>
        </xs:complexType>
      </xs:element>
      <xs:complexType name="type7bde1c36c5f13fd4cf10c2864f8e8a75">
        <xs:sequence>
          <xs:element name="id" type="xs:integer" minOccurs="0" maxOccurs="1"/>
          <xs:element name="userId" type="xs:integer" minOccurs="0" maxOccurs="1"/>
          <xs:element name="title" type="tns:type6a76407ff2bfa4ff2bdec08659df49a7" minOccurs="0" maxOccurs="1"/>
          <xs:element name="date" type="xs:dateTime" minOccurs="0" maxOccurs="1"/>
        </xs:sequence>
      </xs:complexType>
      <xs:simpleType name="type6a76407ff2bfa4ff2bdec08659df49a7">
        <xs:restriction base="xs:string">
          <xs:minLength value="3"/>
          <xs:maxLength value="16"/>
          <xs:pattern value="[A-z]+"/>
        </xs:restriction>
      </xs:simpleType>
      <xs:element name="postRequest">
        <xs:complexType>
          <xs:sequence>
            <xs:element name="id" type="xs:integer" minOccurs="0" maxOccurs="1"/>
            <xs:element name="userId" type="xs:integer" minOccurs="0" maxOccurs="1"/>
            <xs:element name="title" type="tns:type5a75177c616e21aec2af3478247229d6" minOccurs="1" maxOccurs="1"/>
            <xs:element name="date" type="xs:dateTime" minOccurs="1" maxOccurs="1"/>
          </xs:sequence>
        </xs:complexType>
      </xs:element>
      <xs:simpleType name="type5a75177c616e21aec2af3478247229d6">
        <xs:restriction base="xs:string">
          <xs:minLength value="3"/>
          <xs:maxLength value="16"/>
          <xs:pattern value="[A-z]+"/>
        </xs:restriction>
      </xs:simpleType>
      <xs:element name="postResponse">
        <xs:complexType>
          <xs:sequence>
            <xs:element name="success" type="xs:boolean" minOccurs="0" maxOccurs="1"/>
            <xs:element name="message" type="xs:string" minOccurs="0" maxOccurs="1"/>
          </xs:sequence>
        </xs:complexType>
      </xs:element>
      <xs:element name="putRequest">
        <xs:complexType>
          <xs:sequence>
            <xs:element name="id" type="xs:integer" minOccurs="1" maxOccurs="1"/>
            <xs:element name="userId" type="xs:integer" minOccurs="0" maxOccurs="1"/>
            <xs:element name="title" type="tns:type6a76407ff2bfa4ff2bdec08659df49a7" minOccurs="0" maxOccurs="1"/>
            <xs:element name="date" type="xs:dateTime" minOccurs="0" maxOccurs="1"/>
          </xs:sequence>
        </xs:complexType>
      </xs:element>
      <xs:element name="putResponse">
        <xs:complexType>
          <xs:sequence>
            <xs:element name="success" type="xs:boolean" minOccurs="0" maxOccurs="1"/>
            <xs:element name="message" type="xs:string" minOccurs="0" maxOccurs="1"/>
          </xs:sequence>
        </xs:complexType>
      </xs:element>
      <xs:element name="deleteRequest">
        <xs:complexType>
          <xs:sequence>
            <xs:element name="id" type="xs:integer" minOccurs="1" maxOccurs="1"/>
            <xs:element name="userId" type="xs:integer" minOccurs="0" maxOccurs="1"/>
            <xs:element name="title" type="tns:type6a76407ff2bfa4ff2bdec08659df49a7" minOccurs="0" maxOccurs="1"/>
            <xs:element name="date" type="xs:dateTime" minOccurs="0" maxOccurs="1"/>
          </xs:sequence>
        </xs:complexType>
      </xs:element>
      <xs:element name="deleteResponse">
        <xs:complexType>
          <xs:sequence>
            <xs:element name="success" type="xs:boolean" minOccurs="0" maxOccurs="1"/>
            <xs:element name="message" type="xs:string" minOccurs="0" maxOccurs="1"/>
          </xs:sequence>
        </xs:complexType>
      </xs:element>
      <xs:element name="patchRequest">
        <xs:complexType>
          <xs:sequence>
            <xs:element name="id" type="xs:integer" minOccurs="1" maxOccurs="1"/>
            <xs:element name="userId" type="xs:integer" minOccurs="0" maxOccurs="1"/>
            <xs:element name="title" type="tns:type6a76407ff2bfa4ff2bdec08659df49a7" minOccurs="0" maxOccurs="1"/>
            <xs:element name="date" type="xs:dateTime" minOccurs="0" maxOccurs="1"/>
          </xs:sequence>
        </xs:complexType>
      </xs:element>
      <xs:element name="patchResponse">
        <xs:complexType>
          <xs:sequence>
            <xs:element name="success" type="xs:boolean" minOccurs="0" maxOccurs="1"/>
            <xs:element name="message" type="xs:string" minOccurs="0" maxOccurs="1"/>
          </xs:sequence>
        </xs:complexType>
      </xs:element>
      <xs:complexType name="fault">
        <xs:sequence>
          <xs:element name="success" type="xs:boolean" minOccurs="1" maxOccurs="1"/>
          <xs:element name="title" type="xs:string" minOccurs="0" maxOccurs="1"/>
          <xs:element name="message" type="xs:string" minOccurs="1" maxOccurs="1"/>
          <xs:element name="trace" type="xs:string" minOccurs="0" maxOccurs="1"/>
          <xs:element name="context" type="xs:string" minOccurs="0" maxOccurs="1"/>
        </xs:sequence>
      </xs:complexType>
      <xs:complexType name="void">
        <xs:sequence/>
      </xs:complexType>
      <xs:element name="error" type="tns:fault"/>
    </xs:schema>
  </wsdl:types>
  <wsdl:message name="getCollectionInput">
    <wsdl:part name="body" element="tns:getRequest"/>
  </wsdl:message>
  <wsdl:message name="getCollectionOutput">
    <wsdl:part name="body" element="tns:getResponse"/>
  </wsdl:message>
  <wsdl:message name="postItemInput">
    <wsdl:part name="body" element="tns:postRequest"/>
  </wsdl:message>
  <wsdl:message name="postItemOutput">
    <wsdl:part name="body" element="tns:postResponse"/>
  </wsdl:message>
  <wsdl:message name="putItemInput">
    <wsdl:part name="body" element="tns:putRequest"/>
  </wsdl:message>
  <wsdl:message name="putItemOutput">
    <wsdl:part name="body" element="tns:putResponse"/>
  </wsdl:message>
  <wsdl:message name="deleteItemInput">
    <wsdl:part name="body" element="tns:deleteRequest"/>
  </wsdl:message>
  <wsdl:message name="deleteItemOutput">
    <wsdl:part name="body" element="tns:deleteResponse"/>
  </wsdl:message>
  <wsdl:message name="patchItemInput">
    <wsdl:part name="body" element="tns:patchRequest"/>
  </wsdl:message>
  <wsdl:message name="patchItemOutput">
    <wsdl:part name="body" element="tns:patchResponse"/>
  </wsdl:message>
  <wsdl:message name="faultOutput">
    <wsdl:part name="body" element="tns:error"/>
  </wsdl:message>
  <wsdl:portType name="fooPortType">
    <wsdl:operation name="getCollection">
      <wsdl:input message="tns:getCollectionInput"/>
      <wsdl:output message="tns:getCollectionOutput"/>
      <wsdl:fault message="tns:faultOutput" name="SoapFaultException"/>
    </wsdl:operation>
    <wsdl:operation name="postItem">
      <wsdl:input message="tns:postItemInput"/>
      <wsdl:output message="tns:postItemOutput"/>
      <wsdl:fault message="tns:faultOutput" name="SoapFaultException"/>
    </wsdl:operation>
    <wsdl:operation name="putItem">
      <wsdl:input message="tns:putItemInput"/>
      <wsdl:output message="tns:putItemOutput"/>
      <wsdl:fault message="tns:faultOutput" name="SoapFaultException"/>
    </wsdl:operation>
    <wsdl:operation name="deleteItem">
      <wsdl:input message="tns:deleteItemInput"/>
      <wsdl:output message="tns:deleteItemOutput"/>
      <wsdl:fault message="tns:faultOutput" name="SoapFaultException"/>
    </wsdl:operation>
    <wsdl:operation name="patchItem">
      <wsdl:input message="tns:patchItemInput"/>
      <wsdl:output message="tns:patchItemOutput"/>
      <wsdl:fault message="tns:faultOutput" name="SoapFaultException"/>
    </wsdl:operation>
  </wsdl:portType>
  <wsdl:binding name="fooBinding" type="tns:fooPortType">
    <soap:binding style="document" transport="http://schemas.xmlsoap.org/soap/http"/>
    <wsdl:operation name="getCollection">
      <soap:operation soapAction="/api#GET"/>
      <wsdl:input>
        <soap:body use="literal"/>
      </wsdl:input>
      <wsdl:output>
        <soap:body use="literal"/>
      </wsdl:output>
      <wsdl:fault name="SoapFaultException">
        <soap:body use="literal" name="SoapFaultException"/>
      </wsdl:fault>
    </wsdl:operation>
    <wsdl:operation name="postItem">
      <soap:operation soapAction="/api#POST"/>
      <wsdl:input>
        <soap:body use="literal"/>
      </wsdl:input>
      <wsdl:output>
        <soap:body use="literal"/>
      </wsdl:output>
      <wsdl:fault name="SoapFaultException">
        <soap:body use="literal" name="SoapFaultException"/>
      </wsdl:fault>
    </wsdl:operation>
    <wsdl:operation name="putItem">
      <soap:operation soapAction="/api#PUT"/>
      <wsdl:input>
        <soap:body use="literal"/>
      </wsdl:input>
      <wsdl:output>
        <soap:body use="literal"/>
      </wsdl:output>
      <wsdl:fault name="SoapFaultException">
        <soap:body use="literal" name="SoapFaultException"/>
      </wsdl:fault>
    </wsdl:operation>
    <wsdl:operation name="deleteItem">
      <soap:operation soapAction="/api#DELETE"/>
      <wsdl:input>
        <soap:body use="literal"/>
      </wsdl:input>
      <wsdl:output>
        <soap:body use="literal"/>
      </wsdl:output>
      <wsdl:fault name="SoapFaultException">
        <soap:body use="literal" name="SoapFaultException"/>
      </wsdl:fault>
    </wsdl:operation>
    <wsdl:operation name="patchItem">
      <soap:operation soapAction="/api#PATCH"/>
      <wsdl:input>
        <soap:body use="literal"/>
      </wsdl:input>
      <wsdl:output>
        <soap:body use="literal"/>
      </wsdl:output>
      <wsdl:fault name="SoapFaultException">
        <soap:body use="literal" name="SoapFaultException"/>
      </wsdl:fault>
    </wsdl:operation>
  </wsdl:binding>
  <wsdl:service name="fooService">
    <wsdl:port name="fooPort" binding="tns:fooBinding">
      <soap:address location="http://127.0.0.1/api"/>
    </wsdl:port>
  </wsdl:service>
</wsdl:definitions>
XML;

        $this->assertXmlStringEqualsXmlString($expect, $commandTester->getDisplay(), $commandTester->getDisplay());
    }

    public function testXsd()
    {
        $commandTester = new CommandTester($this->command);
        $commandTester->execute(array(
            'path'   => '/api',
            'format' => 'xsd',
        ));

        $expect = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema" xmlns:tns="http://phpsx.org/2014/data" targetNamespace="http://phpsx.org/2014/data" elementFormDefault="qualified">
  <xs:element name="getRequest" type="tns:void"/>
  <xs:element name="getResponse">
    <xs:complexType>
      <xs:sequence>
        <xs:element name="entry" type="tns:type7bde1c36c5f13fd4cf10c2864f8e8a75" minOccurs="0" maxOccurs="unbounded"/>
      </xs:sequence>
    </xs:complexType>
  </xs:element>
  <xs:complexType name="type7bde1c36c5f13fd4cf10c2864f8e8a75">
    <xs:sequence>
      <xs:element name="id" type="xs:integer" minOccurs="0" maxOccurs="1"/>
      <xs:element name="userId" type="xs:integer" minOccurs="0" maxOccurs="1"/>
      <xs:element name="title" type="tns:type6a76407ff2bfa4ff2bdec08659df49a7" minOccurs="0" maxOccurs="1"/>
      <xs:element name="date" type="xs:dateTime" minOccurs="0" maxOccurs="1"/>
    </xs:sequence>
  </xs:complexType>
  <xs:simpleType name="type6a76407ff2bfa4ff2bdec08659df49a7">
    <xs:restriction base="xs:string">
      <xs:minLength value="3"/>
      <xs:maxLength value="16"/>
      <xs:pattern value="[A-z]+"/>
    </xs:restriction>
  </xs:simpleType>
  <xs:element name="postRequest">
    <xs:complexType>
      <xs:sequence>
        <xs:element name="id" type="xs:integer" minOccurs="0" maxOccurs="1"/>
        <xs:element name="userId" type="xs:integer" minOccurs="0" maxOccurs="1"/>
        <xs:element name="title" type="tns:type5a75177c616e21aec2af3478247229d6" minOccurs="1" maxOccurs="1"/>
        <xs:element name="date" type="xs:dateTime" minOccurs="1" maxOccurs="1"/>
      </xs:sequence>
    </xs:complexType>
  </xs:element>
  <xs:simpleType name="type5a75177c616e21aec2af3478247229d6">
    <xs:restriction base="xs:string">
      <xs:minLength value="3"/>
      <xs:maxLength value="16"/>
      <xs:pattern value="[A-z]+"/>
    </xs:restriction>
  </xs:simpleType>
  <xs:element name="postResponse">
    <xs:complexType>
      <xs:sequence>
        <xs:element name="success" type="xs:boolean" minOccurs="0" maxOccurs="1"/>
        <xs:element name="message" type="xs:string" minOccurs="0" maxOccurs="1"/>
      </xs:sequence>
    </xs:complexType>
  </xs:element>
  <xs:element name="putRequest">
    <xs:complexType>
      <xs:sequence>
        <xs:element name="id" type="xs:integer" minOccurs="1" maxOccurs="1"/>
        <xs:element name="userId" type="xs:integer" minOccurs="0" maxOccurs="1"/>
        <xs:element name="title" type="tns:type6a76407ff2bfa4ff2bdec08659df49a7" minOccurs="0" maxOccurs="1"/>
        <xs:element name="date" type="xs:dateTime" minOccurs="0" maxOccurs="1"/>
      </xs:sequence>
    </xs:complexType>
  </xs:element>
  <xs:element name="putResponse">
    <xs:complexType>
      <xs:sequence>
        <xs:element name="success" type="xs:boolean" minOccurs="0" maxOccurs="1"/>
        <xs:element name="message" type="xs:string" minOccurs="0" maxOccurs="1"/>
      </xs:sequence>
    </xs:complexType>
  </xs:element>
  <xs:element name="deleteRequest">
    <xs:complexType>
      <xs:sequence>
        <xs:element name="id" type="xs:integer" minOccurs="1" maxOccurs="1"/>
        <xs:element name="userId" type="xs:integer" minOccurs="0" maxOccurs="1"/>
        <xs:element name="title" type="tns:type6a76407ff2bfa4ff2bdec08659df49a7" minOccurs="0" maxOccurs="1"/>
        <xs:element name="date" type="xs:dateTime" minOccurs="0" maxOccurs="1"/>
      </xs:sequence>
    </xs:complexType>
  </xs:element>
  <xs:element name="deleteResponse">
    <xs:complexType>
      <xs:sequence>
        <xs:element name="success" type="xs:boolean" minOccurs="0" maxOccurs="1"/>
        <xs:element name="message" type="xs:string" minOccurs="0" maxOccurs="1"/>
      </xs:sequence>
    </xs:complexType>
  </xs:element>
  <xs:element name="patchRequest">
    <xs:complexType>
      <xs:sequence>
        <xs:element name="id" type="xs:integer" minOccurs="1" maxOccurs="1"/>
        <xs:element name="userId" type="xs:integer" minOccurs="0" maxOccurs="1"/>
        <xs:element name="title" type="tns:type6a76407ff2bfa4ff2bdec08659df49a7" minOccurs="0" maxOccurs="1"/>
        <xs:element name="date" type="xs:dateTime" minOccurs="0" maxOccurs="1"/>
      </xs:sequence>
    </xs:complexType>
  </xs:element>
  <xs:element name="patchResponse">
    <xs:complexType>
      <xs:sequence>
        <xs:element name="success" type="xs:boolean" minOccurs="0" maxOccurs="1"/>
        <xs:element name="message" type="xs:string" minOccurs="0" maxOccurs="1"/>
      </xs:sequence>
    </xs:complexType>
  </xs:element>
  <xs:complexType name="fault">
    <xs:sequence>
      <xs:element name="success" type="xs:boolean" minOccurs="1" maxOccurs="1"/>
      <xs:element name="title" type="xs:string" minOccurs="0" maxOccurs="1"/>
      <xs:element name="message" type="xs:string" minOccurs="1" maxOccurs="1"/>
      <xs:element name="trace" type="xs:string" minOccurs="0" maxOccurs="1"/>
      <xs:element name="context" type="xs:string" minOccurs="0" maxOccurs="1"/>
    </xs:sequence>
  </xs:complexType>
  <xs:complexType name="void">
    <xs:sequence/>
  </xs:complexType>
  <xs:element name="error" type="tns:fault"/>
</xs:schema>
XML;

        $this->assertXmlStringEqualsXmlString($expect, $commandTester->getDisplay(), $commandTester->getDisplay());
    }

    public function testCommandAvailable()
    {
        $command = Environment::getService('console')->find('resource');

        $this->assertInstanceOf('PSX\Framework\Console\ResourceCommand', $command);
    }

    protected function getPaths()
    {
        return [
            [['GET', 'POST', 'PUT', 'DELETE'], '/api', 'PSX\Framework\Tests\Controller\Foo\Application\TestSchemaApiController']
        ];
    }

    protected function assertSource($expect, $actual)
    {
        $expect = str_replace(array("\r\n", "\n", "\r"), "\n", $expect);
        $actual = str_replace(array("\r\n", "\n", "\r"), "\n", $actual);

        $this->assertEquals($expect, $actual);
    }
}
