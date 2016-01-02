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

/**
 * AssimilationVisitorTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class AssimilationVisitorTest extends \PHPUnit_Framework_TestCase
{
    public function testVisitArray()
    {
        $visitor  = new AssimilationVisitor();
        $property = Property::getArray('test')->setPrototype(Property::getString('foo'));

        $this->assertEquals(['10'], $visitor->visitArray([10], $property, ''));
    }

    public function testVisitArrayNoPrototype()
    {
        $visitor  = new AssimilationVisitor();
        $property = Property::getArray('test');

        $this->assertEquals(array(), $visitor->visitArray([], $property, ''));
    }

    public function testVisitBoolean()
    {
        $visitor  = new AssimilationVisitor();
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
        $visitor  = new AssimilationVisitor();
        $property = Property::getBoolean('test');

        $this->assertEquals(true, $visitor->visitBoolean(4, $property, ''));
    }

    public function testVisitComplex()
    {
        $visitor  = new AssimilationVisitor();
        $property = Property::getComplex('test')
            ->add(Property::getString('foo'))
            ->add(Property::getString('bar'));

        $record = $visitor->visitComplex((object) ['foo' => 'bar'], $property, '');

        $this->assertInstanceOf('PSX\Data\RecordInterface', $record);
        $this->assertEquals(array('foo' => 'bar'), $record->getRecordInfo()->getData());
    }

    public function testVisitComplexNoProperties()
    {
        $visitor  = new AssimilationVisitor();
        $property = Property::getComplex('test');

        $this->assertInstanceOf('PSX\Data\RecordInterface', $visitor->visitComplex(new \stdClass(), $property, ''));
    }

    public function testVisitDateTime()
    {
        $visitor  = new AssimilationVisitor();
        $property = Property::getDateTime('test');

        $this->assertInstanceOf('PSX\DateTime', $visitor->visitDateTime('2002-10-10 17:00:00', $property, '')); // MySQL format
        $this->assertInstanceOf('PSX\DateTime', $visitor->visitDateTime('2002-10-10T17:00:00', $property, ''));
        $this->assertInstanceOf('PSX\DateTime', $visitor->visitDateTime('2002-10-10T17:00:00+01:00', $property, ''));
        $this->assertInstanceOf('DateTime', $visitor->visitDateTime(new \DateTime(), $property, ''));
    }

    /**
     * @expectedException \PSX\Data\Schema\ValidationException
     */
    public function testVisitDateTimeInvalidFormat()
    {
        $visitor  = new AssimilationVisitor();
        $property = Property::getDateTime('test');

        $visitor->visitDateTime('foo', $property, '');
    }

    public function testVisitDate()
    {
        $visitor  = new AssimilationVisitor();
        $property = Property::getDate('test');

        $this->assertInstanceOf('PSX\DateTime\Date', $visitor->visitDate('2000-01-01', $property, ''));
        $this->assertInstanceOf('PSX\DateTime\Date', $visitor->visitDate('2000-01-01+13:00', $property, ''));
        $this->assertInstanceOf('DateTime', $visitor->visitDate(new \DateTime(), $property, ''));
    }

    /**
     * @expectedException \PSX\Data\Schema\ValidationException
     */
    public function testVisitDateInvalidFormat()
    {
        $visitor  = new AssimilationVisitor();
        $property = Property::getDate('test');

        $visitor->visitDate('foo', $property, '');
    }

    public function testVisitDuration()
    {
        $visitor  = new AssimilationVisitor();
        $property = Property::getDuration('test');

        $this->assertInstanceOf('PSX\DateTime\Duration', $visitor->visitDuration('P1D', $property, ''));
        $this->assertInstanceOf('PSX\DateTime\Duration', $visitor->visitDuration('P1DT12H', $property, ''));
        $this->assertInstanceOf('DateInterval', $visitor->visitDuration(new \DateInterval('P1D'), $property, ''));
    }

    /**
     * @expectedException \PSX\Data\Schema\ValidationException
     */
    public function testVisitDurationInvalidFormat()
    {
        $visitor  = new AssimilationVisitor();
        $property = Property::getDuration('test');

        $visitor->visitDuration('foo', $property, '');
    }

    public function testVisitFloat()
    {
        $visitor  = new AssimilationVisitor();
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
        $visitor  = new AssimilationVisitor();
        $property = Property::getFloat('test');

        $this->assertEquals(0, $visitor->visitFloat('foo', $property, ''));
    }

    public function testVisitInteger()
    {
        $visitor  = new AssimilationVisitor();
        $property = Property::getInteger('test');

        $this->assertInternalType('integer', $visitor->visitInteger('4', $property, ''));
        $this->assertEquals(4, $visitor->visitInteger(4, $property, ''));
        $this->assertEquals(4, $visitor->visitInteger('4', $property, ''));
        $this->assertEquals(4, $visitor->visitInteger('+4', $property, ''));
        $this->assertEquals(-4, $visitor->visitInteger('-4', $property, ''));
    }

    public function testVisitIntegerInvalidFormat()
    {
        $visitor  = new AssimilationVisitor();
        $property = Property::getInteger('test');

        $this->assertEquals(0, $visitor->visitInteger('foo', $property, ''));
    }

    public function testVisitString()
    {
        $visitor  = new AssimilationVisitor();
        $property = Property::getString('test');

        $this->assertInternalType('string', $visitor->visitString(4, $property, ''));
        $this->assertEquals('4', $visitor->visitString(4, $property, ''));
    }

    /**
     * @expectedException \ErrorException
     */
    public function testVisitStringInvalidFormat()
    {
        $visitor  = new AssimilationVisitor();
        $property = Property::getString('test');

        $visitor->visitString(array(), $property, '');
    }

    public function testVisitTime()
    {
        $visitor  = new AssimilationVisitor();
        $property = Property::getTime('test');

        $this->assertInstanceOf('PSX\DateTime\Time', $visitor->visitTime('10:00:00', $property, ''));
        $this->assertInstanceOf('PSX\DateTime\Time', $visitor->visitTime('10:00:00+02:00', $property, ''));
        $this->assertInstanceOf('DateTime', $visitor->visitTime(new \DateTime(), $property, ''));
    }

    /**
     * @expectedException \PSX\Data\Schema\ValidationException
     */
    public function testVisitTimeInvalidFormat()
    {
        $visitor  = new AssimilationVisitor();
        $property = Property::getTime('test');

        $visitor->visitTime('foo', $property, '');
    }
}
