<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2014 Christoph Kappestein <k42b3.x@gmail.com>
 *
 * This file is part of psx. psx is free software: you can
 * redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or any later version.
 *
 * psx is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with psx. If not, see <http://www.gnu.org/licenses/>.
 */

namespace PSX\Swagger;

use PSX\Data\SerializeTestAbstract;

/**
 * DeclarationTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
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
