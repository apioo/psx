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

namespace PSX\Data\Tests;

use DateTime;
use PSX\Data\Record;
use PSX\Model\Common\ResultSet;
use PSX\Model\ActivityStream;

/**
 * WriterTestCase
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class WriterTestCase extends \PHPUnit_Framework_TestCase
{
    public function getRecord()
    {
        $record = new Record();
        $record->id = 1;
        $record->author = 'foo';
        $record->title = 'bar';
        $record->content = 'foobar';
        $record->date = new DateTime('2012-03-11 13:37:21');

        return $record;
    }

    public function getResultSet()
    {
        $entries = array();

        $record = new Record();
        $record->id = 1;
        $record->author = 'foo';
        $record->title = 'bar';
        $record->content = 'foobar';
        $record->date = new DateTime('2012-03-11 13:37:21');

        $entries[] = $record;

        $record = new Record();
        $record->id = 2;
        $record->author = 'foo';
        $record->title = 'bar';
        $record->content = 'foobar';
        $record->date = new DateTime('2012-03-11 13:37:21');

        $entries[] = $record;

        $record = new Record('collection');
        $record->totalResults = 2;
        $record->startIndex = 0;
        $record->itemsPerPage = 8;
        $record->entry = $entries;

        return $record;
    }

    public function getComplexRecord()
    {
        $actor = new Record();
        $actor->id = 'tag:example.org,2011:martin';
        $actor->objectType = 'person';
        $actor->displayName = 'Martin Smith';
        $actor->url = 'http://example.org/martin';

        $object = new Record();
        $object->id = 'tag:example.org,2011:abc123/xyz';
        $object->url = 'http://example.org/blog/2011/02/entry';

        $target = new Record();
        $target->id = 'tag:example.org,2011:abc123';
        $target->objectType = 'blog';
        $target->displayName = 'Martin\'s Blog';
        $target->url = 'http://example.org/blog/';

        $activity = new Record('activity');
        $activity->verb = 'post';
        $activity->actor = $actor;
        $activity->object = $object;
        $activity->target = $target;
        $activity->published = new DateTime('2011-02-10T15:04:55Z');

        return $activity;
    }

    public function getEmptyRecord()
    {
        return new Record('record', array());
    }

    abstract public function testWrite();
    abstract public function testWriteResultSet();
}
