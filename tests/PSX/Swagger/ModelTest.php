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

namespace PSX\Swagger;

use PSX\Data\SerializeTestAbstract;

/**
 * ModelTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ModelTest extends SerializeTestAbstract
{
    public function testSerialize()
    {
        $model = new Model('Order', 'Order object');
        $model->addProperty(new Property('id', 'integer'));
        $model->addProperty(new Property('type', 'string'));
        $model->addProperty(new PropertyReference('customer', 'Customer'));
        $model->addProperty(new Property('petId', 'integer'));
        $model->addProperty(new Property('shipDate', 'string'));
        $model->setRequired(array('type', 'customer', 'petId'));
        $model->setSubTypes(array('PetOrder', 'FooOrder'));
        $model->setDiscriminator('type');

        $content = <<<'JSON'
{
  "id": "Order",
  "description": "Order object",
  "required": ["type", "customer", "petId"],
  "discriminator": "type",
  "subTypes": ["PetOrder", "FooOrder"],
  "properties": {
    "id": {
      "id": "id",
      "type": "integer"
    },
    "type": {
      "id": "type",
      "type": "string"
    },
    "customer": {
      "id": "customer",
      "$ref": "Customer"
    },
    "petId": {
      "id": "petId",
      "type": "integer"
    },
    "shipDate": {
      "id": "shipDate",
      "type": "string"
    }
  }
}
JSON;

        $this->assertRecordEqualsContent($model, $content);
    }
}
