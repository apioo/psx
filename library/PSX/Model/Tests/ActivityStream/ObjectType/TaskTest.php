<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2016 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Model\Tests\ActivityStream\ObjectType;

use DateTime;
use PSX\Data\Tests\SerializeTestAbstract;
use PSX\Model\ActivityStream\ObjectType;

/**
 * TaskTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class TaskTest extends SerializeTestAbstract
{
    public function testTask()
    {
        $actor = new ObjectType();
        $actor->setObjectType('person');
        $actor->setDisplayName('James');

        $object = new ObjectType();
        $object->setObjectType('file');
        $object->setDisplayName('A specification');
        $object->setUrl('http://example.org/spec.html');

        $subTask = new ObjectType\Task();
        $subTask->setDisplayName('Foo task');

        $task = new ObjectType\Task();
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
  "by": "2012-12-12T12:12:12Z",
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
