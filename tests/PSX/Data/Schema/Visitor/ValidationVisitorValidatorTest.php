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

namespace PSX\Data\Schema\Visitor;

use PSX\Data\Schema\Property;
use PSX\DateTime;
use PSX\DateTime\Date;
use PSX\DateTime\Duration;
use PSX\DateTime\Time;

/**
 * Checks whether the validator is called for each property
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ValidationVisitorValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function testVisitArray()
    {
        $validator = $this->getMockBuilder('PSX\Validate\Validator')
            ->disableOriginalConstructor()
            ->setMethods(array('validateProperty'))
            ->getMock();

        $validator->expects($this->once())
            ->method('validateProperty')
            ->with($this->equalTo('/test'), $this->equalTo(array('foo')));

        $visitor = new ValidationVisitor();
        $visitor->setValidator($validator);

        $property = Property::getArray('test')->setPrototype(Property::getString('foo'));

        $visitor->visitArray(array('foo'), $property, '/test');
    }

    public function testVisitBoolean()
    {
        $validator = $this->getMockBuilder('PSX\Validate\Validator')
            ->disableOriginalConstructor()
            ->setMethods(array('validateProperty'))
            ->getMock();

        $validator->expects($this->once())
            ->method('validateProperty')
            ->with($this->equalTo('/test'), $this->equalTo(true));

        $visitor = new ValidationVisitor();
        $visitor->setValidator($validator);

        $property = Property::getBoolean('test');

        $visitor->visitBoolean(true, $property, '/test');
    }

    public function testVisitComplex()
    {
        $validator = $this->getMockBuilder('PSX\Validate\Validator')
            ->disableOriginalConstructor()
            ->setMethods(array('validateProperty'))
            ->getMock();

        $validator->expects($this->once())
            ->method('validateProperty')
            ->with($this->equalTo('/test'), $this->equalTo(new \stdClass()));

        $visitor = new ValidationVisitor();
        $visitor->setValidator($validator);

        $property = Property::getComplex('test')
            ->add(Property::getString('foo'))
            ->add(Property::getString('bar'));

        $visitor->visitComplex(new \stdClass(), $property, '/test');
    }

    public function testVisitDateTime()
    {
        $validator = $this->getMockBuilder('PSX\Validate\Validator')
            ->disableOriginalConstructor()
            ->setMethods(array('validateProperty'))
            ->getMock();

        $validator->expects($this->once())
            ->method('validateProperty')
            ->with($this->equalTo('/test'), $this->equalTo(new DateTime('2002-10-10T17:00:00')));

        $visitor = new ValidationVisitor();
        $visitor->setValidator($validator);

        $property = Property::getDateTime('test');

        $visitor->visitDateTime('2002-10-10T17:00:00', $property, '/test');
    }

    public function testVisitDate()
    {
        $validator = $this->getMockBuilder('PSX\Validate\Validator')
            ->disableOriginalConstructor()
            ->setMethods(array('validateProperty'))
            ->getMock();

        $validator->expects($this->once())
            ->method('validateProperty')
            ->with($this->equalTo('/test'), $this->equalTo(new Date('2000-01-01')));

        $visitor = new ValidationVisitor();
        $visitor->setValidator($validator);

        $property = Property::getDate('test');

        $visitor->visitDate('2000-01-01', $property, '/test');
    }

    public function testVisitDuration()
    {
        $validator = $this->getMockBuilder('PSX\Validate\Validator')
            ->disableOriginalConstructor()
            ->setMethods(array('validateProperty'))
            ->getMock();

        $validator->expects($this->once())
            ->method('validateProperty')
            ->with($this->equalTo('/test'), $this->equalTo(new Duration('P1D')));

        $visitor = new ValidationVisitor();
        $visitor->setValidator($validator);

        $property = Property::getDuration('test');

        $visitor->visitDuration('P1D', $property, '/test');
    }

    public function testVisitFloat()
    {
        $validator = $this->getMockBuilder('PSX\Validate\Validator')
            ->disableOriginalConstructor()
            ->setMethods(array('validateProperty'))
            ->getMock();

        $validator->expects($this->once())
            ->method('validateProperty')
            ->with($this->equalTo('/test'), $this->equalTo(1.2));

        $visitor = new ValidationVisitor();
        $visitor->setValidator($validator);

        $property = Property::getFloat('test');

        $visitor->visitFloat(1.2, $property, '/test');
    }

    public function testVisitInteger()
    {
        $validator = $this->getMockBuilder('PSX\Validate\Validator')
            ->disableOriginalConstructor()
            ->setMethods(array('validateProperty'))
            ->getMock();

        $validator->expects($this->once())
            ->method('validateProperty')
            ->with($this->equalTo('/test'), $this->equalTo(4));

        $visitor = new ValidationVisitor();
        $visitor->setValidator($validator);

        $property = Property::getInteger('test');

        $visitor->visitInteger(4, $property, '/test');
    }

    public function testVisitString()
    {
        $validator = $this->getMockBuilder('PSX\Validate\Validator')
            ->disableOriginalConstructor()
            ->setMethods(array('validateProperty'))
            ->getMock();

        $validator->expects($this->once())
            ->method('validateProperty')
            ->with($this->equalTo('/test'), $this->equalTo('foo'));

        $visitor = new ValidationVisitor();
        $visitor->setValidator($validator);

        $property = Property::getString('test');

        $visitor->visitString('foo', $property, '/test');
    }

    public function testVisitTime()
    {
        $validator = $this->getMockBuilder('PSX\Validate\Validator')
            ->disableOriginalConstructor()
            ->setMethods(array('validateProperty'))
            ->getMock();

        $validator->expects($this->once())
            ->method('validateProperty')
            ->with($this->equalTo('/test'), $this->equalTo(new Time('13:37:00')));

        $visitor = new ValidationVisitor();
        $visitor->setValidator($validator);

        $property = Property::getTime('test');

        $visitor->visitTime('13:37:00', $property, '/test');
    }
}
