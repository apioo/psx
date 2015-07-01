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
use PSX\Filter;
use PSX\Validate;

/**
 * ValidateAbstractTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ValidateAbstractTest extends \PHPUnit_Framework_TestCase
{
    public function testGetRecord()
    {
        $validator = new ArrayValidator(new Validate(), array(
            new Property('id', Validate::TYPE_INTEGER),
            new Property('title', Validate::TYPE_STRING, array(new Filter\Length(1, 2))),
        ));

        $this->assertInstanceOf('PSX\Data\RecordInterface', $validator->getRecord());
        $this->assertEquals(array('id' => null, 'title' => null), $validator->getRecord()->getRecordInfo()->getFields());
    }

    public function testThrowErrors()
    {
        try {
            $this->getValidator(ValidatorInterface::THROW_ERRORS)->validate(array(
                'id' => 5,
                'title' => 'foobar',
            ));
        } catch (ValidationException $e) {
            $this->assertEquals('id', $e->getTitle());
            $this->assertEquals('id has an invalid length min 1 and max 2 signs', $e->getResult()->getFirstError());
            $this->assertEquals(['id has an invalid length min 1 and max 2 signs'], $e->getResult()->getErrors());
        }
    }

    public function testCollectErrors()
    {
        $validator = $this->getValidator(ValidatorInterface::COLLECT_ERRORS);
        $result    = $validator->validate(array(
            'id' => 5,
            'title' => 'foobar',
        ));

        $errors = $validator->getErrors();

        $this->assertEquals(['id' => null, 'title' => null], $result);
        $this->assertArrayHasKey('id', $errors);
        $this->assertEquals('id has an invalid length min 1 and max 2 signs', $errors['id']->getFirstError());
        $this->assertArrayHasKey('title', $errors);
        $this->assertEquals('title has an invalid length min 1 and max 2 signs', $errors['title']->getFirstError());
        $this->assertEquals(['title has an invalid length min 1 and max 2 signs'], $errors['title']->getErrors());
        $this->assertEquals(false, $validator->isSuccessful());
    }

    /**
     * @expectedException \PSX\Validate\ValidationException
     */
    public function testSetFieldsAndFlag()
    {
        $validator = $this->getValidator(ValidatorInterface::COLLECT_ERRORS);
        $validator->setFields([new Property('id', Validate::TYPE_INTEGER, array(new Filter\Length(1, 2)))]);
        $validator->setFlag(ValidatorInterface::THROW_ERRORS);
        $validator->validate(array(
            'id' => 1,
            'title' => 'foobar',
        ));
    }

    protected function getValidator($flag)
    {
        $properties = [
            new Property('id', Validate::TYPE_INTEGER, array(new Filter\Length(1, 2))),
            new Property('title', Validate::TYPE_STRING, array(new Filter\Length(1, 2))),
        ];

        return new ArrayValidator(new Validate(), $properties, $flag);
    }
}
