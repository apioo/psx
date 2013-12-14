<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2013 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Data;

use PSX\Data\Record\DefaultImporter;
use PSX\Exception;
use PSX\Http\Message;

/**
 * SerializeTestAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class SerializeTestAbstract extends \PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
	}

	protected function tearDown()
	{
	}

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
		$message = new Message(array(), $content);
		$reader  = new Reader\Json();
		$result  = $reader->read($message);

		$class     = get_class($record);
		$newRecord = new $class();
		$importer  = new DefaultImporter();
		$importer->import($newRecord, $result);

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
