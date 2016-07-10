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
#%RAML 0.8
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
            schema: |
              {
                  "$schema": "http:\/\/json-schema.org\/draft-04\/schema#",
                  "id": "urn:schema.phpsx.org#",
                  "definitions": {
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
                      }
                  },
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
              }
  post:
    body:
      application/json:
        schema: |
          {
              "$schema": "http:\/\/json-schema.org\/draft-04\/schema#",
              "id": "urn:schema.phpsx.org#",
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
          }
    responses:
      201:
        body:
          application/json:
            schema: |
              {
                  "$schema": "http:\/\/json-schema.org\/draft-04\/schema#",
                  "id": "urn:schema.phpsx.org#",
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
              }

RAML;

        $this->assertEquals(null, $response->getStatusCode(), $body);
        $this->assertEquals(Yaml::parse($expect), Yaml::parse($body), $body);
    }
}
