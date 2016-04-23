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
            "ref12427a2a4da80c722d6d54e518488d16": {
                "type": "object",
                "title": "query",
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
                        "description": "Position in the top list"
                    },
                    "region": {
                        "type": "string",
                        "description": "Name of the region",
                        "minLength": 3,
                        "maxLength": 64,
                        "pattern": "[A-z]+"
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
                        "description": "Date when the entity was created"
                    }
                },
                "required": [
                    "place",
                    "region",
                    "population",
                    "users",
                    "worldUsers"
                ],
                "additionalProperties": false
            },
            "ref0eaf38a348f17dc3ce8660979b1d42e6": {
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
                            "$ref": "#\/definitions\/ref4fe78e9f8d9266767f15f9b094d00e9d"
                        },
                        "title": "entry"
                    }
                },
                "additionalProperties": false
            },
            "ref31ead4d236fd038a7d55a40e2ca1171e": {
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
                "additionalProperties": false
            },
            "GET-query": {
                "$ref": "#\/definitions\/ref12427a2a4da80c722d6d54e518488d16"
            },
            "GET-200-response": {
                "$ref": "#\/definitions\/ref0eaf38a348f17dc3ce8660979b1d42e6"
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
