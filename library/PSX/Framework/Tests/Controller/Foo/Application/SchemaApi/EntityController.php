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

namespace PSX\Framework\Tests\Controller\Foo\Application\SchemaApi;

use PSX\Framework\Api\Documentation;
use PSX\Framework\Api\Resource;
use PSX\Framework\Api\Version;
use PSX\Framework\Controller\SchemaApiAbstract;
use PSX\Data\RecordInterface;
use PSX\Schema\Property;
use PSX\Framework\Loader\Context;
use PSX\Framework\Test\Environment;

/**
 * EntityController
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class EntityController extends SchemaApiAbstract
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

    public function getDocumentation()
    {
        $resource = new Resource(Resource::STATUS_ACTIVE, $this->context->get(Context::KEY_PATH));
        $resource->addPathParameter(Property::getInteger('fooId'));

        $resource->addMethod(Resource\Factory::getMethod('GET')
            ->addQueryParameter(Property::getInteger('startIndex'))
            ->addQueryParameter(Property::getInteger('count'))
            ->addResponse(200, $this->schemaManager->getSchema('PSX\Framework\Tests\Controller\Foo\Schema\Collection')));

        $resource->addMethod(Resource\Factory::getMethod('PUT')
            ->setRequest($this->schemaManager->getSchema('PSX\Framework\Tests\Controller\Foo\Schema\Update'))
            ->addResponse(200, $this->schemaManager->getSchema('PSX\Framework\Tests\Controller\Foo\Schema\SuccessMessage')));

        $resource->addMethod(Resource\Factory::getMethod('DELETE')
            ->setRequest($this->schemaManager->getSchema('PSX\Framework\Tests\Controller\Foo\Schema\Delete'))
            ->addResponse(200, $this->schemaManager->getSchema('PSX\Framework\Tests\Controller\Foo\Schema\SuccessMessage')));

        return new Documentation\Simple($resource);
    }

    protected function doGet(Version $version)
    {
        $this->testCase->assertEquals(12, $this->queryParameters->getProperty('startIndex'));
        $this->testCase->assertEmpty($this->queryParameters->getProperty('bar'));
        $this->testCase->assertEquals(8, $this->pathParameters->getProperty('fooId'));
        $this->testCase->assertEmpty($this->pathParameters->getProperty('bar'));

        return array(
            'entry' => Environment::getService('table_manager')->getTable('PSX\Sql\TestTable')->getAll()
        );
    }

    protected function doPost(RecordInterface $record, Version $version)
    {
    }

    protected function doPut(RecordInterface $record, Version $version)
    {
        $this->testCase->assertEquals(8, $this->pathParameters->getProperty('fooId'));
        $this->testCase->assertEmpty($this->pathParameters->getProperty('bar'));

        $this->testCase->assertEquals(1, $record->getId());
        $this->testCase->assertEquals(3, $record->getUserId());
        $this->testCase->assertEquals('foobar', $record->getTitle());

        return array(
            'success' => true,
            'message' => 'You have successful update a record'
        );
    }

    protected function doDelete(RecordInterface $record, Version $version)
    {
        $this->testCase->assertEquals(8, $this->pathParameters->getProperty('fooId'));
        $this->testCase->assertEmpty($this->pathParameters->getProperty('bar'));

        $this->testCase->assertEquals(1, $record->getId());

        return array(
            'success' => true,
            'message' => 'You have successful delete a record'
        );
    }
}
