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

namespace PSX\Sql\Tests\Provider;

use PSX\Sql\Tests\ProviderTestCase;
use PSX\Sql\Reference;
use PSX\Sql\Provider\Callback;

/**
 * CallbackTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class CallbackTest extends ProviderTestCase
{
    protected $authorId;

    protected function getDefinition()
    {
        $this->authorId = 0;

        return [
            'totalEntries' => 2,
            'entries' => new Callback\Collection([$this, 'dataNews'], [], [
                'id' => 'id',
                'title' => 'title',
                'author' => new Callback\Entity([$this, 'dataAuthor'], [new Reference('authorId'), 'bar'], [
                    'displayName' => 'name',
                    'uri' => 'uri',
                ]),
            ])
        ];
    }

    public function dataNews()
    {
        return [[
            'id' => 1,
            'authorId' => 1,
            'title' => 'foo',
            'createDate' => '2016-03-01 00:00:00',
        ], [
            'id' => 2,
            'authorId' => 2,
            'title' => 'bar',
            'createDate' => '2016-03-01 00:00:00',
        ]];
    }

    public function dataAuthor($authorId, $foo)
    {
        $this->authorId++;
        $this->assertEquals($this->authorId, $authorId);
        $this->assertEquals('bar', $foo);

        return [
            'name' => 'Foo Bar',
            'uri' => 'http://phpsx.org'
        ];
    }
}
