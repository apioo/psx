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

use DateTime;
use PSX\ActivityStream;
use PSX\Data\ResultSet;

/**
 * WriterTestCase
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
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

