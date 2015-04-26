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

namespace PSX\ActivityStream;

use DateTime;
use PSX\ActivityStream\ObjectType\Binary;
use PSX\Data\SerializeTestAbstract;

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
		$binary = new Binary();
		$binary->setCompression('deflate');
		$binary->setData('dGhpcyBpcyB1bmNvbXByZXNzZWQgZGF0YQo=');
		$binary->setFileUrl('foo.txt');
		$binary->setLength(25);
		$binary->setMd5('827ae7e1ab45e4dd591d087c741e5770');
		$binary->setMimeType('text/plain');

		$laura = new Object();
		$laura->setObjectType('person');
		$laura->setDisplayName('Laura');

		$object = new Object();
		$object->setId('1');
		$object->setObjectType('person');
		$object->setLanguage('en');
		$object->setDisplayName('foo');
		$object->setUrl('http://localhost.com');
		$object->setRel('self');
		$object->setMediaType('text/plain');
		$object->setAlias('@network');
		$object->setAttachments(array($binary));
		$object->setAuthor($laura);
		$object->setContent('foobar');
		$object->setDuplicates('http://localhost.com#foo');
		$object->setIcon('http://localhost.com/icon.png');
		$object->setImage('http://localhost.com/image.png');
		$object->setLocation('foo');
		$object->setPublished(new DateTime('2012-12-12T12:00:00Z'));
		$object->setGenerator('http://phpsx.org');
		$object->setProvider('http://phpsx.org');
		$object->setSummary('foobar');
		$object->setUpdated(new DateTime('2012-12-12T12:00:00Z'));
		$object->setStartTime(new DateTime('2012-12-12T12:00:00Z'));
		$object->setEndTime(new DateTime('2012-12-12T12:00:00Z'));
		$object->setRating(0.8);
		$object->setTags(array('#foo', '#bar'));
		$object->setTitle('foobar');
		$object->setDuration('PT5M');
		$object->setHeight(256);
		$object->setWidth(256);
		$object->setInReplyTo('http://localhost.com#foo');
		$object->setScope('http://localhost.com');

		$content = <<<JSON
{
  "id": "1",
  "objectType": "person",
  "language": "en",
  "displayName": "foo",
  "url": "http://localhost.com",
  "rel": "self",
  "mediaType": "text/plain",
  "alias": "@network",
  "attachments": [{
      "compression": "deflate",
      "data": "dGhpcyBpcyB1bmNvbXByZXNzZWQgZGF0YQo=",
      "fileUrl": "foo.txt",
      "length": 25,
      "md5": "827ae7e1ab45e4dd591d087c741e5770",
      "mimeType": "text/plain",
      "objectType": "binary"
    }],
  "author": {
    "objectType": "person",
    "displayName": "Laura"
  },
  "content": "foobar",
  "duplicates": "http://localhost.com#foo",
  "icon": "http://localhost.com/icon.png",
  "image": "http://localhost.com/image.png",
  "location": "foo",
  "published": "2012-12-12T12:00:00Z",
  "generator": "http://phpsx.org",
  "provider": "http://phpsx.org",
  "summary": "foobar",
  "updated": "2012-12-12T12:00:00Z",
  "startTime": "2012-12-12T12:00:00Z",
  "endTime": "2012-12-12T12:00:00Z",
  "rating": 0.8,
  "tags": [
    "#foo",
    "#bar"
  ],
  "title": "foobar",
  "duration": "PT5M",
  "height": 256,
  "width": 256,
  "inReplyTo": "http://localhost.com#foo",
  "scope": "http://localhost.com"
}
JSON;

		$this->assertRecordEqualsContent($object, $content);

		$this->assertEquals('1', $object->getId());
		$this->assertEquals('person', $object->getObjectType());
		$this->assertEquals('en', $object->getLanguage());
		$this->assertEquals('foo', $object->getDisplayName());
		$this->assertEquals('http://localhost.com', $object->getUrl());
		$this->assertEquals('self', $object->getRel());
		$this->assertEquals('text/plain', $object->getMediaType());
		$this->assertEquals('@network', $object->getAlias());
		$this->assertEquals(array($binary), $object->getAttachments());
		$this->assertEquals($laura, $object->getAuthor());
		$this->assertEquals('foobar', $object->getContent());
		$this->assertEquals('http://localhost.com#foo', $object->getDuplicates());
		$this->assertEquals('http://localhost.com/icon.png', $object->getIcon());
		$this->assertEquals('http://localhost.com/image.png', $object->getImage());
		$this->assertEquals('foo', $object->getLocation());
		$this->assertEquals(new DateTime('2012-12-12T12:00:00Z'), $object->getPublished());
		$this->assertEquals('http://phpsx.org', $object->getGenerator());
		$this->assertEquals('http://phpsx.org', $object->getProvider());
		$this->assertEquals('foobar', $object->getSummary());
		$this->assertEquals(new DateTime('2012-12-12T12:00:00Z'), $object->getUpdated());
		$this->assertEquals(new DateTime('2012-12-12T12:00:00Z'), $object->getStartTime());
		$this->assertEquals(new DateTime('2012-12-12T12:00:00Z'), $object->getEndTime());
		$this->assertEquals(0.8, $object->getRating());
		$this->assertEquals(array('#foo', '#bar'), $object->getTags());
		$this->assertEquals('foobar', $object->getTitle());
		$this->assertEquals('PT5M', $object->getDuration());
		$this->assertEquals(256, $object->getHeight());
		$this->assertEquals(256, $object->getWidth());
		$this->assertEquals('http://localhost.com#foo', $object->getInReplyTo());
		$this->assertEquals('http://localhost.com', $object->getScope());
		$this->assertEquals('http://localhost.com', $object->__toString());
	}
}
