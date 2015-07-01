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

namespace PSX\Data\Record\Visitor;

use PSX\Data\Record;
use PSX\Data\Record\GraphTraverser;

/**
 * StdClassSerializeVisitorTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class StdClassSerializeVisitorTest extends VisitorTestCase
{
    public function testTraverse()
    {
        $visitor = new StdClassSerializeVisitor();

        $graph = new GraphTraverser();
        $graph->traverse($this->getRecord(), $visitor);

        $this->assertEquals($this->getExpected(), $visitor->getObject());
    }

    protected function getExpected()
    {
        $person = new \stdClass();
        $person->title = 'Foo';

        $category = new \stdClass();
        $category->general = new \stdClass();
        $category->general->news = new \stdClass();
        $category->general->news->technic = 'Foo';

        $entry = array();
        $entry[0] = new \stdClass();
        $entry[0]->title = 'bar';
        $entry[1] = new \stdClass();
        $entry[1]->title = 'foo';

        $record = new \stdClass();
        $record->id = 1;
        $record->title = 'foobar';
        $record->active = true;
        $record->disabled = false;
        $record->rating = 12.45;
        $record->date = '2014-01-01T12:34:47+01:00';
        $record->href = 'http://foo.com';
        $record->person = $person;
        $record->category = $category;
        $record->tags = array('bar', 'foo', 'test');
        $record->entry = $entry;

        return $record;
    }
}
