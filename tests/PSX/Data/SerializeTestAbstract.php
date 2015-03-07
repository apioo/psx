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

namespace PSX\Data;

use PSX\Data\Record\DefaultImporter;
use PSX\Exception;
use PSX\Http\Message;

/**
 * SerializeTestAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class SerializeTestAbstract extends \PHPUnit_Framework_TestCase
{
	/**
	 * Checks whether the records can be serialzed to the content format and the
	 * content format can be serialized to the record without loosing data
	 *
	 * @param PSX\Data\RecordInterface $record
	 * @param string $content
	 */
	protected function assertRecordEqualsContent(RecordInterface $record, $content)
	{
		// serialize the record
		$response = $this->getWriterResponse($record);

		// check whether the response is the same as the content
		$this->assertJsonStringEqualsJsonString($content, $response);

		// create a new record of the same class and import the content
		$message   = new Message(array(), $content);
		$class     = get_class($record);
		$newRecord = getContainer()->get('importer')->import(new $class(), $message);

		// get response
		$newResponse = $this->getWriterResponse($newRecord);

		// check whether the newResponse is the same as the content
		$this->assertJsonStringEqualsJsonString($content, $newResponse);

		// check whether the newResponse is the same as the response
		$this->assertJsonStringEqualsJsonString($response, $newResponse);
	}

	protected function getWriterResponse(RecordInterface $record)
	{
		$writer  = new Writer\Json();
		$content = $writer->write($record);

		return $content;
	}
}
