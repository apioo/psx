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

namespace PSX\ActivityStream\ObjectType;

use PSX\ActivityStream\Object;
use PSX\DateTime;
use PSX\Data\SerializeTestAbstract;

/**
 * TaskTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class TaskTest extends SerializeTestAbstract
{
	public function testTask()
	{
		$actor = new Object();
		$actor->setObjectType('person');
		$actor->setDisplayName('James');

		$object = new Object();
		$object->setObjectType('file');
		$object->setDisplayName('A specification');
		$object->setUrl('http://example.org/spec.html');

		$subTask = new Task();
		$subTask->setDisplayName('Foo task');

		$task = new Task();
		$task->setDisplayName('James needs to read the spec');
		$task->setBy(new DateTime('2012-12-12T12:12:12Z'));
		$task->setVerb('read');
		$task->setActor($actor);
		$task->setObject($object);
		$task->setRequired(true);
		$task->setPrerequisites(array($subTask));
		$task->setSupersedes(array($subTask));

		$content = <<<JSON
{
  "actor": {
    "objectType": "person",
    "displayName": "James"
  },
  "by": "2012-12-12T12:12:12+00:00",
  "object": {
    "objectType": "file",
    "displayName": "A specification",
    "url": "http://example.org/spec.html"
  },
  "prerequisites": [{
      "objectType": "task",
      "displayName": "Foo task"
    }],
  "required": true,
  "supersedes": [{
      "objectType": "task",
      "displayName": "Foo task"
    }],
  "verb": "read",
  "objectType": "task",
  "displayName": "James needs to read the spec"
}
JSON;

		$this->assertRecordEqualsContent($task, $content);

		$this->assertEquals($actor, $task->getActor());
		$this->assertEquals(new DateTime('2012-12-12T12:12:12Z'), $task->getBy());
		$this->assertEquals($object, $task->getObject());
		$this->assertEquals(array($subTask), $task->getPrerequisites());
		$this->assertEquals(true, $task->getRequired());
		$this->assertEquals(array($subTask), $task->getSupersedes());
		$this->assertEquals('read', $task->getVerb());
	}
}
