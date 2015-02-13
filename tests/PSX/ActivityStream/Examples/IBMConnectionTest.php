<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\ActivityStream\Examples;

use DateTime;
use PSX\ActivityStream\Object;
use PSX\ActivityStream\ObjectType\Activity;
use PSX\ActivityStream\ObjectType\Collection;
use PSX\Data\SerializeTestAbstract;

/**
 * IBMConnectionTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 * @see     http://www.w3.org/wiki/Activity_Streams
 */
class IBMConnectionTest extends SerializeTestAbstract
{
	public function testStream()
	{
		$items = array();

		$author = new Object();
		$author->setObjectType('person');
		$author->setId('12345678-8f0a-1028-xxxz-db07163b51b2');
		$author->setDisplayName('Joe Blogs');

		$item = new Object();
		$item->setContent('This was my first comment');
		$item->setAuthor($author);
		$item->setUpdated(new DateTime('2011-11-21T15:13:59+00:00'));
		$item->setId('f8f0e93f-e462-4ede-92cc-f6e8a1b7eb36');

		$items[] = $item;

		$author = new Object();
		$author->setObjectType('person');
		$author->setId('12345678-8f0a-1028-xxxy-db07163b51b2');
		$author->setDisplayName('Jane Doe');

		$item = new Object();
		$item->setContent('This was another comment');
		$item->setAuthor($author);
		$item->setUpdated(new DateTime('2011-11-21T15:14:06+00:00'));
		$item->setId('5369ea82-d791-46cb-a87a-3696ff90d8f3');

		$items[] = $item;

		$replies = new Collection();
		$replies->setItems($items);
		$replies->setTotalItems(0);

		$author = new Object();
		$author->setId('12345678-8f0a-1028-xxxx-db07163b51b2');
		$author->setDisplayName('Joseph Bloggs');

		$target = new Object();
		$target->setSummary('Top App Entry');
		$target->setReplies($replies);
		$target->setObjectType('note');
		$target->setAuthor($author);
		$target->setUpdated(new DateTime('2011-11-21T15:08:44+00:00'));
		$target->setId('87d7a7fb-af22-403b-ab0d-d101d9caac4f');
		$target->setDisplayName('Joseph Bloggs');
		$target->setPublished(new DateTime('2011-11-21T15:08:44+00:00'));
		$target->setUrl('http://www.example.org/topapp/1028-xxxx-db07163b51b2');

		$provider = new Object();
		$provider->setId('http://www.ibm.com/xmlns/prod/sn');
		$provider->setDisplayName('IBM Connections - News Service');
		$provider->setUrl('http://www.example.org/news');

		$image = new Object();
		$image->setUrl('http://www.example.org/topapp/images/icon.png'); 

		$generator = new Object();
		$generator->setImage($image);
		$generator->setId('topapp');
		$generator->setDisplayName('Top Application');
		$generator->setUrl('http://www.example.org/topapp');

		$actor = new Object();
		$actor->setId('12345678-8f0a-1028-xxxx-db07163b51b2');
		$actor->setDisplayName('Joseph Bloggs');

		$author = new Object();
		$author->setId('12345678-8f0a-1028-xxxx-db07163b51b2');
		$author->setDisplayName('Joseph Bloggs');

		$object = new Object();
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
		$collection->setStartIndex(0);
		$collection->setTotalItems(1);
		$collection->setItemsPerPage(1);
		$collection->setItems([$activity]);

		$content = <<<JSON
{
    "startIndex": 0,
    "totalItems": 1,
    "itemsPerPage": 1,
    "items": [
        {
        "published": "2011-11-21T15:14:06+00:00",
        "url": "http://www.example.org/connections/opensocial/rest/activitystreams/@me/@all/@all/86c62a05-61de-4658-97a7-16e7ccf72e78",
        "target": {
            "summary": "Top App Entry",
            "replies": {
                "items": [{
                    "content": "This was my first comment",
                    "author": {
                        "objectType": "person",
                        "id": "12345678-8f0a-1028-xxxz-db07163b51b2",
                        "displayName": "Joe Blogs"
                    },
                    "updated": "2011-11-21T15:13:59+00:00",
                    "id": "f8f0e93f-e462-4ede-92cc-f6e8a1b7eb36"
                 },
                 {
                    "content": "This was another comment",
                    "author": {
                        "objectType": "person",
                        "id": "12345678-8f0a-1028-xxxy-db07163b51b2",
                        "displayName": "Jane Doe"
                    },
                    "updated": "2011-11-21T15:14:06+00:00",
                    "id": "5369ea82-d791-46cb-a87a-3696ff90d8f3"
                 }],
                "totalItems": 0
            },
            "objectType": "note",
            "author": {
                "id": "12345678-8f0a-1028-xxxx-db07163b51b2",
                "displayName": "Joseph Bloggs"
            },
            "updated": "2011-11-21T15:08:44+00:00",
            "id": "87d7a7fb-af22-403b-ab0d-d101d9caac4f",
            "displayName": "Joseph Bloggs",
            "published": "2011-11-21T15:08:44+00:00",
            "url": "http://www.example.org/topapp/1028-xxxx-db07163b51b2"
        },
        "provider": {
            "id": "http://www.ibm.com/xmlns/prod/sn",
            "displayName": "IBM Connections - News Service",
            "url": "http://www.example.org/news"
        },
        "generator": {
            "image": {
                "url": "http://www.example.org/topapp/images/icon.png"
            },
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
        "updated": "2011-11-21T15:14:07+00:00",
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
