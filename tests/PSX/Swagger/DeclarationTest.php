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

namespace PSX\Swagger;

use PSX\Data\SerializeTestAbstract;

/**
 * DeclarationTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class DeclarationTest extends SerializeTestAbstract
{
    public function testSerialize()
    {
        $operation = new Operation('PUT', 'updatePet', 'Update an existing pet');

        $api = new Api('/foo', 'Foobar');
        $api->addOperation($operation);

        $model = new Model('Order', 'Order object');
        $model->addProperty(new Property('id', 'integer'));
        $model->addProperty(new PropertyReference('customer', 'Customer'));
        $model->addProperty(new Property('petId', 'integer'));
        $model->addProperty(new Property('quantity', 'integer'));
        $model->addProperty(new Property('status', 'string'));
        $model->addProperty(new Property('shipDate', 'string'));

        $declaration = new Declaration('1.0.0', 'http://petstore.swagger.wordnik.com/api', '/store');
        $declaration->addApi($api);
        $declaration->addModel($model);

        $content = <<<'JSON'
{
  "apiVersion": "1.0.0",
  "swaggerVersion": "1.2",
  "basePath": "http://petstore.swagger.wordnik.com/api",
  "resourcePath": "/store",
  "apis": [{
    "path": "/foo",
    "description": "Foobar",
    "operations": [{
      "method": "PUT",
      "nickname": "updatePet",
      "summary": "Update an existing pet",
      "parameters": [],
      "responseMessages": []
    }]
  }],
  "models": {
    "Order": {
      "id": "Order",
      "description": "Order object",
      "properties": {
        "id": {
          "id": "id",
          "type": "integer"
        },
        "customer": {
          "id": "customer",
          "$ref": "Customer"
        },
        "petId": {
          "id": "petId",
          "type": "integer"
        },
        "quantity": {
          "id": "quantity",
          "type": "integer"
        },
        "status": {
          "id": "status",
          "type": "string"
        },
        "shipDate": {
          "id": "shipDate",
          "type": "string"
        }
      }
    }
  }
}
JSON;

        $this->assertRecordEqualsContent($declaration, $content);
    }
}
