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

namespace PSX\Model\Tests\ActivityStream\Examples;

use DateTime;
use PSX\Data\Tests\SerializeTestAbstract;
use PSX\Model\ActivityStream\ObjectType;
use PSX\Model\ActivityStream\Collection;
use PSX\Model\ActivityStream\Activity;

/**
 * IBMConnectionTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 * @see     http://www.w3.org/wiki/Activity_Streams
 */
class IBMConnectionTest extends SerializeTestAbstract
{
    public function testStream()
    {
        $author = new ObjectType();
        $author->setId('12345678-8f0a-1028-xxxx-db07163b51b2');
        $author->setDisplayName('Joseph Bloggs');

        $target = new ObjectType();
        $target->setSummary('Top App Entry');
        $target->setObjectType('note');
        $target->setAuthor($author);
        $target->setUpdated(new DateTime('2011-11-21T15:08:44+00:00'));
        $target->setId('87d7a7fb-af22-403b-ab0d-d101d9caac4f');
        $target->setDisplayName('Joseph Bloggs');
        $target->setPublished(new DateTime('2011-11-21T15:08:44+00:00'));
        $target->setUrl('http://www.example.org/topapp/1028-xxxx-db07163b51b2');

        $provider = new ObjectType();
        $provider->setId('http://www.ibm.com/xmlns/prod/sn');
        $provider->setDisplayName('IBM Connections - News Service');
        $provider->setUrl('http://www.example.org/news');

        $generator = new ObjectType();
        $generator->setImage('http://www.example.org/topapp/images/icon.png');
        $generator->setId('topapp');
        $generator->setDisplayName('Top Application');
        $generator->setUrl('http://www.example.org/topapp');

        $actor = new ObjectType();
        $actor->setId('12345678-8f0a-1028-xxxx-db07163b51b2');
        $actor->setDisplayName('Joseph Bloggs');

        $author = new ObjectType();
        $author->setId('12345678-8f0a-1028-xxxx-db07163b51b2');
        $author->setDisplayName('Joseph Bloggs');

        $object = new ObjectType();
        $object->setSummary('This was my first comment');
        $object->setObjectType('comment');
        $object->setAuthor($author);
        $object->setId('5369ea82-d791-46cb-a87a-3696ff90d8f3');
        $object->setUrl('http://www.example.org/topapp/1028-xxxx-db07163b51b2/comments/1');

        $activity = new Activity();
        $activity->setPublished(new DateTime('2011-11-21T15:14:06+00:00'));
        $activity->setUrl('http://www.example.org/connections/opensocial/rest/activitystreams/@me/@all/@all/86c62a05-61de-4658-97a7-16e7ccf72e78');
        $activity->setTarget($target);
        $activity->setProvider($provider);
        $activity->setGenerator($generator);
        $activity->setActor($actor);
        $activity->setTitle('Joseph Bloggs commented on their own Top App entry.');
        $activity->setContent('<span class="vcard"><a class="fn url" title="This is a link to the profile of Joseph Bloggs." href="http://www.example.org/profiles/html/profileView.do?userid=12345678-8f0a-1028-xxxx-db07163b51b2"><span class="photo" src="http://www.example.org/profiles/photo.do?userid=12345678-8f0a-1028-xxxx-db07163b51b2" alt="This is a photo of Joseph Bloggs." style="display : none"></span>Joseph Bloggs</a><span class="x-lconn-userid" style="display : none">12345678-8f0a-1028-xxxx-db07163b51b2</span></span> commented on their own Top App entry.');
        $activity->setId('urn:lsid:ibm.com:activitystreams:86c62a05-61de-4658-97a7-16e7ccf72e78');
        $activity->setUpdated(new DateTime('2011-11-21T15:14:07+00:00'));
        $activity->setObject($object);
        $activity->setVerb('post');

        $collection = new Collection();
        $collection->setTotalItems(1);
        $collection->setItems([$activity]);

        $content = <<<JSON
{
    "totalItems": 1,
    "items": [
        {
        "objectType": "activity",
        "published": "2011-11-21T15:14:06Z",
        "url": "http://www.example.org/connections/opensocial/rest/activitystreams/@me/@all/@all/86c62a05-61de-4658-97a7-16e7ccf72e78",
        "target": {
            "summary": "Top App Entry",
            "objectType": "note",
            "author": {
                "id": "12345678-8f0a-1028-xxxx-db07163b51b2",
                "displayName": "Joseph Bloggs"
            },
            "updated": "2011-11-21T15:08:44Z",
            "id": "87d7a7fb-af22-403b-ab0d-d101d9caac4f",
            "displayName": "Joseph Bloggs",
            "published": "2011-11-21T15:08:44Z",
            "url": "http://www.example.org/topapp/1028-xxxx-db07163b51b2"
        },
        "provider": {
            "id": "http://www.ibm.com/xmlns/prod/sn",
            "displayName": "IBM Connections - News Service",
            "url": "http://www.example.org/news"
        },
        "generator": {
            "image": "http://www.example.org/topapp/images/icon.png",
            "id": "topapp",
            "displayName": "Top Application",
            "url": "http://www.example.org/topapp"
        },
        "actor": {
            "id": "12345678-8f0a-1028-xxxx-db07163b51b2",
            "displayName": "Joseph Bloggs"
        },
        "title": "Joseph Bloggs commented on their own Top App entry.",
        "content": "<span class=\"vcard\"><a class=\"fn url\" title=\"This is a link to the profile of Joseph Bloggs.\" href=\"http://www.example.org/profiles/html/profileView.do?userid=12345678-8f0a-1028-xxxx-db07163b51b2\"><span class=\"photo\" src=\"http://www.example.org/profiles/photo.do?userid=12345678-8f0a-1028-xxxx-db07163b51b2\" alt=\"This is a photo of Joseph Bloggs.\" style=\"display : none\"></span>Joseph Bloggs</a><span class=\"x-lconn-userid\" style=\"display : none\">12345678-8f0a-1028-xxxx-db07163b51b2</span></span> commented on their own Top App entry.",
        "id": "urn:lsid:ibm.com:activitystreams:86c62a05-61de-4658-97a7-16e7ccf72e78",
        "updated": "2011-11-21T15:14:07Z",
        "object": {
            "summary": "This was my first comment",
            "objectType": "comment",
            "author": {
                "id": "12345678-8f0a-1028-xxxx-db07163b51b2",
                "displayName": "Joseph Bloggs"
            },
            "id": "5369ea82-d791-46cb-a87a-3696ff90d8f3",
            "url": "http://www.example.org/topapp/1028-xxxx-db07163b51b2/comments/1"
        },
        "verb": "post"
    }]
}
JSON;

        $this->assertRecordEqualsContent($collection, $content);
    }
}
