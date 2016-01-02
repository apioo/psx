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

namespace PSX\ActivityStream\ObjectType;

use PSX\ActivityStream\ObjectType;
use PSX\Data\SerializeTestAbstract;
use PSX\DateTime;

/**
 * ActivityTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ActivityTest extends SerializeTestAbstract
{
    public function testActivity()
    {
        $image = new ObjectType();
        $image->setUrl('http://example.org/martin/image.jpg');
        $image->setMediaType('image/jpeg');
        $image->setWidth(250);
        $image->setHeight(250);

        $actor = new ObjectType();
        $actor->setObjectType('person');
        $actor->setId('urn:example:person:martin');
        $actor->setDisplayName('Martin Smith');
        $actor->setUrl('http://example.org/martin');
        $actor->setImage($image);

        $object = new ObjectType();
        $object->setObjectType('article');
        $object->setId('urn:example:blog:abc123/xyz');
        $object->setUrl('http://example.org/blog/2011/02/entry');
        $object->setDisplayName('Why I love Activity Streams');

        $target = new ObjectType();
        $target->setObjectType('blog');
        $target->setId('urn:example:blog:abc123');
        $target->setDisplayName('Martin\'s Blog');
        $target->setUrl('http://example.org/blog/');

        $activity = new Activity();
        $activity->setVerb('post');
        $activity->setPublished(new DateTime('2011-02-10T15:04:55Z'));
        $activity->setLanguage('en');
        $activity->setActor($actor);
        $activity->setObject($object);
        $activity->setTarget($target);
        $activity->setResult($image);
        $activity->setPriority(0.5);
        $activity->setTo($actor);
        $activity->setCc($actor);
        $activity->setBto($actor);
        $activity->setBcc($actor);

        $content = <<<JSON
{
  "verb": "post",
  "actor": {
    "id": "urn:example:person:martin",
    "objectType": "person",
    "displayName": "Martin Smith",
    "url": "http://example.org/martin",
    "image": {
      "mediaType": "image/jpeg",
      "url": "http://example.org/martin/image.jpg",
      "height": 250,
      "width": 250
    }
  },
  "object": {
    "id": "urn:example:blog:abc123/xyz",
    "objectType": "article",
    "displayName": "Why I love Activity Streams",
    "url": "http://example.org/blog/2011/02/entry"
  },
  "target": {
    "id": "urn:example:blog:abc123",
    "objectType": "blog",
    "displayName": "Martin's Blog",
    "url": "http://example.org/blog/"
  },
  "result": {
    "mediaType": "image/jpeg",
    "url": "http://example.org/martin/image.jpg",
    "height": 250,
    "width": 250
  },
  "priority": 0.5,
  "to": {
    "id": "urn:example:person:martin",
    "objectType": "person",
    "displayName": "Martin Smith",
    "url": "http://example.org/martin",
    "image": {
      "mediaType": "image/jpeg",
      "url": "http://example.org/martin/image.jpg",
      "height": 250,
      "width": 250
    }
  },
  "cc": {
    "id": "urn:example:person:martin",
    "objectType": "person",
    "displayName": "Martin Smith",
    "url": "http://example.org/martin",
    "image": {
      "mediaType": "image/jpeg",
      "url": "http://example.org/martin/image.jpg",
      "height": 250,
      "width": 250
    }
  },
  "bto": {
    "id": "urn:example:person:martin",
    "objectType": "person",
    "displayName": "Martin Smith",
    "url": "http://example.org/martin",
    "image": {
      "mediaType": "image/jpeg",
      "url": "http://example.org/martin/image.jpg",
      "height": 250,
      "width": 250
    }
  },
  "bcc": {
    "id": "urn:example:person:martin",
    "objectType": "person",
    "displayName": "Martin Smith",
    "url": "http://example.org/martin",
    "image": {
      "mediaType": "image/jpeg",
      "url": "http://example.org/martin/image.jpg",
      "height": 250,
      "width": 250
    }
  },
  "language": "en",
  "published": "2011-02-10T15:04:55Z"
}
JSON;

        $this->assertRecordEqualsContent($activity, $content);

        $this->assertEquals('post', $activity->getVerb());
        $this->assertEquals(new DateTime('2011-02-10T15:04:55Z'), $activity->getPublished());
        $this->assertEquals('en', $activity->getLanguage());
        $this->assertEquals($actor, $activity->getActor());
        $this->assertEquals($object, $activity->getObject());
        $this->assertEquals($target, $activity->getTarget());
        $this->assertEquals($image, $activity->getResult());
        $this->assertEquals(0.5, $activity->getPriority());
        $this->assertEquals($actor, $activity->getTo());
        $this->assertEquals($actor, $activity->getCc());
        $this->assertEquals($actor, $activity->getBto());
        $this->assertEquals($actor, $activity->getBcc());
    }

    public function testComplexActivity()
    {
        $generator = new ObjectType();
        $generator->setUrl('http://example.org/activities-app');

        $provider = new ObjectType();
        $provider->setUrl('http://example.org/activity-stream');

        $image = new ObjectType();
        $image->setUrl('http://example.org/martin/image');
        $image->setMediaType('image/jpeg');
        $image->setWidth(250);
        $image->setHeight(250);

        $actor = new ObjectType();
        $actor->setUrl('http://example.org/martin');
        $actor->setObjectType('person');
        $actor->setId('urn:example:person:martin');
        $actor->setImage($image);
        $actor->setDisplayName('Martin Smith');

        $image = new ObjectType();
        $image->setUrl('http://example.org/album/my_fluffy_cat_thumb.jpg');
        $image->setMediaType('image/jpeg');
        $image->setWidth(250);
        $image->setHeight(250);

        $objectType = new ObjectType();
        $objectType->setId('http://example.org/Photo');
        $objectType->setDisplayName('Photo');

        $object = new ObjectType();
        $object->setUrl('http://example.org/album/my_fluffy_cat.jpg');
        $object->setObjectType($objectType);
        $object->setId('urn:example:album:abc123/my_fluffy_cat');
        $object->setImage($image);

        $image = new ObjectType();
        $image->setUrl('http://example.org/album/thumbnail.jpg');
        $image->setMediaType('image/jpeg');
        $image->setWidth(250);
        $image->setHeight(250);

        $objectType = new ObjectType();
        $objectType->setId('http://example.org/PhotoAlbum');
        $objectType->setDisplayName('Photo-Album');

        $displayName = new \stdClass();
        $displayName->en = 'Martin\'s Photo Album';
        $displayName->ga = 'Grianghraif Mairtin';

        $target = new ObjectType();
        $target->setUrl('http://example.org/album/');
        $target->setObjectType($objectType);
        $target->setId('urn:example.org:album:abc123');
        $target->setDisplayName($displayName);
        $target->setImage($image);

        $activity = new Activity();
        $activity->setVerb('post');
        $activity->setLanguage('en');
        $activity->setPublished(new DateTime('2011-02-10T15:04:55Z'));
        $activity->setGenerator('http://example.org/activities-app');
        $activity->setProvider('http://example.org/activity-stream');

        $displayName = new \stdClass();
        $displayName->en = 'Martin posted a new video to his album.';
        $displayName->ga = 'Martin phost le fisean nua a albam.';

        $activity->setDisplayName($displayName);
        $activity->setActor($actor);
        $activity->setObject($object);
        $activity->setTarget($target);

        $collection = new Collection();
        $collection->setTotalItems(1);
        $collection->add($activity);

        $content = <<<JSON
{
  "totalItems": 1,
  "items": [{
      "verb": "post",
      "language": "en",
      "published": "2011-02-10T15:04:55Z",
      "generator": "http://example.org/activities-app",
      "provider": "http://example.org/activity-stream",
      "displayName": {
        "en": "Martin posted a new video to his album.",
        "ga": "Martin phost le fisean nua a albam."
      },
      "actor": {
        "objectType": "person",
        "id": "urn:example:person:martin",
        "displayName": "Martin Smith",
        "url": "http://example.org/martin",
        "image": {
          "url": "http://example.org/martin/image",
          "mediaType": "image/jpeg",
          "width": 250,
          "height": 250
        }
      },
      "object": {
        "objectType": {
          "id": "http://example.org/Photo",
          "displayName": "Photo"
        },
        "id": "urn:example:album:abc123/my_fluffy_cat",
        "url": "http://example.org/album/my_fluffy_cat.jpg",
        "image": {
          "url": "http://example.org/album/my_fluffy_cat_thumb.jpg",
          "mediaType": "image/jpeg",
          "width": 250,
          "height": 250
        }
      },
      "target": {
        "objectType": {
          "id": "http://example.org/PhotoAlbum",
          "displayName": "Photo-Album"
        },
        "id": "urn:example.org:album:abc123",
        "url": "http://example.org/album/",
        "displayName": {
          "en": "Martin's Photo Album",
          "ga": "Grianghraif Mairtin"
        },
        "image": {
          "url": "http://example.org/album/thumbnail.jpg",
          "mediaType": "image/jpeg",
          "width": 250,
          "height": 250
        }
      }
    }]
}
JSON;

        $this->assertRecordEqualsContent($collection, $content);
    }
}
