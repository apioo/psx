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
 * OperationTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class OperationTest extends SerializeTestAbstract
{
	public function testSerialize()
	{
		$parameter       = new Parameter('query', 'count', 'Count parameter');
		$responseMessage = new ResponseMessage(200, 'Return', 'News');

		$operation = new Operation('PUT', 'updatePet', 'Update an existing pet');
		$operation->setNotes('foobar');
		$operation->addParameter($parameter);
		$operation->addResponseMessage($responseMessage);

		$content = <<<JSON
{
  "method": "PUT",
  "nickname": "updatePet",
  "summary": "Update an existing pet",
  "notes": "foobar",
  "parameters": [{
    "paramType": "query",
    "name": "count",
    "description": "Count parameter"
  }],
  "responseMessages": [{
    "code": 200,
    "message": "Return",
    "responseModel": "News"
  }]
}
JSON;

		$this->assertRecordEqualsContent($operation, $content);

		$this->assertEquals('PUT', $operation->getMethod());
		$this->assertEquals('updatePet', $operation->getNickname());
		$this->assertEquals('Update an existing pet', $operation->getSummary());
		$this->assertEquals('foobar', $operation->getNotes());
		$this->assertEquals([$parameter], $operation->getParameters());
		$this->assertEquals([$responseMessage], $operation->getResponseMessages());
	}
}
