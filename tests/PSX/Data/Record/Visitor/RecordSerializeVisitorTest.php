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
 * RecordSerializeVisitorTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class RecordSerializeVisitorTest extends VisitorTestCase
{
    public function testTraverse()
    {
        $visitor = new RecordSerializeVisitor();

        $graph = new GraphTraverser();
        $graph->traverse($this->getRecord(), $visitor);

        $this->assertEquals($this->getExpected(), $visitor->getObject());
    }

    protected function getExpected()
    {
        $person = new Record();
        $person->setProperty('title', 'Foo');

        $category = new Record();
        $category->setProperty('general', new Record());
        $category['general']->setProperty('news', new Record());
        $category['general']['news']->setProperty('technic', 'Foo');

        $entry = array();
        $entry[0] = new Record();
        $entry[0]->setProperty('title', 'bar');
        $entry[1] = new Record();
        $entry[1]->setProperty('title', 'foo');

        $record = new Record();
        $record->setProperty('id', 1);
        $record->setProperty('title', 'foobar');
        $record->setProperty('active', true);
        $record->setProperty('disabled', false);
        $record->setProperty('rating', 12.45);
        $record->setProperty('date', '2014-01-01T12:34:47+01:00');
        $record->setProperty('href', 'http://foo.com');
        $record->setProperty('person', $person);
        $record->setProperty('category', $category);
        $record->setProperty('tags', ['bar', 'foo', 'test']);
        $record->setProperty('entry', $entry);

        return $record;
    }
}
