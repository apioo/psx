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

namespace PSX\Controller\Foo\Application\SchemaApi;

use PSX\Api\Documentation;
use PSX\Api\Resource;
use PSX\Api\Version;
use PSX\Controller\SchemaApiAbstract;
use PSX\Data\RecordInterface;
use PSX\Data\Schema\Property;
use PSX\Loader\Context;
use PSX\Test\Environment;

/**
 * VersionViewController
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class VersionViewController extends SchemaApiAbstract
{
    /**
     * @Inject
     * @var \PSX\Data\Schema\SchemaManager
     */
    protected $schemaManager;

    /**
     * @Inject
     * @var \PHPUnit_Framework_TestCase
     */
    protected $testCase;

    public function getDocumentation()
    {
        $doc      = new Documentation\Version();
        $resource = new Resource(Resource::STATUS_CLOSED, $this->context->get(Context::KEY_PATH));

        $resource->addMethod(Resource\Factory::getMethod('GET')
            ->addResponse(200, $this->schemaManager->getSchema('PSX\Controller\Foo\Schema\Collection')));

        $resource->addMethod(Resource\Factory::getMethod('POST')
            ->setRequest($this->schemaManager->getSchema('PSX\Controller\Foo\Schema\Create'))
            ->addResponse(200, $this->schemaManager->getSchema('PSX\Controller\Foo\Schema\SuccessMessage')));

        $resource->addMethod(Resource\Factory::getMethod('PUT')
            ->setRequest($this->schemaManager->getSchema('PSX\Controller\Foo\Schema\Update'))
            ->addResponse(200, $this->schemaManager->getSchema('PSX\Controller\Foo\Schema\SuccessMessage')));

        $resource->addMethod(Resource\Factory::getMethod('DELETE')
            ->setRequest($this->schemaManager->getSchema('PSX\Controller\Foo\Schema\Delete'))
            ->addResponse(200, $this->schemaManager->getSchema('PSX\Controller\Foo\Schema\SuccessMessage')));

        $doc->addResource(1, $resource);

        $resource = new Resource(Resource::STATUS_DEPRECATED, $this->context->get(Context::KEY_PATH));

        $resource->addMethod(Resource\Factory::getMethod('GET')
            ->addResponse(200, $this->schemaManager->getSchema('PSX\Controller\Foo\Schema\Collection')));

        $resource->addMethod(Resource\Factory::getMethod('POST')
            ->setRequest($this->schemaManager->getSchema('PSX\Controller\Foo\Schema\Create'))
            ->addResponse(200, $this->schemaManager->getSchema('PSX\Controller\Foo\Schema\SuccessMessage')));

        $resource->addMethod(Resource\Factory::getMethod('PUT')
            ->setRequest($this->schemaManager->getSchema('PSX\Controller\Foo\Schema\Update'))
            ->addResponse(200, $this->schemaManager->getSchema('PSX\Controller\Foo\Schema\SuccessMessage')));

        $resource->addMethod(Resource\Factory::getMethod('DELETE')
            ->setRequest($this->schemaManager->getSchema('PSX\Controller\Foo\Schema\Delete'))
            ->addResponse(200, $this->schemaManager->getSchema('PSX\Controller\Foo\Schema\SuccessMessage')));

        $doc->addResource(2, $resource);

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
            ->addResponse(200, $this->schemaManager->getSchema('PSX\Controller\Foo\Schema\Collection')));

        $resource->addMethod(Resource\Factory::getMethod('POST')
            ->setRequest($this->schemaManager->getSchema('PSX\Controller\Foo\Schema\Create'))
            ->addResponse(200, $this->schemaManager->getSchema('PSX\Controller\Foo\Schema\SuccessMessage')));

        $resource->addMethod(Resource\Factory::getMethod('PUT')
            ->setRequest($this->schemaManager->getSchema('PSX\Controller\Foo\Schema\Update'))
            ->addResponse(200, $this->schemaManager->getSchema('PSX\Controller\Foo\Schema\SuccessMessage')));

        $resource->addMethod(Resource\Factory::getMethod('DELETE')
            ->setRequest($this->schemaManager->getSchema('PSX\Controller\Foo\Schema\Delete'))
            ->addResponse(200, $this->schemaManager->getSchema('PSX\Controller\Foo\Schema\SuccessMessage')));

        $doc->addResource(3, $resource);

        return $doc;
    }

    protected function doGet(Version $version)
    {
        return array(
            'entry' => Environment::getService('table_manager')->getTable('PSX\Sql\TestTable')->getAll()
        );
    }

    protected function doCreate(RecordInterface $record, Version $version)
    {
        $this->testCase->assertEquals(3, $record->getUserId());
        $this->testCase->assertEquals('test', $record->getTitle());
        $this->testCase->assertInstanceOf('DateTime', $record->getDate());

        return array(
            'success' => true,
            'message' => 'You have successful create a record'
        );
    }

    protected function doUpdate(RecordInterface $record, Version $version)
    {
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
        $this->testCase->assertEquals(1, $record->getId());

        return array(
            'success' => true,
            'message' => 'You have successful delete a record'
        );
    }
}
