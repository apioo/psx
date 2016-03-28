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

namespace PSX\Model\Tests\ActivityStream;

use DateTime;
use PSX\Data\Tests\SerializeTestAbstract;
use PSX\Model\ActivityStream\ObjectType;
use PSX\Model\ActivityStream\ObjectType\Binary;

/**
 * ObjectTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ObjectTest extends SerializeTestAbstract
{
    public function testObject()
    {
        $laura = new ObjectType();
        $laura->setObjectType('person');
        $laura->setDisplayName('Laura');

        $object = new ObjectType();
        $object->setId('1');
        $object->setObjectType('person');
        $object->setDisplayName('foo');
        $object->setUrl('http://localhost.com');
        $object->setAuthor($laura);
        $object->setContent('foobar');
        $object->setImage('http://localhost.com/image.png');
        $object->setPublished(new DateTime('2012-12-12T12:00:00Z'));
        $object->setSummary('foobar');
        $object->setUpdated(new DateTime('2012-12-12T12:00:00Z'));

        $content = <<<JSON
{
  "id": "1",
  "objectType": "person",
  "displayName": "foo",
  "url": "http://localhost.com",
  "author": {
    "objectType": "person",
    "displayName": "Laura"
  },
  "content": "foobar",
  "image": "http://localhost.com/image.png",
  "published": "2012-12-12T12:00:00Z",
  "summary": "foobar",
  "updated": "2012-12-12T12:00:00Z"
}
JSON;

        $this->assertRecordEqualsContent($object, $content);

        $this->assertEquals('1', $object->getId());
        $this->assertEquals('person', $object->getObjectType());
        $this->assertEquals('foo', $object->getDisplayName());
        $this->assertEquals('http://localhost.com', $object->getUrl());
        $this->assertEquals($laura, $object->getAuthor());
        $this->assertEquals('foobar', $object->getContent());
        $this->assertEquals('http://localhost.com/image.png', $object->getImage());
        $this->assertEquals(new DateTime('2012-12-12T12:00:00Z'), $object->getPublished());
        $this->assertEquals('foobar', $object->getSummary());
        $this->assertEquals(new DateTime('2012-12-12T12:00:00Z'), $object->getUpdated());
    }
}
