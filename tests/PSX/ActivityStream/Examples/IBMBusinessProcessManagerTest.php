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
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 * @see     http://www.w3.org/wiki/Activity_Streams
 */
class IBMBusinessProcessManagerTest extends SerializeTestAbstract
{
    public function testStream()
    {
        $image = new Object();
        $image->setHeight(32);
        $image->setWidth(32);

        $author = new Object();
        $author->setDisplayName('Internal TW Admin user');
        $author->setId('tw_admin');
        $author->setImage($image);
        $author->setObjectType('PERSON');

        $item = new Object();
        $item->setAuthor($author);
        $item->setContent('tagging Internal TW Admin user user');
        $item->setObjectType('COMMENT');
        $item->setPublished(new DateTime('2012-01-09T16:18:44+00:00'));

        $replies = new Collection();
        $replies->setItems([$item]);

        $actor = new Object();
        $actor->setId('tw_admin');
        $actor->setDisplayName('Internal TW Admin user');
        $actor->setObjectType('PERSON');

        $object = new Object();
        $object->setDisplayName('Task: Submit requisition');
        $object->setId('2078.3');
        $object->setObjectType('ibm.bpm.task');

        $activity = new Activity();
        $activity->setActor($actor);
        $activity->setContent('Internal TW Admin user completed the task titled Task: Submit requisition and associated with the Submit job requisition activity.');
        $activity->setObject($object);
        $activity->setPublished(new DateTime('2012-01-09T09:58:00+00:00'));
        $activity->setVerb('POST');
        $activity->setReplies($replies);

        $collection = new Collection();
        $collection->setTotalItems(1);
        $collection->setItems([$activity]);

        $content = <<<JSON
{
    "totalItems":1,
    "items":[
        {
            "actor":{
                "displayName":"Internal TW Admin user",
                "id":"tw_admin",
                "objectType":"PERSON"
            },
            "content":"Internal TW Admin user completed the task titled Task: Submit requisition and associated with the Submit job requisition activity.",
            "object":{
                "displayName":"Task: Submit requisition",
                "id":"2078.3",
                "objectType":"ibm.bpm.task"
            },
            "published":"2012-01-09T09:58:00Z",
            "verb":"POST",
            "replies":{
                "items":[
                    {
                        "author":{
                            "displayName":"Internal TW Admin user",
                            "id":"tw_admin",
                            "image":{
                                "height":32,
                                "width":32
                            },
                            "objectType":"PERSON"
                        },
                        "content":"tagging Internal TW Admin user user",
                        "objectType":"COMMENT",
                        "published":"2012-01-09T16:18:44Z"
                    }
                ]
            }
        }
    ]
}
JSON;

        $this->assertRecordEqualsContent($collection, $content);
    }
}
