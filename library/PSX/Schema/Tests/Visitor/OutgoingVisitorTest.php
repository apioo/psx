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

namespace PSX\Schema\Tests\Visitor;

use PSX\Schema\Property;
use PSX\Schema\Visitor\AssimilationVisitor;
use PSX\Schema\Visitor\IncomingVisitor;
use PSX\Schema\Visitor\OutgoingVisitor;

/**
 * OutgoingVisitorTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class OutgoingVisitorTest extends \PHPUnit_Framework_TestCase
{
    public function testVisitArray()
    {
        $visitor  = new OutgoingVisitor();
        $property = Property::getArray('test')->setPrototype(Property::getString('foo'));

        $this->assertEquals(['10'], $visitor->visitArray([10], $property, ''));
    }

    public function testVisitArrayNoPrototype()
    {
        $visitor  = new OutgoingVisitor();
        $property = Property::getArray('test');

        $this->assertEquals(array(), $visitor->visitArray([], $property, ''));
    }

    public function testVisitBoolean()
    {
        $visitor  = new OutgoingVisitor();
        $property = Property::getBoolean('test');

        $this->assertInternalType('boolean', $visitor->visitBoolean(1, $property, ''));
        $this->assertEquals(true, $visitor->visitBoolean(true, $property, ''));
        $this->assertEquals(false, $visitor->visitBoolean(false, $property, ''));
        $this->assertEquals(true, $visitor->visitBoolean(1, $property, ''));
        $this->assertEquals(false, $visitor->visitBoolean(0, $property, ''));
        $this->assertEquals(true, $visitor->visitBoolean('1', $property, ''));
        $this->assertEquals(false, $visitor->visitBoolean('0', $property, ''));
        $this->assertEquals(true, $visitor->visitBoolean('true', $property, ''));
        $this->assertEquals(false, $visitor->visitBoolean('false', $property, ''));
    }

    public function testVisitBooleanInvalidFormat()
    {
        $visitor  = new OutgoingVisitor();
        $property = Property::getBoolean('test');

        $this->assertEquals(true, $visitor->visitBoolean(4, $property, ''));
    }

    public function testVisitComplex()
    {
        $visitor  = new OutgoingVisitor();
        $property = Property::getComplex('test')
            ->add(Property::getString('foo'))
            ->add(Property::getString('bar'));

        $record = $visitor->visitComplex((object) ['foo' => 'bar'], $property, '');

        $this->assertInstanceOf('PSX\Data\RecordInterface', $record);
        $this->assertEquals(['foo' => 'bar'], $record->getProperties());
    }

    public function testVisitComplexNoProperties()
    {
        $visitor  = new OutgoingVisitor();
        $property = Property::getComplex('test');

        $record = $visitor->visitComplex(new \stdClass(), $property, '');

        $this->assertInstanceOf('PSX\Data\RecordInterface', $record);
        $this->assertEquals([], $record->getProperties());
    }

    public function testVisitDateTime()
    {
        $visitor  = new OutgoingVisitor();
        $property = Property::getDateTime('test');

        $this->assertEquals('2002-10-10T17:00:00Z', $visitor->visitDateTime('2002-10-10 17:00:00Z', $property, '')); // MySQL format
        $this->assertEquals('2002-10-10T17:00:00Z', $visitor->visitDateTime('2002-10-10T17:00:00Z', $property, ''));
        $this->assertEquals('2002-10-10T17:00:00+01:00', $visitor->visitDateTime('2002-10-10T17:00:00+01:00', $property, ''));
        $this->assertEquals('2002-10-10T17:00:00Z', $visitor->visitDateTime(new \DateTime('2002-10-10T17:00:00Z'), $property, ''));
    }

    /**
     * @expectedException \PSX\Schema\ValidationException
     */
    public function testVisitDateTimeInvalidFormat()
    {
        $visitor  = new OutgoingVisitor();
        $property = Property::getDateTime('test');

        $visitor->visitDateTime('foo', $property, '');
    }

    public function testVisitDate()
    {
        $visitor  = new OutgoingVisitor();
        $property = Property::getDate('test');

        $this->assertEquals('2000-01-01', $visitor->visitDate('2000-01-01', $property, ''));
        $this->assertEquals('2000-01-01+13:00', $visitor->visitDate('2000-01-01+13:00', $property, ''));
        $this->assertEquals('2000-01-01', $visitor->visitDate(new \DateTime('2000-01-01'), $property, ''));
    }

    /**
     * @expectedException \PSX\Schema\ValidationException
     */
    public function testVisitDateInvalidFormat()
    {
        $visitor  = new OutgoingVisitor();
        $property = Property::getDate('test');

        $visitor->visitDate('foo', $property, '');
    }

    public function testVisitDuration()
    {
        $visitor  = new OutgoingVisitor();
        $property = Property::getDuration('test');

        $this->assertEquals('P1D', $visitor->visitDuration('P1D', $property, ''));
        $this->assertEquals('P1DT12H', $visitor->visitDuration('P1DT12H', $property, ''));
        $this->assertEquals('P1D', $visitor->visitDuration(new \DateInterval('P1D'), $property, ''));
    }

    /**
     * @expectedException \PSX\Schema\ValidationException
     */
    public function testVisitDurationInvalidFormat()
    {
        $visitor  = new OutgoingVisitor();
        $property = Property::getDuration('test');

        $visitor->visitDuration('foo', $property, '');
    }

    public function testVisitFloat()
    {
        $visitor  = new OutgoingVisitor();
        $property = Property::getFloat('test');

        $this->assertInternalType('float', $visitor->visitFloat('1', $property, ''));
        $this->assertEquals(1, $visitor->visitFloat(1, $property, ''));
        $this->assertEquals(1.2, $visitor->visitFloat(1.2, $property, ''));
        $this->assertEquals(-1.2, $visitor->visitFloat(-1.2, $property, ''));
        $this->assertEquals(1, $visitor->visitFloat('1', $property, ''));
        $this->assertEquals(1.2, $visitor->visitFloat('1.2', $property, ''));
        $this->assertEquals(-1.2, $visitor->visitFloat('-1.2', $property, ''));
        $this->assertEquals(12000.0, $visitor->visitFloat('1.2E4', $property, ''));
        $this->assertEquals(12000.0, $visitor->visitFloat('1.2e4', $property, ''));
        $this->assertEquals(12000.0, $visitor->visitFloat('1.2e+4', $property, ''));
        $this->assertEquals(0.00012, $visitor->visitFloat('1.2e-4', $property, ''));
    }

    public function testVisitFloatInvalidFormat()
    {
        $visitor  = new OutgoingVisitor();
        $property = Property::getFloat('test');

        $this->assertEquals(0, $visitor->visitFloat('foo', $property, ''));
    }

    public function testVisitInteger()
    {
        $visitor  = new OutgoingVisitor();
        $property = Property::getInteger('test');

        $this->assertInternalType('integer', $visitor->visitInteger('4', $property, ''));
        $this->assertEquals(4, $visitor->visitInteger(4, $property, ''));
        $this->assertEquals(4, $visitor->visitInteger('4', $property, ''));
        $this->assertEquals(4, $visitor->visitInteger('+4', $property, ''));
        $this->assertEquals(-4, $visitor->visitInteger('-4', $property, ''));
    }

    public function testVisitIntegerInvalidFormat()
    {
        $visitor  = new OutgoingVisitor();
        $property = Property::getInteger('test');

        $this->assertEquals(0, $visitor->visitInteger('foo', $property, ''));
    }

    public function testVisitString()
    {
        $visitor  = new OutgoingVisitor();
        $property = Property::getString('test');

        $this->assertInternalType('string', $visitor->visitString(4, $property, ''));
        $this->assertEquals('4', $visitor->visitString(4, $property, ''));
    }

    /**
     * @expectedException \ErrorException
     */
    public function testVisitStringInvalidFormat()
    {
        $visitor  = new OutgoingVisitor();
        $property = Property::getString('test');

        $visitor->visitString(array(), $property, '');
    }

    public function testVisitTime()
    {
        $visitor  = new OutgoingVisitor();
        $property = Property::getTime('test');

        $this->assertEquals('10:00:00', $visitor->visitTime('10:00:00', $property, ''));
        $this->assertEquals('10:00:00+02:00', $visitor->visitTime('10:00:00+02:00', $property, ''));
        $this->assertEquals('10:00:00', $visitor->visitTime(new \DateTime('10:00:00'), $property, ''));
    }

    /**
     * @expectedException \PSX\Schema\ValidationException
     */
    public function testVisitTimeInvalidFormat()
    {
        $visitor  = new OutgoingVisitor();
        $property = Property::getTime('test');

        $visitor->visitTime('foo', $property, '');
    }
}
