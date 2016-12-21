<?php

namespace PSX\Project\Tests\Api\Generator;

use PSX\Project\Tests\ApiTestCase;
use Symfony\Component\Yaml\Yaml;

class RamlTest extends ApiTestCase
{
    public function testGet()
    {
        $response = $this->sendRequest('http://127.0.0.1/generator/raml/*/population/popo', 'GET');

        $body   = (string) $response->getBody();
        $expect = <<<'RAML'
#%RAML 1.0
---
baseUri: 'http://127.0.0.1/'
version: v0
title: Population
/population/popo:
  description: 'Collection endpoint'
  get:
    queryParameters:
      startIndex:
        type: integer
        required: false
      count:
        type: integer
        required: false
    responses:
      200:
        body:
          application/json:
            type: |
              {
                  "$schema": "http:\/\/json-schema.org\/draft-04\/schema#",
                  "id": "urn:schema.phpsx.org#",
                  "definitions": {
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
                      }
                  },
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
              }
  post:
    body:
      application/json:
        type: |
          {
              "$schema": "http:\/\/json-schema.org\/draft-04\/schema#",
              "id": "urn:schema.phpsx.org#",
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
          }
    responses:
      201:
        body:
          application/json:
            type: |
              {
                  "$schema": "http:\/\/json-schema.org\/draft-04\/schema#",
                  "id": "urn:schema.phpsx.org#",
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

RAML;

        $this->assertEquals(null, $response->getStatusCode(), $body);
        $this->assertEquals(Yaml::parse($expect), Yaml::parse($body), $body);
    }
}
