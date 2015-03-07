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

namespace PSX\Validate;

use PSX\Data\Record;
use PSX\Data\RecordAbstract;
use PSX\Filter;
use PSX\Validate;
use PSX\Validate\Property;

/**
 * ValidateAbstractTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ValidateAbstractTest extends \PHPUnit_Framework_TestCase
{
	public function testGetRecord()
	{
		$validator = new ArrayValidator(new Validate(), array(
			new Property('id', Validate::TYPE_INTEGER),
			new Property('title', Validate::TYPE_STRING, array(new Filter\Length(1, 2))),
		));

		$this->assertInstanceOf('PSX\Data\RecordInterface', $validator->getRecord());
		$this->assertEquals(array('id' => null, 'title' => null), $validator->getRecord()->getRecordInfo()->getFields());
	}
}
