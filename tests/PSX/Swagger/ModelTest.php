<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
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
 * ModelTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
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
