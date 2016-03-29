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

namespace PSX\Model\Tests\Swagger;

use PSX\Data\Tests\SerializeTestAbstract;
use PSX\Model\Swagger\Api;
use PSX\Model\Swagger\Declaration;
use PSX\Model\Swagger\InfoObject;
use PSX\Model\Swagger\Model;
use PSX\Model\Swagger\Operation;
use PSX\Model\Swagger\Parameter;
use PSX\Model\Swagger\Property;
use PSX\Model\Swagger\ResourceListing;
use PSX\Model\Swagger\ResourceObject;
use PSX\Model\Swagger\ResponseMessage;

/**
 * SerializeTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class SerializeTest extends SerializeTestAbstract
{
    public function testSerialize()
    {
        $api = new Api('/store/order/{orderId}');

        $parameters = [];

        $parameter = new Parameter('path', 'orderId', 'ID of pet that needs to be fetched', true);
        $parameter->setType('string');
        $parameters[] = $parameter;

        $responseMessages = [];

        $responseMessages[] = new ResponseMessage(400, 'Invalid ID supplied');
        $responseMessages[] = new ResponseMessage(404, 'Order not found');

        $operation = new Operation('GET', 'getOrderById', 'Find purchase order by ID');
        $operation->setNotes('For valid response try integer IDs with value <= 5. Anything above 5 or nonintegers will generate API errors');
        $operation->setParameters($parameters);
        $operation->setResponseMessages($responseMessages);

        $api->addOperation($operation);

        $model = new Model('Order');

        $property = new Property('integer');
        $property->setFormat(Property::FORMAT_INT64);
        $model->addProperty('id', $property);

        $property = new Property('integer');
        $property->setFormat(Property::FORMAT_INT64);
        $model->addProperty('petId', $property);

        $property = new Property('integer');
        $property->setFormat(Property::FORMAT_INT32);
        $model->addProperty('quantity', $property);

        $property = new Property('string', 'Order Status');
        $property->setEnum(['placed', 'approved', 'delivered']);
        $model->addProperty('status', $property);

        $property = new Property('string');
        $property->setFormat(Property::FORMAT_DATETIME);
        $model->addProperty('shipDate', $property);

        $declaration = new Declaration('1.0.0', 'http://petstore.swagger.wordnik.com/api', '/store');
        $declaration->setProduces(['application/json']);
        $declaration->addApi($api);
        $declaration->addModel($model);

        $content = <<<'JSON'
{
  "apiVersion": "1.0.0",
  "swaggerVersion": "1.2",
  "basePath": "http://petstore.swagger.wordnik.com/api",
  "resourcePath": "/store",
  "produces": [
    "application/json"
  ],
  "apis": [
    {
      "path": "/store/order/{orderId}",
      "operations": [
        {
          "method": "GET",
          "summary": "Find purchase order by ID",
          "notes": "For valid response try integer IDs with value <= 5. Anything above 5 or nonintegers will generate API errors",
          "nickname": "getOrderById",
          "parameters": [
            {
              "name": "orderId",
              "description": "ID of pet that needs to be fetched",
              "required": true,
              "type": "string",
              "paramType": "path"
            }
          ],
          "responseMessages": [
            {
              "code": 400,
              "message": "Invalid ID supplied"
            },
            {
              "code": 404,
              "message": "Order not found"
            }
          ]
        }
      ]
    }
  ],
  "models": {
    "Order": {
      "id": "Order",
      "properties": {
        "id": {
          "type": "integer",
          "format": "int64"
        },
        "petId": {
          "type": "integer",
          "format": "int64"
        },
        "quantity": {
          "type": "integer",
          "format": "int32"
        },
        "status": {
          "type": "string",
          "description": "Order Status",
          "enum": [
            "placed",
            "approved",
            "delivered"
          ]
        },
        "shipDate": {
          "type": "string",
          "format": "date-time"
        }
      }
    }
  }
}
JSON;

        $this->assertRecordEqualsContent($declaration, $content);
    }

    public function testSerializeResourceListing()
    {
        $listing = new ResourceListing('1.0.0', 'http://petstore.swagger.wordnik.com/api', '/store');
        $listing->addResource(new ResourceObject('/pet', 'Operations about pets'));
        $listing->addResource(new ResourceObject('/user', 'Operations about user'));
        $listing->addResource(new ResourceObject('/store', 'Operations about store'));

        $info = new InfoObject('Swagger Sample App', 'This is a sample server Petstore server.');
        $info->setTermsOfServiceUrl('http://helloreverb.com/terms/');
        $info->setContact('apiteam@wordnik.com');
        $info->setLicense('Apache 2.0');
        $info->setLicenseUrl('http://www.apache.org/licenses/LICENSE-2.0.html');

        $listing->setInfo($info);

        $content = <<<'JSON'
{
  "apiVersion": "1.0.0",
  "swaggerVersion": "1.2",
  "apis": [
    {
      "path": "/pet",
      "description": "Operations about pets"
    },
    {
      "path": "/user",
      "description": "Operations about user"
    },
    {
      "path": "/store",
      "description": "Operations about store"
    }
  ],
  "info": {
    "title": "Swagger Sample App",
    "description": "This is a sample server Petstore server.",
    "termsOfServiceUrl": "http://helloreverb.com/terms/",
    "contact": "apiteam@wordnik.com",
    "license": "Apache 2.0",
    "licenseUrl": "http://www.apache.org/licenses/LICENSE-2.0.html"
  }
}
JSON;

        $this->assertRecordEqualsContent($listing, $content);
    }
}
