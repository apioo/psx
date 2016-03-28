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

namespace PSX\Framework\Tests\Controller\Foo\Application;

use PSX\Api\Resource;
use PSX\Framework\Controller\SchemaApiAbstract;
use PSX\Data\RecordInterface;
use PSX\Schema\Property;
use PSX\Validate\Filter;
use PSX\Framework\Loader\Context;
use PSX\Framework\Test\Environment;
use PSX\Validate\Validate;

/**
 * TestSchemaApiController
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class TestSchemaApiController extends SchemaApiAbstract
{
    /**
     * @Inject
     * @var \PSX\Schema\SchemaManager
     */
    protected $schemaManager;

    /**
     * @Inject
     * @var \PHPUnit_Framework_TestCase
     */
    protected $testCase;

    public function getDocumentation($version = null)
    {
        $resource = new Resource(Resource::STATUS_ACTIVE, $this->context->get(Context::KEY_PATH));
        $resource->setTitle('foo');
        $resource->setDescription('lorem ipsum');

        $resource->addPathParameter(Property::getString('name')
            ->setDescription('Name parameter')
            ->setRequired(false)
            ->setMinLength(0)
            ->setMaxLength(16)
            ->setPattern('[A-z]+'));
        $resource->addPathParameter(Property::getString('type')
            ->setEnumeration(['foo', 'bar']));

        $resource->addMethod(Resource\Factory::getMethod('GET')
            ->setDescription('Returns a collection')
            ->addQueryParameter(Property::getInteger('startIndex')
                ->setDescription('startIndex parameter')
                ->setRequired(false)
                ->setMin(0)
                ->setMax(32))
            ->addQueryParameter(Property::getFloat('float'))
            ->addQueryParameter(Property::getBoolean('boolean'))
            ->addQueryParameter(Property::getDate('date'))
            ->addQueryParameter(Property::getDateTime('datetime'))
            ->addResponse(200, $this->schemaManager->getSchema('PSX\Framework\Tests\Controller\Foo\Schema\Collection')));

        $resource->addMethod(Resource\Factory::getMethod('POST')
            ->setRequest($this->schemaManager->getSchema('PSX\Framework\Tests\Controller\Foo\Schema\Create'))
            ->addResponse(201, $this->schemaManager->getSchema('PSX\Framework\Tests\Controller\Foo\Schema\SuccessMessage')));

        $resource->addMethod(Resource\Factory::getMethod('PUT')
            ->setRequest($this->schemaManager->getSchema('PSX\Framework\Tests\Controller\Foo\Schema\Update'))
            ->addResponse(200, $this->schemaManager->getSchema('PSX\Framework\Tests\Controller\Foo\Schema\SuccessMessage')));

        $resource->addMethod(Resource\Factory::getMethod('DELETE')
            ->setRequest($this->schemaManager->getSchema('PSX\Framework\Tests\Controller\Foo\Schema\Delete'))
            ->addResponse(200, $this->schemaManager->getSchema('PSX\Framework\Tests\Controller\Foo\Schema\SuccessMessage')));

        $resource->addMethod(Resource\Factory::getMethod('PATCH')
            ->setRequest($this->schemaManager->getSchema('PSX\Framework\Tests\Controller\Foo\Schema\Patch'))
            ->addResponse(200, $this->schemaManager->getSchema('PSX\Framework\Tests\Controller\Foo\Schema\SuccessMessage')));

        return $resource;
    }

    protected function doGet()
    {
        return array(
            'entry' => Environment::getService('table_manager')->getTable('PSX\Sql\Tests\TestTable')->getAll()
        );
    }

    protected function doPost(RecordInterface $record)
    {
        $this->testCase->assertEquals(3, $record->userId);
        $this->testCase->assertEquals('test', $record->title);
        $this->testCase->assertInstanceOf('DateTime', $record->date);

        return array(
            'success' => true,
            'message' => 'You have successful post a record'
        );
    }

    protected function doPut(RecordInterface $record)
    {
        $this->testCase->assertEquals(1, $record->id);
        $this->testCase->assertEquals(3, $record->userId);
        $this->testCase->assertEquals('foobar', $record->title);

        return array(
            'success' => true,
            'message' => 'You have successful put a record'
        );
    }

    protected function doDelete(RecordInterface $record)
    {
        $this->testCase->assertEquals(1, $record->id);

        return array(
            'success' => true,
            'message' => 'You have successful delete a record'
        );
    }

    protected function doPatch(RecordInterface $record)
    {
        $this->testCase->assertEquals(1, $record->id);
        $this->testCase->assertEquals(3, $record->userId);
        $this->testCase->assertEquals('foobar', $record->title);

        return array(
            'success' => true,
            'message' => 'You have successful patch a record'
        );
    }
}
