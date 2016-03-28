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

use PSX\Framework\Controller\ApiAbstract;
use PSX\Validate\Filter;
use PSX\Http\Message;
use PSX\Validate\Validate;
use PSX\Validate\Property;
use PSX\Validate\Validator;

/**
 * TestApiValidateController
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class TestApiValidateController extends ApiAbstract
{
    /**
     * @Inject
     * @var \PHPUnit_Framework_TestCase
     */
    protected $testCase;

    /**
     * @Inject
     * @var \PSX\Schema\SchemaManager
     */
    protected $schemaManager;

    public function doIndex()
    {
        $this->setBody([
            'foo' => 'bar'
        ]);
    }

    public function doInsert()
    {
        $schema    = $this->schemaManager->getSchema('PSX\Framework\Tests\Controller\Foo\Schema\NestedEntry');
        $validator = new Validator([
            new Property('/title', Validate::TYPE_STRING, [new Filter\Length(3, 8)]),
            new Property('/author/name', Validate::TYPE_STRING, [new Filter\Length(3, 8)]),
        ]);

        $data = $this->getBodyAs($schema, $validator);

        $this->testCase->assertInstanceOf('PSX\Data\RecordInterface', $data);

        $this->setBody([
            'success' => true,
        ]);
    }
}
