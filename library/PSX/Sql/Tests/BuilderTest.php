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
 * BuilderTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class BuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testBuild()
    {
        $news = [[
            'id' => 1,
            'authorId' => 1,
            'title' => 'foo',
            'createDate' => '2016-03-01 00:00:00',
        ],[
            'id' => 2,
            'authorId' => 1,
            'title' => 'bar',
            'createDate' => '2016-03-01 00:00:00',
        ]];

        $author = [
            'id' => 1,
            'name' => 'Foo Bar',
            'uri' => 'http://phpsx.org',
        ];

        $definition = [
            'totalEntries' => 2,
            'entries' => new Map\Collection($news, [
                'id' => 'id',
                'title' => new Field\Callback('title', function($title){
                    return ucfirst($title);
                }),
                'isNew' => new Field\Value(true),
                'author' => new Map\Entity($author, [
                    'displayName' => 'name',
                    'uri' => 'uri',
                ]),
                'date' => new Field\DateTime('createDate'),
                'links' => [
                    'self' => new Field\Replace('http://foobar.com/news/{id}'),
                ]
            ])
        ];

        $expect = <<<JSON
{
    "totalEntries": 2,
    "entries": [
        {
            "id": 1,
            "title": "Foo",
            "isNew": true,
            "author": {
                "displayName": "Foo Bar",
                "uri": "http:\/\/phpsx.org"
            },
            "date": "2016-03-01T00:00:00+00:00",
            "links": {
                "self": "http:\/\/foobar.com\/news\/1"
            }
        },
        {
            "id": 2,
            "title": "Bar",
            "isNew": true,
            "author": {
                "displayName": "Foo Bar",
                "uri": "http:\/\/phpsx.org"
            },
            "date": "2016-03-01T00:00:00+00:00",
            "links": {
                "self": "http:\/\/foobar.com\/news\/2"
            }
        }
    ]
}
JSON;

        $builder = new Builder();
        $result  = json_encode($builder->build($definition), JSON_PRETTY_PRINT);

        $this->assertJsonStringEqualsJsonString($expect, $result, $result);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testBuildUnknownFieldInContext()
    {
        $data = [
            'foo' => 'bar',
        ];

        $definition = [
            'fields' => new Map\Entity($data, [
                'test' => 'test'
            ]),
        ];

        $builder = new Builder();
        $builder->build($definition);
    }

    public function testBuildUnknownFieldWithoutContext()
    {
        $definition = [
            'foo' => 'bar',
        ];

        $builder = new Builder();
        $result  = $builder->build($definition);

        $this->assertEquals($definition, $result);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testBuildInvalidCollectionResult()
    {
        $data = [
            'foo' => 'bar',
        ];

        $definition = [
            'fields' => new Map\Collection($data, [
                'test' => 'test'
            ]),
        ];

        $builder = new Builder();
        $builder->build($definition);
    }
}