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

namespace PSX\Data\Record\Visitor;

use PSX\Data\Record;
use PSX\Data\Record\GraphTraverser;
use PSX\Test\Assert;

/**
 * TextWriterVisitorTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class TextWriterVisitorTest extends VisitorTestCase
{
    public function testTraverse()
    {
        $visitor = new TextWriterVisitor();

        $graph = new GraphTraverser();
        $graph->traverse($this->getRecord(), $visitor);

        Assert::assertStringMatchIgnoreWhitespace($this->getExpected(), $visitor->getOutput());
    }

    public function testTraverseTextLong()
    {
        $visitor = new TextWriterVisitor();
        $record  = new Record('foo', array(
            'title' => 'Lorem ipsum dolor' . "\n" . 'sit amet, consetetur sadipscin'
        ));

        $graph = new GraphTraverser();
        $graph->traverse($record, $visitor);

        $except = <<<TEXT
Object(foo){
    title = Lorem ipsum dolor sit amet, cons (...)
}

TEXT;

        Assert::assertStringMatchIgnoreWhitespace($except, $visitor->getOutput());
    }

    protected function getExpected()
    {
        return <<<TEXT
Object(record){
    id = 1
    title = foobar
    active = true
    disabled = false
    rating = 12.45
    date = 2014-01-01T12:34:47+01:00
    href = http://foo.com
    person = Object(person){
        title = Foo
    }
    category = Object(category){
        general = Object(category){
            news = Object(category){
                technic = Foo
            }
        }
    }
    tags = Array[
        bar
        foo
        test
    ]
    entry = Array[
        Object(entry){
            title = bar
        }
        Object(entry){
            title = foo
        }
    ]
}

TEXT;
    }
}
