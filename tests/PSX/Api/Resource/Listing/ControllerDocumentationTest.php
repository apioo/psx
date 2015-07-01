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

namespace PSX\Api\Resource\Listing;

use PSX\Test\ControllerDbTestCase;
use PSX\Test\Environment;

/**
 * ControllerDocumentationTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ControllerDocumentationTest extends ControllerDbTestCase
{
    public function getDataSet()
    {
        return $this->createFlatXMLDataSet(__DIR__ . '/../../../Sql/table_fixture.xml');
    }

    public function testGetResourceIndex()
    {
        /** @var \PSX\Api\Resource[] $resources */
        $resources = Environment::getService('resource_listing')->getResourceIndex();

        $this->assertEquals(2, count($resources));

        $this->assertEquals(['GET', 'POST', 'PUT', 'DELETE'], $resources[0]->getAllowedMethods());
        $this->assertEquals('/bar', $resources[0]->getPath());

        $this->assertEquals(['GET', 'POST', 'PUT', 'DELETE'], $resources[1]->getAllowedMethods());
        $this->assertEquals('/foo', $resources[1]->getPath());
    }

    public function testGetDocumentation()
    {
        /** @var \PSX\Api\DocumentationInterface $documentation */
        $documentation = Environment::getService('resource_listing')->getDocumentation('/foo');

        $this->assertInstanceOf('PSX\Api\DocumentationInterface', $documentation);

        $resource = $documentation->getResource($documentation->getLatestVersion());

        $this->assertInstanceOf('PSX\Api\Resource', $resource);
        $this->assertEquals(['GET', 'POST', 'PUT', 'DELETE'], $resource->getAllowedMethods());

        $this->assertEmpty($resource->getMethod('GET')->getRequest());
        $this->assertInstanceOf('PSX\Data\SchemaInterface', $resource->getMethod('GET')->getResponse(200));
        $this->assertInstanceOf('PSX\Data\SchemaInterface', $resource->getMethod('POST')->getRequest());
        $this->assertInstanceOf('PSX\Data\SchemaInterface', $resource->getMethod('POST')->getResponse(200));
        $this->assertInstanceOf('PSX\Data\SchemaInterface', $resource->getMethod('PUT')->getRequest());
        $this->assertInstanceOf('PSX\Data\SchemaInterface', $resource->getMethod('PUT')->getResponse(200));
        $this->assertInstanceOf('PSX\Data\SchemaInterface', $resource->getMethod('DELETE')->getRequest());
        $this->assertInstanceOf('PSX\Data\SchemaInterface', $resource->getMethod('DELETE')->getResponse(200));
    }

    protected function getPaths()
    {
        return array(
            [['GET', 'POST', 'PUT', 'DELETE'], '/bar', 'PSX\Controller\Foo\Application\TestSchemaApiController'],
            [['GET', 'POST', 'PUT', 'DELETE'], '/foo', 'PSX\Controller\Foo\Application\TestTableApiController'],
        );
    }
}
