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

namespace PSX\ActivityStream;

use PSX\DateTime;
use PSX\Data\Writer;
use PSX\Data\SerializeTestAbstract;

/**
 * ActivityTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ActivityTest extends SerializeTestAbstract
{
	public function testNormalActivity()
	{
		$image = new MediaLink();
		$image->setUrl('http://example.org/martin/image');
		$image->setWidth(250);
		$image->setHeight(250);

		$actor = new Object();
		$actor->setUrl('http://example.org/martin');
		$actor->setObjectType('person');
		$actor->setId('tag:example.org,2011:martin');
		$actor->setImage($image);
		$actor->setDisplayName('Martin Smith');

		$object = new Object();
		$object->setUrl('http://example.org/blog/2011/02/entry');
		$object->setId('tag:example.org,2011:abc123/xyz');

		$target = new Object();
		$target->setUrl('http://example.org/blog/');
		$target->setObjectType('blog');
		$target->setId('tag:example.org,2011:abc123');
		$target->setDisplayName('Martin\'s Blog');

		$activity = new Activity();
		$activity->setPublished(new DateTime('2011-02-10T15:04:55Z'));
		$activity->setActor($actor);
		$activity->setVerb('post');
		$activity->setObject($object);
		$activity->setTarget($target);

		$content = <<<JSON
  {
    "published": "2011-02-10T15:04:55+00:00",
    "actor": {
      "url": "http://example.org/martin",
      "objectType" : "person",
      "id": "tag:example.org,2011:martin",
      "image": {
        "url": "http://example.org/martin/image",
        "width": 250,
        "height": 250
      },
      "displayName": "Martin Smith"
    },
    "verb": "post",
    "object" : {
      "url": "http://example.org/blog/2011/02/entry",
      "id": "tag:example.org,2011:abc123/xyz"
    },
    "target" : {
      "url": "http://example.org/blog/",
      "objectType": "blog",
      "id": "tag:example.org,2011:abc123",
      "displayName": "Martin's Blog"
    }
  }
JSON;

		$this->assertRecordEqualsContent($activity, $content);
	}

	public function testComplexActivity()
	{
		$generator = new Source();
		$generator->setUrl('http://example.org/activities-app');

		$provider = new Source();
		$provider->setUrl('http://example.org/activity-stream');

		$image = new MediaLink();
		$image->setUrl('http://example.org/martin/image');
		$image->setWidth(250);
		$image->setHeight(250);

		$actor = new Object();
		$actor->setUrl('http://example.org/martin');
		$actor->setObjectType('person');
		$actor->setId('tag:example.org,2011:martin');
		$actor->setImage($image);
		$actor->setDisplayName('Martin Smith');

		$image = new MediaLink();
		$image->setUrl('http://example.org/album/my_fluffy_cat_thumb.jpg');
		$image->setWidth(250);
		$image->setHeight(250);

		$object = new Object();
		$object->setUrl('http://example.org/album/my_fluffy_cat.jpg');
		$object->setObjectType('photo');
		$object->setId('tag:example.org,2011:my_fluffy_cat');
		$object->setImage($image);

		$image = new MediaLink();
		$image->setUrl('http://example.org/album/thumbnail.jpg');
		$image->setWidth(250);
		$image->setHeight(250);

		$target = new Object();
		$target->setUrl('http://example.org/album/');
		$target->setObjectType('photo-album');
		$target->setId('tag:example.org,2011:abc123');
		$target->setDisplayName('Martin\'s Photo Album');
		$target->setImage($image);

		$activity = new Activity();
		$activity->setPublished(new DateTime('2011-02-10T15:04:55Z'));
		$activity->setGenerator($generator);
		$activity->setProvider($provider);
		$activity->setTitle('Martin posted a new video to his album.');
		$activity->setActor($actor);
		$activity->setVerb('post');
		$activity->setObject($object);
		$activity->setTarget($target);

		$collection = new Collection();
		$collection->setItems(array($activity));

		$content = <<<JSON
  {
    "items" : [
      {
        "published": "2011-02-10T15:04:55+00:00",
        "generator": {
          "url": "http://example.org/activities-app"
        },
        "provider": {
          "url": "http://example.org/activity-stream"
        },
        "title": "Martin posted a new video to his album.",
        "actor": {
          "url": "http://example.org/martin",
          "objectType": "person",
          "id": "tag:example.org,2011:martin",
          "image": {
            "url": "http://example.org/martin/image",
            "width": 250,
            "height": 250
          },
          "displayName": "Martin Smith"
        },
        "verb": "post",
        "object" : {
          "url": "http://example.org/album/my_fluffy_cat.jpg",
          "objectType": "photo",
          "id": "tag:example.org,2011:my_fluffy_cat",
          "image": {
            "url": "http://example.org/album/my_fluffy_cat_thumb.jpg",
            "width": 250,
            "height": 250
          }
        },
        "target": {
          "url": "http://example.org/album/",
          "objectType": "photo-album",
          "id": "tag:example.org,2011:abc123",
          "displayName": "Martin's Photo Album",
          "image": {
            "url": "http://example.org/album/thumbnail.jpg",
            "width": 250,
            "height": 250
          }
        }
      }
    ]
  }
JSON;

		$this->assertRecordEqualsContent($collection, $content);	
	}
}




