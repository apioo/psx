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

namespace PSX\Validate;

use PSX\Data\Record;
use PSX\Data\RecordAbstract;
use PSX\Filter;
use PSX\Validate;

/**
 * RecordValidatorTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class RecordValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function testValidate()
    {
        $record = new ValidateTestRecord();
        $record->setId(1);
        $record->setTitle('foo');
        $record->setDate('2013-12-10 00:00:00');

        $validator = new RecordValidator(new Validate(), array(
            new Property('id', Validate::TYPE_INTEGER),
            new Property('title', Validate::TYPE_STRING, array(new Filter\Length(2, 8))),
            new Property('date', Validate::TYPE_STRING, array(new Filter\DateTime())),
        ));

        $result = $validator->validate($record);

        $this->assertInstanceOf('PSX\Validate\ValidateTestRecord', $result);
        $this->assertEquals(1, $result->getId());
        $this->assertEquals('foo', $result->getTitle());
        $this->assertInstanceOf('DateTime', $result->getDate());
        $this->assertEquals('2013-12-10', $result->getDate()->format('Y-m-d'));
    }

    /**
     * @expectedException \PSX\Validate\ValidationException
     */
    public function testValidateFieldNotDefined()
    {
        $record = new ValidateTestRecord();
        $record->setId(1);
        $record->setTitle('foo');

        $validator = new RecordValidator(new Validate(), array(
            new Property('bar', Validate::TYPE_INTEGER),
        ));

        $validator->validate($record);
    }

    /**
     * @expectedException \PSX\Validate\ValidationException
     */
    public function testValidateValidationError()
    {
        $record = new ValidateTestRecord();
        $record->setId(1);
        $record->setTitle('foo');

        $validator = new RecordValidator(new Validate(), array(
            new Property('id', Validate::TYPE_INTEGER),
            new Property('title', Validate::TYPE_STRING, array(new Filter\Length(1, 2))),
        ));

        $validator->validate($record);
    }

    /**
     * If the filter returns an string the validate method calls the setter of
     * the record to change the value
     */
    public function testValidateCallSetMethod()
    {
        $record = $this->getMock('PSX\Validate\ValidateTestRecord', array('setTitle'));

        // we must use another method to set the title because setTitle is
        // already mocked
        $record->setTitleAlt('foo');

        $record->expects($this->once())
            ->method('setTitle')
            ->with($this->equalTo('acbd18db4cc2f85cedef654fccc4a4d8'));

        $validator = new RecordValidator(new Validate(), array(
            new Property('id', Validate::TYPE_INTEGER),
            new Property('title', Validate::TYPE_STRING, array(new Filter\Md5())),
            new Property('date', Validate::TYPE_STRING, array(new Filter\DateTime())),
        ));

        $validator->validate($record);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testValidateInvalidData()
    {
        $validator = new RecordValidator(new Validate(), array(
            new Property('id', Validate::TYPE_INTEGER),
            new Property('title', Validate::TYPE_STRING, array(new Filter\Length(2, 8))),
            new Property('date', Validate::TYPE_STRING, array(new Filter\DateTime())),
        ));

        $validator->validate('foo');
    }

    public function testValidateEmptyData()
    {
        $validator = new RecordValidator(new Validate(), array(
            new Property('id', Validate::TYPE_INTEGER),
            new Property('title', Validate::TYPE_STRING, array(new Filter\Length(2, 8))),
            new Property('date', Validate::TYPE_STRING, array(new Filter\DateTime())),
        ));

        $result = $validator->validate(new ValidateTestRecord());

        $this->assertInstanceOf('PSX\Validate\ValidateTestRecord', $result);
        $this->assertEquals(null, $result->getId());
        $this->assertEquals(null, $result->getTitle());
        $this->assertEquals(null, $result->getDate());
    }
}

class ValidateTestRecord extends RecordAbstract
{
    protected $id;
    protected $title;
    protected $date;

    public function setId($id)
    {
        $this->id = $id;
    }
    
    public function getId()
    {
        return $this->id;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }
    
    public function getTitle()
    {
        return $this->title;
    }

    public function setDate($date)
    {
        $this->date = $date;
    }
    
    public function getDate()
    {
        return $this->date;
    }

    public function setTitleAlt($title)
    {
        $this->title = $title;
    }
}
