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

namespace PSX\Framework\Tests\Controller;

use PSX\Framework\Test\ControllerDbTestCase;

/**
 * Tests the API controller in combination with the sql table. We simply return
 * some results from the database and check whether this can be transformed in
 * the right json format
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ApiAbstractTableTest extends ControllerDbTestCase
{
    public function getDataSet()
    {
        return $this->createFlatXMLDataSet(__DIR__ . '/../../../Sql/Tests/table_fixture.xml');
    }

    public function testAll()
    {
        $response = $this->sendRequest('http://127.0.0.1/api', 'GET');
        $body     = (string) $response->getBody();

        $expect = <<<JSON
{
    "entry": [
        {
            "id": 4,
            "userId": 3,
            "title": "blub",
            "date": "2013-04-29T16:56:32Z"
        },
        {
            "id": 3,
            "userId": 2,
            "title": "test",
            "date": "2013-04-29T16:56:32Z"
        },
        {
            "id": 2,
            "userId": 1,
            "title": "bar",
            "date": "2013-04-29T16:56:32Z"
        },
        {
            "id": 1,
            "userId": 1,
            "title": "foo",
            "date": "2013-04-29T16:56:32Z"
        }
    ]
}
JSON;

        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }

    public function testRow()
    {
        $response = $this->sendRequest('http://127.0.0.1/api/row', 'GET');
        $body     = (string) $response->getBody();

        $expect = <<<JSON
{
    "id": 1,
    "userId": 1,
    "title": "foo",
    "date": "2013-04-29T16:56:32Z"
}
JSON;

        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }

    public function testNested()
    {
        $response = $this->sendRequest('http://127.0.0.1/api/nested', 'GET');
        $body     = (string) $response->getBody();

        $expect = <<<JSON
{
    "entry": [
        {
            "id": 4,
            "title": "blub",
            "author": {
                "userId": 3,
                "date": "2013-04-29T16:56:32Z"
            }
        },
        {
            "id": 3,
            "title": "test",
            "author": {
                "userId": 2,
                "date": "2013-04-29T16:56:32Z"
            }
        },
        {
            "id": 2,
            "title": "bar",
            "author": {
                "userId": 1,
                "date": "2013-04-29T16:56:32Z"
            }
        },
        {
            "id": 1,
            "title": "foo",
            "author": {
                "userId": 1,
                "date": "2013-04-29T16:56:32Z"
            }
        }
    ]
}
JSON;

        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }

    protected function getPaths()
    {
        return array(
            [['GET'], '/api', 'PSX\Framework\Tests\Controller\Foo\Application\TestApiTableController::doAll'],
            [['GET'], '/api/row', 'PSX\Framework\Tests\Controller\Foo\Application\TestApiTableController::doRow'],
            [['GET'], '/api/nested', 'PSX\Framework\Tests\Controller\Foo\Application\TestApiTableController::doNested'],
        );
    }
}
