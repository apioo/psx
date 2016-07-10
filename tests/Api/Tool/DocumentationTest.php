<?php

namespace PSX\Project\Tests\Api\Tool;

use PSX\Project\Tests\ApiTestCase;

class DocumentationTest extends ApiTestCase
{
    public function testGet()
    {
        $response = $this->sendRequest('http://127.0.0.1/tool/doc', 'GET');

        $body   = (string) $response->getBody();
        $expect = <<<JSON
{
    "routings": [
        {
            "path": "\/population\/popo",
            "methods": [
                "GET",
                "POST",
                "PUT",
                "DELETE"
            ],
            "version": "*"
        },
        {
            "path": "\/population\/popo\/:id",
            "methods": [
                "GET",
                "POST",
                "PUT",
                "DELETE"
            ],
            "version": "*"
        },
        {
            "path": "\/population\/jsonschema",
            "methods": [
                "GET",
                "POST",
                "PUT",
                "DELETE"
            ],
            "version": "*"
        },
        {
            "path": "\/population\/jsonschema\/:id",
            "methods": [
                "GET",
                "POST",
                "PUT",
                "DELETE"
            ],
            "version": "*"
        },
        {
            "path": "\/population\/raml",
            "methods": [
                "GET",
                "POST",
                "PUT",
                "DELETE"
            ],
            "version": "*"
        },
        {
            "path": "\/population\/raml\/:id",
            "methods": [
                "GET",
                "POST",
                "PUT",
                "DELETE"
            ],
            "version": "*"
        },
        {
            "path": "\/population",
            "methods": [
                "GET",
                "POST",
                "PUT",
                "DELETE"
            ],
            "version": "*"
        },
        {
            "path": "\/population\/:id",
            "methods": [
                "GET",
                "POST",
                "PUT",
                "DELETE"
            ],
            "version": "*"
        }
    ],
    "links": [
        {
            "rel": "self",
            "href": "http:\/\/127.0.0.1\/tool\/doc"
        },
        {
            "rel": "detail",
            "href": "http:\/\/127.0.0.1\/tool\/doc\/{version}\/{path}"
        },
        {
            "rel": "api",
            "href": "http:\/\/127.0.0.1\/"
        }
    ]
}
JSON;

        $this->assertEquals(null, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }

    public function testGetDetail()
    {
        $response = $this->sendRequest('http://127.0.0.1/tool/doc/*/population/popo', 'GET');

        $body   = (string) $response->getBody();
        $expect = <<<'JSON'
{
    "path": "\/population\/popo",
    "version": "*",
    "status": 1,
    "description": "Collection endpoint",
    "schema": {
        "$schema": "http:\/\/json-schema.org\/draft-04\/schema#",
        "id": "urn:schema.phpsx.org#",
        "type": "object",
        "definitions": {
            "ref057ec8a6751954e551431f5108608475": {
                "title": "query",
                "type": "object",
                "properties": {
                    "startIndex": {
                        "type": "integer"
                    },
                    "count": {
                        "type": "integer"
                    }
                },
                "additionalProperties": true
            },
            "ref4fe78e9f8d9266767f15f9b094d00e9d": {
                "title": "entity",
                "description": "Represents an internet population entity",
                "type": "object",
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
                },
                "additionalProperties": false,
                "required": [
                    "place",
                    "region",
                    "population",
                    "users",
                    "worldUsers"
                ],
                "reference": "PSX\\Project\\Tests\\Model\\Entity"
            },
            "ref56cafd765c795ce405f04ff2c2b95851": {
                "title": "collection",
                "description": "Collection result",
                "type": "object",
                "properties": {
                    "totalResults": {
                        "type": "integer"
                    },
                    "entry": {
                        "type": "array",
                        "items": {
                            "$ref": "#\/definitions\/ref4fe78e9f8d9266767f15f9b094d00e9d"
                        },
                        "title": "entry"
                    }
                },
                "additionalProperties": false,
                "reference": "PSX\\Project\\Tests\\Model\\Collection"
            },
            "ref31ead4d236fd038a7d55a40e2ca1171e": {
                "title": "message",
                "description": "Operation message",
                "type": "object",
                "properties": {
                    "success": {
                        "type": "boolean"
                    },
                    "message": {
                        "type": "string"
                    }
                },
                "additionalProperties": false,
                "reference": "PSX\\Project\\Tests\\Model\\Message"
            },
            "GET-query": {
                "$ref": "#\/definitions\/ref057ec8a6751954e551431f5108608475"
            },
            "GET-200-response": {
                "$ref": "#\/definitions\/ref56cafd765c795ce405f04ff2c2b95851"
            },
            "POST-request": {
                "$ref": "#\/definitions\/ref4fe78e9f8d9266767f15f9b094d00e9d"
            },
            "POST-201-response": {
                "$ref": "#\/definitions\/ref31ead4d236fd038a7d55a40e2ca1171e"
            }
        }
    },
    "methods": {
        "GET": {
            "queryParameters": "#\/definitions\/GET-query",
            "responses": {
                "200": "#\/definitions\/GET-200-response"
            }
        },
        "POST": {
            "request": "#\/definitions\/POST-request",
            "responses": {
                "201": "#\/definitions\/POST-201-response"
            }
        }
    },
    "links": [
        {
            "rel": "wsdl",
            "href": "\/generator\/wsdl\/*\/population\/popo"
        },
        {
            "rel": "swagger",
            "href": "\/generator\/swagger\/*\/population\/popo"
        },
        {
            "rel": "raml",
            "href": "\/generator\/raml\/*\/population\/popo"
        }
    ]
}
JSON;

        $this->assertEquals(null, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }
}
