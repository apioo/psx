<?php

namespace PSX\Project\Tests\Api\Generator;

use PSX\Project\Tests\ApiTestCase;

class SwaggerTest extends ApiTestCase
{
    public function testGet()
    {
        $response = $this->sendRequest('http://127.0.0.1/generator/swagger', 'GET');

        $body   = (string) $response->getBody();
        $expect = <<<JSON
{
    "swaggerVersion": "1.2",
    "apiVersion": "1.0",
    "apis": [
        {
            "path": "\/*\/population\/popo"
        },
        {
            "path": "\/*\/population\/popo\/{id}"
        },
        {
            "path": "\/*\/population\/jsonschema"
        },
        {
            "path": "\/*\/population\/jsonschema\/{id}"
        },
        {
            "path": "\/*\/population\/raml"
        },
        {
            "path": "\/*\/population\/raml\/{id}"
        },
        {
            "path": "\/*\/population"
        },
        {
            "path": "\/*\/population\/{id}"
        }
    ]
}
JSON;

        $this->assertEquals(null, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }

    public function testGetDetail()
    {
        $response = $this->sendRequest('http://127.0.0.1/generator/swagger/*/population/popo', 'GET');

        $body   = (string) $response->getBody();
        $expect = <<<'JSON'
{
    "swaggerVersion": "1.2",
    "apiVersion": 0,
    "basePath": "http:\/\/127.0.0.1\/",
    "resourcePath": "\/population\/popo",
    "apis": [
        {
            "path": "\/population\/popo",
            "description": "Collection endpoint",
            "operations": [
                {
                    "method": "GET",
                    "nickname": "getCollection",
                    "parameters": [
                        {
                            "paramType": "query",
                            "name": "startIndex",
                            "type": "integer"
                        },
                        {
                            "paramType": "query",
                            "name": "count",
                            "type": "integer"
                        }
                    ],
                    "responseMessages": [
                        {
                            "code": 200,
                            "message": "Collection result",
                            "responseModel": "GET-200-response"
                        }
                    ]
                },
                {
                    "method": "POST",
                    "nickname": "postEntity",
                    "parameters": [
                        {
                            "paramType": "body",
                            "name": "body",
                            "description": "Represents an internet population entity",
                            "required": true,
                            "type": "POST-request"
                        }
                    ],
                    "responseMessages": [
                        {
                            "code": 201,
                            "message": "Operation message",
                            "responseModel": "POST-201-response"
                        }
                    ]
                }
            ]
        }
    ],
    "models": {
        "ref057ec8a6751954e551431f5108608475": {
            "id": "ref057ec8a6751954e551431f5108608475",
            "properties": []
        },
        "ref4fe78e9f8d9266767f15f9b094d00e9d": {
            "id": "ref4fe78e9f8d9266767f15f9b094d00e9d",
            "description": "Represents an internet population entity",
            "required": [
                "place",
                "region",
                "population",
                "users",
                "worldUsers"
            ],
            "properties": []
        },
        "ref56cafd765c795ce405f04ff2c2b95851": {
            "id": "ref56cafd765c795ce405f04ff2c2b95851",
            "description": "Collection result",
            "properties": []
        },
        "ref31ead4d236fd038a7d55a40e2ca1171e": {
            "id": "ref31ead4d236fd038a7d55a40e2ca1171e",
            "description": "Operation message",
            "properties": []
        },
        "GET-query": {
            "id": "GET-query",
            "properties": {
                "startIndex": {
                    "type": "integer"
                },
                "count": {
                    "type": "integer"
                }
            }
        },
        "GET-200-response": {
            "id": "GET-200-response",
            "description": "Collection result",
            "properties": {
                "totalResults": {
                    "type": "integer"
                },
                "entry": {
                    "type": "array",
                    "items": {
                        "$ref": "ref4fe78e9f8d9266767f15f9b094d00e9d"
                    },
                    "title": "entry"
                }
            }
        },
        "POST-request": {
            "id": "POST-request",
            "description": "Represents an internet population entity",
            "required": [
                "place",
                "region",
                "population",
                "users",
                "worldUsers"
            ],
            "properties": {
                "id": {
                    "description": "Unique id for each entry",
                    "type": "integer"
                },
                "place": {
                    "description": "Position in the top list",
                    "type": "integer"
                },
                "region": {
                    "description": "Name of the region",
                    "type": "string",
                    "minLength": 3,
                    "maxLength": 64,
                    "pattern": "[A-z]+"
                },
                "population": {
                    "description": "Complete number of population",
                    "type": "integer"
                },
                "users": {
                    "description": "Number of internet users",
                    "type": "integer"
                },
                "worldUsers": {
                    "description": "Percentage users of the world",
                    "type": "number"
                },
                "datetime": {
                    "description": "Date when the entity was created",
                    "type": "string",
                    "format": "date-time"
                }
            }
        },
        "POST-201-response": {
            "id": "POST-201-response",
            "description": "Operation message",
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

        $this->assertEquals(null, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }
}
