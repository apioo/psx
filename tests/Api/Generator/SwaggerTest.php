<?php

namespace PSX\Project\Tests\Api\Generator;

use PSX\Project\Tests\ApiTestCase;

class SwaggerTest extends ApiTestCase
{
    public function testGet()
    {
        $response = $this->sendRequest('http://127.0.0.1/generator/swagger/*/population/popo', 'GET');

        $body   = (string) $response->getBody();
        $expect = <<<'JSON'
{
    "swagger": "2.0",
    "info": {
        "title": "PSX",
        "version": "0"
    },
    "basePath": "\/",
    "paths": {
        "\/population\/popo": {
            "get": {
                "operationId": "doGet",
                "parameters": [
                    {
                        "name": "startIndex",
                        "in": "query",
                        "required": false,
                        "type": "integer"
                    },
                    {
                        "name": "count",
                        "in": "query",
                        "required": false,
                        "type": "integer"
                    }
                ],
                "responses": {
                    "200": {
                        "description": "Collection result",
                        "schema": {
                            "$ref": "#\/definitions\/Collection"
                        }
                    }
                }
            },
            "post": {
                "operationId": "doPost",
                "parameters": [
                    {
                        "description": "Represents an internet population entity",
                        "name": "body",
                        "in": "body",
                        "required": true,
                        "schema": {
                            "$ref": "#\/definitions\/Entity"
                        }
                    }
                ],
                "responses": {
                    "201": {
                        "description": "Operation message",
                        "schema": {
                            "$ref": "#\/definitions\/Message"
                        }
                    }
                }
            },
            "parameters": []
        }
    },
    "definitions": {
        "Collection": {
            "type": "object",
            "title": "collection",
            "description": "Collection result",
            "properties": {
                "totalResults": {
                    "type": "integer"
                },
                "entry": {
                    "type": "array",
                    "items": {
                        "$ref": "#\/definitions\/Entity"
                    }
                }
            },
            "class": "PSX\\Project\\Tests\\Model\\Collection"
        },
        "Entity": {
            "type": "object",
            "title": "entity",
            "description": "Represents an internet population entity",
            "properties": {
                "id": {
                    "type": "integer",
                    "description": "Unique id for each entry"
                },
                "place": {
                    "type": "integer",
                    "description": "Position in the top list",
                    "minimum": 1,
                    "maximum": 64
                },
                "region": {
                    "type": "string",
                    "description": "Name of the region",
                    "pattern": "[A-z]+",
                    "minLength": 3,
                    "maxLength": 64
                },
                "population": {
                    "type": "integer",
                    "description": "Complete number of population"
                },
                "users": {
                    "type": "integer",
                    "description": "Number of internet users"
                },
                "worldUsers": {
                    "type": "number",
                    "description": "Percentage users of the world"
                },
                "datetime": {
                    "type": "string",
                    "description": "Date when the entity was created",
                    "format": "date-time"
                }
            },
            "required": [
                "place",
                "region",
                "population",
                "users",
                "worldUsers"
            ],
            "class": "PSX\\Project\\Tests\\Model\\Entity"
        },
        "Message": {
            "type": "object",
            "title": "message",
            "description": "Operation message",
            "properties": {
                "success": {
                    "type": "boolean"
                },
                "message": {
                    "type": "string"
                }
            },
            "class": "PSX\\Project\\Tests\\Model\\Message"
        }
    }
}
JSON;

        $this->assertEquals(null, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }
}
