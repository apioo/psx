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
 * PropertyTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class PropertyTest extends SerializeTestAbstract
{
	public function testSerialize()
	{
		$property = new Property('id', Property::TYPE_INTEGER, 'Foobar');
		$property->setFormat(Property::FORMAT_INT64);
		$property->setDefaultValue(12);
		$property->setEnum(array(12, 24, 48));
		$property->setMinimum(8);
		$property->setMaximum(20);

		$content = <<<JSON
{
  "id": "id",
  "type": "integer",
  "format": "int64",
  "description": "Foobar",
  "defaultValue": 12,
  "enum": [12, 24, 48],
  "minimum": 8,
  "maximum": 20
}
JSON;

		$this->assertRecordEqualsContent($property, $content);
	}
}
