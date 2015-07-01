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

namespace PSX\Data\Schema;

/**
 * BuilderTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class BuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testMeta()
    {
        $builder = new Builder('foo');

        $builder
            ->setDescription('bar')
            ->setRequired(true)
            ->setReference('stdClass');

        $property = $builder->getProperty();

        $this->assertEquals('foo', $property->getName());
        $this->assertEquals('bar', $property->getDescription());
        $this->assertEquals(true, $property->isRequired());
        $this->assertEquals('stdClass', $property->getReference());
    }

    public function testArrayType()
    {
        $builder = new Builder('foo');

        $builder->arrayType('foo');
        $builder->arrayType(Property::getArray('bar'));

        $property = $builder->getProperty();

        $this->assertInstanceOf('PSX\Data\Schema\Property\ArrayType', $property->get('foo'));
        $this->assertInstanceOf('PSX\Data\Schema\Property\ArrayType', $property->get('bar'));
    }

    public function testBoolean()
    {
        $builder = new Builder('foo');

        $builder->boolean('foo');

        $property = $builder->getProperty();

        $this->assertInstanceOf('PSX\Data\Schema\Property\BooleanType', $property->get('foo'));
    }

    public function testComplexType()
    {
        $builder = new Builder('foo');

        $builder->complexType('foo');
        $builder->complexType(Property::getComplex('bar'));
        $builder->complexType('baz', Property::getComplex('foo'));

        $property = $builder->getProperty();

        $this->assertInstanceOf('PSX\Data\Schema\Property\ComplexType', $property->get('foo'));
        $this->assertInstanceOf('PSX\Data\Schema\Property\ComplexType', $property->get('bar'));
        $this->assertInstanceOf('PSX\Data\Schema\Property\ComplexType', $property->get('baz'));
    }

    public function testDate()
    {
        $builder = new Builder('foo');

        $builder->date('foo');

        $property = $builder->getProperty();

        $this->assertInstanceOf('PSX\Data\Schema\Property\DateType', $property->get('foo'));
    }

    public function testDateTime()
    {
        $builder = new Builder('foo');

        $builder->dateTime('foo');

        $property = $builder->getProperty();

        $this->assertInstanceOf('PSX\Data\Schema\Property\DateTimeType', $property->get('foo'));
    }

    public function testDuration()
    {
        $builder = new Builder('foo');

        $builder->duration('foo');

        $property = $builder->getProperty();

        $this->assertInstanceOf('PSX\Data\Schema\Property\DurationType', $property->get('foo'));
    }

    public function testFloat()
    {
        $builder = new Builder('foo');

        $builder->float('foo');

        $property = $builder->getProperty();

        $this->assertInstanceOf('PSX\Data\Schema\Property\FloatType', $property->get('foo'));
    }

    public function testInteger()
    {
        $builder = new Builder('foo');

        $builder->integer('foo');

        $property = $builder->getProperty();

        $this->assertInstanceOf('PSX\Data\Schema\Property\IntegerType', $property->get('foo'));
    }

    public function testString()
    {
        $builder = new Builder('foo');

        $builder->string('foo');

        $property = $builder->getProperty();

        $this->assertInstanceOf('PSX\Data\Schema\Property\StringType', $property->get('foo'));
    }

    public function testTime()
    {
        $builder = new Builder('foo');

        $builder->time('foo');

        $property = $builder->getProperty();

        $this->assertInstanceOf('PSX\Data\Schema\Property\TimeType', $property->get('foo'));
    }
}
