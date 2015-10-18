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

namespace PSX\Data\Record;

use PSX\Data\RecordInterface;
use PSX\Uri;

/**
 * GraphTraverserTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class GraphTraverserTest extends Visitor\VisitorTestCase
{
    public function testTraverse()
    {
        $record = $this->getRecord();
        $graph  = new GraphTraverser();
        $graph->traverse($record, $this->getVisitorMock($record));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testTraverseNoObject()
    {
        $record = $this->getRecord();
        $graph  = new GraphTraverser();
        $graph->traverse('foo', new SpyVisitor());
    }

    public function testTraverseCallStack()
    {
        $record  = $this->getRecord();
        $visitor = new SpyVisitor();
        $graph   = new GraphTraverser();
        $graph->traverse($record, $visitor);

        $expect = array(
            '0 PSX\Data\Record\SpyVisitor::visitObjectStart',
            '1 PSX\Data\Record\SpyVisitor::visitObjectValueStart',
            '2 PSX\Data\Record\SpyVisitor::visitValue',
            '3 PSX\Data\Record\SpyVisitor::visitObjectValueEnd',
            '4 PSX\Data\Record\SpyVisitor::visitObjectValueStart',
            '5 PSX\Data\Record\SpyVisitor::visitValue',
            '6 PSX\Data\Record\SpyVisitor::visitObjectValueEnd',
            '7 PSX\Data\Record\SpyVisitor::visitObjectValueStart',
            '8 PSX\Data\Record\SpyVisitor::visitValue',
            '9 PSX\Data\Record\SpyVisitor::visitObjectValueEnd',
            '10 PSX\Data\Record\SpyVisitor::visitObjectValueStart',
            '11 PSX\Data\Record\SpyVisitor::visitValue',
            '12 PSX\Data\Record\SpyVisitor::visitObjectValueEnd',
            '13 PSX\Data\Record\SpyVisitor::visitObjectValueStart',
            '14 PSX\Data\Record\SpyVisitor::visitValue',
            '15 PSX\Data\Record\SpyVisitor::visitObjectValueEnd',
            '16 PSX\Data\Record\SpyVisitor::visitObjectValueStart',
            '17 PSX\Data\Record\SpyVisitor::visitValue',
            '18 PSX\Data\Record\SpyVisitor::visitObjectValueEnd',
            '19 PSX\Data\Record\SpyVisitor::visitObjectValueStart',
            '20 PSX\Data\Record\SpyVisitor::visitValue',
            '21 PSX\Data\Record\SpyVisitor::visitObjectValueEnd',
            '22 PSX\Data\Record\SpyVisitor::visitObjectValueStart',
            '23 PSX\Data\Record\SpyVisitor::visitObjectStart',
            '24 PSX\Data\Record\SpyVisitor::visitObjectValueStart',
            '25 PSX\Data\Record\SpyVisitor::visitValue',
            '26 PSX\Data\Record\SpyVisitor::visitObjectValueEnd',
            '27 PSX\Data\Record\SpyVisitor::visitObjectEnd',
            '28 PSX\Data\Record\SpyVisitor::visitObjectValueEnd',
            '29 PSX\Data\Record\SpyVisitor::visitObjectValueStart',
            '30 PSX\Data\Record\SpyVisitor::visitObjectStart',
            '31 PSX\Data\Record\SpyVisitor::visitObjectValueStart',
            '32 PSX\Data\Record\SpyVisitor::visitObjectStart',
            '33 PSX\Data\Record\SpyVisitor::visitObjectValueStart',
            '34 PSX\Data\Record\SpyVisitor::visitObjectStart',
            '35 PSX\Data\Record\SpyVisitor::visitObjectValueStart',
            '36 PSX\Data\Record\SpyVisitor::visitValue',
            '37 PSX\Data\Record\SpyVisitor::visitObjectValueEnd',
            '38 PSX\Data\Record\SpyVisitor::visitObjectEnd',
            '39 PSX\Data\Record\SpyVisitor::visitObjectValueEnd',
            '40 PSX\Data\Record\SpyVisitor::visitObjectEnd',
            '41 PSX\Data\Record\SpyVisitor::visitObjectValueEnd',
            '42 PSX\Data\Record\SpyVisitor::visitObjectEnd',
            '43 PSX\Data\Record\SpyVisitor::visitObjectValueEnd',
            '44 PSX\Data\Record\SpyVisitor::visitObjectValueStart',
            '45 PSX\Data\Record\SpyVisitor::visitArrayStart',
            '46 PSX\Data\Record\SpyVisitor::visitArrayValueStart',
            '47 PSX\Data\Record\SpyVisitor::visitValue',
            '48 PSX\Data\Record\SpyVisitor::visitArrayValueEnd',
            '49 PSX\Data\Record\SpyVisitor::visitArrayValueStart',
            '50 PSX\Data\Record\SpyVisitor::visitValue',
            '51 PSX\Data\Record\SpyVisitor::visitArrayValueEnd',
            '52 PSX\Data\Record\SpyVisitor::visitArrayValueStart',
            '53 PSX\Data\Record\SpyVisitor::visitValue',
            '54 PSX\Data\Record\SpyVisitor::visitArrayValueEnd',
            '55 PSX\Data\Record\SpyVisitor::visitArrayEnd',
            '56 PSX\Data\Record\SpyVisitor::visitObjectValueEnd',
            '57 PSX\Data\Record\SpyVisitor::visitObjectValueStart',
            '58 PSX\Data\Record\SpyVisitor::visitArrayStart',
            '59 PSX\Data\Record\SpyVisitor::visitArrayValueStart',
            '60 PSX\Data\Record\SpyVisitor::visitObjectStart',
            '61 PSX\Data\Record\SpyVisitor::visitObjectValueStart',
            '62 PSX\Data\Record\SpyVisitor::visitValue',
            '63 PSX\Data\Record\SpyVisitor::visitObjectValueEnd',
            '64 PSX\Data\Record\SpyVisitor::visitObjectEnd',
            '65 PSX\Data\Record\SpyVisitor::visitArrayValueEnd',
            '66 PSX\Data\Record\SpyVisitor::visitArrayValueStart',
            '67 PSX\Data\Record\SpyVisitor::visitObjectStart',
            '68 PSX\Data\Record\SpyVisitor::visitObjectValueStart',
            '69 PSX\Data\Record\SpyVisitor::visitValue',
            '70 PSX\Data\Record\SpyVisitor::visitObjectValueEnd',
            '71 PSX\Data\Record\SpyVisitor::visitObjectEnd',
            '72 PSX\Data\Record\SpyVisitor::visitArrayValueEnd',
            '73 PSX\Data\Record\SpyVisitor::visitArrayEnd',
            '74 PSX\Data\Record\SpyVisitor::visitObjectValueEnd',
            '75 PSX\Data\Record\SpyVisitor::visitObjectEnd',
        );

        $this->assertEquals($expect, $visitor->getStack());
    }

    public function getVisitorMock(RecordInterface $record)
    {
        $visitor = $this->getMock('PSX\Data\Record\VisitorInterface', array(
            'visitObjectStart',
            'visitObjectEnd',
            'visitObjectValueStart',
            'visitObjectValueEnd',
            'visitArrayStart',
            'visitArrayEnd',
            'visitArrayValueStart',
            'visitArrayValueEnd',
            'visitValue',
        ));

        $visitor->expects($this->at(0))
            ->method('visitObjectStart')
            ->with($this->equalTo('record'));

        $visitor->expects($this->at(1))
            ->method('visitObjectValueStart')
            ->with($this->equalTo('id'), $this->equalTo(1));

        $visitor->expects($this->at(2))
            ->method('visitValue')
            ->with($this->equalTo(1));

        $visitor->expects($this->at(4))
            ->method('visitObjectValueStart')
            ->with($this->equalTo('title'), $this->equalTo('foobar'));

        $visitor->expects($this->at(5))
            ->method('visitValue')
            ->with($this->equalTo('foobar'));

        $visitor->expects($this->at(7))
            ->method('visitObjectValueStart')
            ->with($this->equalTo('active'), $this->equalTo(true));

        $visitor->expects($this->at(8))
            ->method('visitValue')
            ->with($this->equalTo(true));

        $visitor->expects($this->at(10))
            ->method('visitObjectValueStart')
            ->with($this->equalTo('disabled'), $this->equalTo(false));

        $visitor->expects($this->at(11))
            ->method('visitValue')
            ->with($this->equalTo(false));

        $visitor->expects($this->at(13))
            ->method('visitObjectValueStart')
            ->with($this->equalTo('rating'), $this->equalTo(12.45));

        $visitor->expects($this->at(14))
            ->method('visitValue')
            ->with($this->equalTo(12.45));

        $visitor->expects($this->at(16))
            ->method('visitObjectValueStart')
            ->with($this->equalTo('date'), $this->equalTo(new \DateTime('2014-01-01T12:34:47+01:00')));

        $visitor->expects($this->at(17))
            ->method('visitValue')
            ->with($this->equalTo(new \DateTime('2014-01-01T12:34:47+01:00')));

        $visitor->expects($this->at(19))
            ->method('visitObjectValueStart')
            ->with($this->equalTo('href'), $this->equalTo(new Uri('http://foo.com')));

        $visitor->expects($this->at(20))
            ->method('visitValue')
            ->with($this->equalTo(new Uri('http://foo.com')));

        // person
        $visitor->expects($this->at(22))
            ->method('visitObjectValueStart')
            ->with($this->equalTo('person'), $this->equalTo($record->getPerson()));

        $visitor->expects($this->at(23))
            ->method('visitObjectStart')
            ->with($this->equalTo('person'));

        $visitor->expects($this->at(24))
            ->method('visitObjectValueStart')
            ->with($this->equalTo('title'), $this->equalTo('Foo'));

        $visitor->expects($this->at(25))
            ->method('visitValue')
            ->with($this->equalTo('Foo'));

        // category
        $visitor->expects($this->at(29))
            ->method('visitObjectValueStart')
            ->with($this->equalTo('category'), $this->equalTo($record->getCategory()));

        $visitor->expects($this->at(30))
            ->method('visitObjectStart')
            ->with($this->equalTo('category'));

        $visitor->expects($this->at(31))
            ->method('visitObjectValueStart')
            ->with($this->equalTo('general'), $this->equalTo($record->getCategory()->getGeneral()));

        $visitor->expects($this->at(32))
            ->method('visitObjectStart')
            ->with($this->equalTo('category'));

        $visitor->expects($this->at(33))
            ->method('visitObjectValueStart')
            ->with($this->equalTo('news'), $this->equalTo($record->getCategory()->getGeneral()->getNews()));

        $visitor->expects($this->at(34))
            ->method('visitObjectStart')
            ->with($this->equalTo('category'));

        $visitor->expects($this->at(35))
            ->method('visitObjectValueStart')
            ->with($this->equalTo('technic'), $this->equalTo('Foo'));

        $visitor->expects($this->at(36))
            ->method('visitValue')
            ->with($this->equalTo('Foo'));

        // tags
        $visitor->expects($this->at(44))
            ->method('visitObjectValueStart')
            ->with($this->equalTo('tags'), $this->equalTo($record->getTags()));

        $visitor->expects($this->at(46))
            ->method('visitArrayValueStart')
            ->with($this->equalTo('bar'));

        $visitor->expects($this->at(47))
            ->method('visitValue')
            ->with($this->equalTo('bar'));

        $visitor->expects($this->at(49))
            ->method('visitArrayValueStart')
            ->with($this->equalTo('foo'));

        $visitor->expects($this->at(50))
            ->method('visitValue')
            ->with($this->equalTo('foo'));

        $visitor->expects($this->at(52))
            ->method('visitArrayValueStart')
            ->with($this->equalTo('test'));

        $visitor->expects($this->at(53))
            ->method('visitValue')
            ->with($this->equalTo('test'));

        // entry
        $visitor->expects($this->at(57))
            ->method('visitObjectValueStart')
            ->with($this->equalTo('entry'), $this->equalTo($record->getEntry()));

        $visitor->expects($this->at(59))
            ->method('visitArrayValueStart')
            ->with($this->equalTo($record->getEntry()[0]));

        $visitor->expects($this->at(60))
            ->method('visitObjectStart')
            ->with($this->equalTo('entry'));

        $visitor->expects($this->at(61))
            ->method('visitObjectValueStart')
            ->with($this->equalTo('title'), $this->equalTo($record->getEntry()[0]->getTitle()));

        $visitor->expects($this->at(62))
            ->method('visitValue')
            ->with($this->equalTo($record->getEntry()[0]->getTitle()));

        $visitor->expects($this->at(66))
            ->method('visitArrayValueStart')
            ->with($this->equalTo($record->getEntry()[1]));

        $visitor->expects($this->at(67))
            ->method('visitObjectStart')
            ->with($this->equalTo('entry'));

        $visitor->expects($this->at(68))
            ->method('visitObjectValueStart')
            ->with($this->equalTo('title'), $this->equalTo($record->getEntry()[1]->getTitle()));

        $visitor->expects($this->at(69))
            ->method('visitValue')
            ->with($this->equalTo($record->getEntry()[1]->getTitle()));

        return $visitor;
    }
}

class SpyVisitor implements VisitorInterface
{
    protected $stack = array();
    protected $i     = 0;

    public function getStack()
    {
        return $this->stack;
    }

    public function visitObjectStart($name)
    {
        $this->stack[] = $this->i++ . ' ' . __METHOD__;
    }

    public function visitObjectEnd()
    {
        $this->stack[] = $this->i++ . ' ' . __METHOD__;
    }

    public function visitObjectValueStart($key, $value)
    {
        $this->stack[] = $this->i++ . ' ' . __METHOD__;
    }

    public function visitObjectValueEnd()
    {
        $this->stack[] = $this->i++ . ' ' . __METHOD__;
    }

    public function visitArrayStart()
    {
        $this->stack[] = $this->i++ . ' ' . __METHOD__;
    }

    public function visitArrayEnd()
    {
        $this->stack[] = $this->i++ . ' ' . __METHOD__;
    }

    public function visitArrayValueStart($value)
    {
        $this->stack[] = $this->i++ . ' ' . __METHOD__;
    }

    public function visitArrayValueEnd()
    {
        $this->stack[] = $this->i++ . ' ' . __METHOD__;
    }

    public function visitValue($value)
    {
        $this->stack[] = $this->i++ . ' ' . __METHOD__;
    }
}
