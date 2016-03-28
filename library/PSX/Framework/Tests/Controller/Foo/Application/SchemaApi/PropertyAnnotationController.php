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

use PSX\Framework\Controller\AnnotationApiAbstract;
use PSX\Framework\Tests\Controller\SchemaApi\PropertyTestCase;
use PSX\Data\RecordInterface;

/**
 * PropertyAnnotationController
 *
 * @PathParam(name="id", type="integer")
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class PropertyAnnotationController extends AnnotationApiAbstract
{
    use PropertyControllerTrait;

    /**
     * @QueryParam(name="type", type="integer")
     * @Outgoing(code=200, schema="../../Resource/property.json")
     */
    protected function doGet()
    {
        $this->testCase->assertEquals(1, $this->pathParameters->getProperty('id'));

        return PropertyTestCase::getDataByType($this->queryParameters->getProperty('type'));
    }

    /**
     * @Incoming(schema="../../Resource/property.json")
     * @Outgoing(code=200, schema="../../Resource/property.json")
     */
    protected function doPost(RecordInterface $record)
    {
        PropertyTestCase::assertRecord($this->testCase, $record);

        return $record;
    }
}
