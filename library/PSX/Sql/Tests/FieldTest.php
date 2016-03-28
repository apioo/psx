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

namespace PSX\Sql\Tests;

use PSX\Sql\Builder;
use PSX\Sql\Provider\Map;
use PSX\Sql\Field;

/**
 * FieldTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class FieldTest extends \PHPUnit_Framework_TestCase
{
    public function testFields()
    {
        $data = [
            'boolean' => '1',
            'callback' => 'foo',
            'dateTime' => '2016-03-01 00:00:00',
            'number' => '1.4',
            'integer' => '2',
        ];

        $definition = [
            'fields' => new Map\Entity($data, [
                'boolean' => new Field\Boolean('boolean'),
                'callback' => new Field\Callback('callback', function($value){
                    return ucfirst($value);
                }),
                'dateTime' => new Field\DateTime('dateTime'),
                'number' => new Field\Number('number'),
                'integer' => new Field\Integer('integer'),
                'replace' => new Field\Replace('http://foobar.com/entry/{integer}'),
                'value' => new Field\Value('bar'),
            ]),
        ];

        $builder = new Builder();
        $result  = json_encode($builder->build($definition), JSON_PRETTY_PRINT);

        $expect = <<<JSON
{
    "fields": {
        "boolean": true,
        "callback": "Foo",
        "dateTime": "2016-03-01T00:00:00+00:00",
        "number": 1.4,
        "integer": 2,
        "replace": "http:\/\/foobar.com\/entry\/2",
        "value": "bar"
    }
}
JSON;

        $this->assertJsonStringEqualsJsonString($expect, $result, $result);
    }
}