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
use PSX\Validate\Property;
use PSX\Validate\Validate;
use PSX\Validate\ValidationException;
use PSX\Validate\Validator;
use PSX\Validate\ValidatorInterface;

/**
 * ValidateAbstractTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ValidateAbstractTest extends \PHPUnit_Framework_TestCase
{
    public function testValidate()
    {
        $validator = $this->getValidator(ValidatorInterface::THROW_ERRORS);
        $validator->validate([
            'id' => 1,
            'title' => 'fo',
            'foo' => 'bar',
        ]);

        $this->assertTrue($validator->isSuccessful());
    }

    /**
     * @expectedException \PSX\Validate\ValidationException
     */
    public function testValidateInvalid()
    {
        $validator = $this->getValidator(ValidatorInterface::THROW_ERRORS);
        $validator->validate([
            'id' => 1,
            'title' => 'foobar',
            'foo' => 'bar',
        ]);

        $this->assertTrue($validator->isSuccessful());
    }

    public function testValidateProperty()
    {
        $validator = $this->getValidator(ValidatorInterface::THROW_ERRORS);
        $validator->validateProperty('/id', 2);

        $this->assertTrue($validator->isSuccessful());
    }

    /**
     * @expectedException \PSX\Validate\ValidationException
     */
    public function testValidatePropertyInvalid()
    {
        $validator = $this->getValidator(ValidatorInterface::THROW_ERRORS);
        $validator->validateProperty('/id', 4);

        $this->assertTrue($validator->isSuccessful());
    }

    public function testValidatePropertyUnknown()
    {
        $validator = $this->getValidator(ValidatorInterface::THROW_ERRORS);
        $validator->validateProperty('/foo', 4);

        $this->assertTrue($validator->isSuccessful());
    }

    public function testValidateFlagThrowErrors()
    {
        try {
            $this->getValidator(ValidatorInterface::THROW_ERRORS)->validate(array(
                'id' => 5,
                'title' => 'foobar',
                'author' => [
                    'name' => 'foobar',
                ],
                'foo' => 'bar',
            ));
        } catch (ValidationException $e) {
            $this->assertEquals('/id', $e->getTitle());
            $this->assertEquals('/id has an invalid length min 1 and max 2 signs', $e->getResult()->getFirstError());
            $this->assertEquals(['/id has an invalid length min 1 and max 2 signs'], $e->getResult()->getErrors());
        }
    }

    public function testValidateFlagCollectErrors()
    {
        $validator = $this->getValidator(ValidatorInterface::COLLECT_ERRORS);

        $validator->validate(array(
            'id' => 5,
            'title' => 'foobar',
            'author' => [
                'name' => 'foobar',
            ],
            'foo' => 'bar',
        ));

        $errors = $validator->getErrors();

        $this->assertArrayHasKey('id', $errors);
        $this->assertEquals('/id has an invalid length min 1 and max 2 signs', $errors['id']->getFirstError());
        $this->assertArrayHasKey('title', $errors);
        $this->assertEquals('/title has an invalid length min 1 and max 2 signs', $errors['title']->getFirstError());
        $this->assertEquals(['/title has an invalid length min 1 and max 2 signs'], $errors['title']->getErrors());
        $this->assertEquals('/author/name has an invalid length min 1 and max 2 signs', $errors['author/name']->getFirstError());
        $this->assertEquals(['/author/name has an invalid length min 1 and max 2 signs'], $errors['author/name']->getErrors());
        $this->assertEquals(false, $validator->isSuccessful());
    }

    public function testGetFields()
    {
        $fields    = [new Property('id', Validate::TYPE_INTEGER, array(new Filter\Length(1, 2)))];
        $validator = new Validator($fields);

        $this->assertEquals($fields, $validator->getFields());
    }

    /**
     * @expectedException \PSX\Validate\ValidationException
     */
    public function testSetFieldsAndFlag()
    {
        $fields = [new Property('id', Validate::TYPE_INTEGER, array(new Filter\Length(3, 6)))];

        $validator = $this->getValidator(ValidatorInterface::COLLECT_ERRORS);
        $validator->setFields($fields);
        $validator->setFlag(ValidatorInterface::THROW_ERRORS);

        $validator->validate(array(
            'id' => 1,
            'title' => 'foobar',
        ));
    }

    public function testGetRequiredNames()
    {
        $validator = $this->getValidator(ValidatorInterface::THROW_ERRORS);
        $validator->validate(array(
            'title' => 'fo',
        ));

        $this->assertEquals(['id', 'author/name'], $validator->getRequiredNames());
    }

    protected function getValidator($flag)
    {
        $properties = [
            new Property('id', Validate::TYPE_INTEGER, array(new Filter\Length(1, 2))),
            new Property('title', Validate::TYPE_STRING, array(new Filter\Length(1, 2))),
            new Property('author/name', Validate::TYPE_STRING, array(new Filter\Length(1, 2))),
        ];

        return new Validator($properties, $flag);
    }
}
