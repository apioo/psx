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

namespace PSX\Validate\Tests;

use PSX\Validate\Filter;
use PSX\Validate\Validate;

/**
 * ValidateTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ValidateTest extends \PHPUnit_Framework_TestCase
{
    protected $successFilter;
    protected $failureFilter;
    protected $responseFilter;
    protected $validate;

    protected function setUp()
    {
        $this->successFilter = $this->getMockBuilder('PSX\Validate\FilterAbstract')
            ->getMock();

        $this->failureFilter = $this->getMockBuilder('PSX\Validate\FilterAbstract')
            ->getMock();

        $this->responseFilter = $this->getMockBuilder('PSX\Validate\FilterAbstract')
            ->getMock();

        $this->validate = new Validate();
    }

    protected function tearDown()
    {
    }

    public function testApply()
    {
        $this->successFilter->expects($this->once())
            ->method('apply')
            ->will($this->returnValue(true));

        $this->responseFilter->expects($this->once())
            ->method('apply')
            ->will($this->returnValue('bar'));

        $this->assertEquals('foo', $this->validate->apply('foo', Validate::TYPE_STRING));
        $this->assertEquals('foo', $this->validate->apply('foo', Validate::TYPE_STRING, array()));
        $this->assertEquals('foo', $this->validate->apply('foo', Validate::TYPE_STRING, array($this->successFilter)));
        $this->assertEquals('bar', $this->validate->apply('foo', Validate::TYPE_STRING, array($this->responseFilter)));
    }

    /**
     * @expectedException \PSX\Validate\ValidationException
     */
    public function testApplyFailure()
    {
        $this->failureFilter->expects($this->once())
            ->method('apply')
            ->will($this->returnValue(false));

        $this->assertEquals(false, $this->validate->apply('foo', Validate::TYPE_STRING, array($this->failureFilter)));
    }

    public function testApplyType()
    {
        $this->assertEquals('foo', $this->validate->apply('foo', Validate::TYPE_STRING));
        $this->assertInternalType(Validate::TYPE_STRING, $this->validate->apply('foo', Validate::TYPE_STRING));

        $this->assertEquals(0, $this->validate->apply('foo', Validate::TYPE_INTEGER));
        $this->assertInternalType(Validate::TYPE_INTEGER, $this->validate->apply('foo', Validate::TYPE_INTEGER));

        $this->assertEquals(0.0, $this->validate->apply('foo', Validate::TYPE_FLOAT));
        $this->assertInternalType(Validate::TYPE_FLOAT, $this->validate->apply('foo', Validate::TYPE_FLOAT));

        $this->assertEquals(true, $this->validate->apply('foo', Validate::TYPE_BOOLEAN));
        $this->assertInternalType(Validate::TYPE_BOOLEAN, $this->validate->apply('foo', Validate::TYPE_BOOLEAN));

        $this->assertEquals(array('foo'), $this->validate->apply('foo', Validate::TYPE_ARRAY));
        $this->assertInternalType(Validate::TYPE_ARRAY, $this->validate->apply('foo', Validate::TYPE_ARRAY));

        $expect = new \stdClass();
        $expect->scalar = 'foo';

        $this->assertEquals($expect, $this->validate->apply('foo', Validate::TYPE_OBJECT));
        $this->assertInternalType(Validate::TYPE_OBJECT, $this->validate->apply('foo', Validate::TYPE_OBJECT));
    }

    /**
     * @expectedException \PSX\Validate\ValidationException
     */
    public function testApplyRequiredNull()
    {
        $this->validate->apply(null, Validate::TYPE_STRING, [], 'bar', true);
    }

    public function testApplyOptionalNull()
    {
        $this->assertEquals(null, $this->validate->apply(null, Validate::TYPE_STRING, [], 'bar', false));
    }

    public function testApplyRequiredValue()
    {
        $this->assertEquals('foo', $this->validate->apply('foo', Validate::TYPE_STRING, [], 'bar', true));
    }

    public function testApplyOptionalValue()
    {
        $this->assertEquals('foo', $this->validate->apply('foo', Validate::TYPE_STRING, [], 'bar', false));
    }

    /**
     * @expectedException \PSX\Validate\ValidationException
     */
    public function testApplyRequiredValueFilterInvalid()
    {
        $this->validate->apply('foo-', Validate::TYPE_STRING, [new Filter\Alnum()], 'bar', true);
    }

    /**
     * @expectedException \PSX\Validate\ValidationException
     */
    public function testApplyOptionalValueFilterInvalid()
    {
        $this->validate->apply('foo-', Validate::TYPE_STRING, [new Filter\Alnum()], 'bar', false);
    }

    public function testApplyFilter()
    {
        $this->assertEquals('foo', $this->validate->apply('foo', Validate::TYPE_STRING, array(new Filter\Alnum())));
    }

    /**
     * @expectedException \PSX\Validate\ValidationException
     */
    public function testApplyFilterInvalid()
    {
        $this->assertEquals('foo', $this->validate->apply('foo-', Validate::TYPE_STRING, array(new Filter\Alnum())));
    }

    public function testApplyFilterCallable()
    {
        $this->assertEquals('foo', $this->validate->apply('foo', Validate::TYPE_STRING, array(function ($value) {
            return ctype_alnum($value);
        })));
    }

    /**
     * @expectedException \PSX\Validate\ValidationException
     */
    public function testApplyFilterCallableInvalid()
    {
        $this->assertEquals('foo', $this->validate->apply('foo', Validate::TYPE_STRING, array(function ($value) {
            return false;
        })));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testApplyInvalidFilterType()
    {
        $this->assertEquals('foo', $this->validate->apply('foo', Validate::TYPE_STRING, array('foo')));
    }

    public function testValidateTitleNotSet()
    {
        $result = $this->validate->validate(null);

        $this->assertEquals(null, $result->getValue());
        $this->assertEquals('Unknown is not set', $result->getFirstError());

        $result = $this->validate->validate(null, Validate::TYPE_STRING, array(), 'foo');

        $this->assertEquals(null, $result->getValue());
        $this->assertEquals('foo is not set', $result->getFirstError());
    }
}
