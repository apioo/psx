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

use DateTime;
use PSX\ActivityStream;
use PSX\Data\ResultSet;

/**
 * WriterTestCase
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class WriterTestCase extends \PHPUnit_Framework_TestCase
{
	public function getRecord()
	{
		$record = new WriterTestRecord();
		$record->setId(1);
		$record->setAuthor('foo');
		$record->setTitle('bar');
		$record->setContent('foobar');
		$record->setDate(new DateTime('2012-03-11 13:37:21'));

		return $record;
	}

	public function getResultSet()
	{
		$entries = array();

		$record = new WriterTestRecord();
		$record->setId(1);
		$record->setAuthor('foo');
		$record->setTitle('bar');
		$record->setContent('foobar');
		$record->setDate(new DateTime('2012-03-11 13:37:21'));

		$entries[] = $record;

		$record = new WriterTestRecord();
		$record->setId(2);
		$record->setAuthor('foo');
		$record->setTitle('bar');
		$record->setContent('foobar');
		$record->setDate(new DateTime('2012-03-11 13:37:21'));

		$entries[] = $record;

		return new ResultSet(2, 0, 8, $entries);
	}

	public function getComplexRecord()
	{
		$actor = new ActivityStream\Object();
		$actor->setUrl('http://example.org/martin');
		$actor->setObjectType('person');
		$actor->setId('tag:example.org,2011:martin');
		$actor->setDisplayName('Martin Smith');

		$object = new ActivityStream\Object();
		$object->setUrl('http://example.org/blog/2011/02/entry');
		$object->setId('tag:example.org,2011:abc123/xyz');

		$target = new ActivityStream\Object();
		$target->setUrl('http://example.org/blog/');
		$target->setObjectType('blog');
		$target->setId('tag:example.org,2011:abc123');
		$target->setDisplayName('Martin\'s Blog');

		$activity = new ActivityStream\ObjectType\Activity();
		$activity->setPublished(new DateTime('2011-02-10T15:04:55Z'));
		$activity->setActor($actor);
		$activity->setVerb('post');
		$activity->setObject($object);
		$activity->setTarget($target);

		return $activity;
	}

	abstract public function testWrite();
	abstract public function testWriteResultSet();
}

