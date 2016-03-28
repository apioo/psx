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

namespace PSX\Data\Tests;

use PSX\Data\Payload;
use PSX\Data\Processor;
use PSX\Data\Record;
use PSX\Data\Tests\Processor\Model\Entry;
use PSX\Framework\Test\Environment;
use PSX\Schema\Visitor\OutgoingVisitor;
use PSX\Validate\Filter;

/**
 * ProcessorTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ProcessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PSX\Data\Processor
     */
    protected $processor;

    protected function setUp()
    {
        $this->processor = Environment::getService('io');
    }

    public function testRead()
    {
        $entry = $this->processor->read(Entry::class, Payload::json('{"title": "foo"}'));

        $this->assertInstanceOf('PSX\Data\Tests\Processor\Model\Entry', $entry);
        $this->assertEquals('foo', $entry->getTitle());
    }

    public function testParse()
    {
        $data = $this->processor->parse(Payload::json('{"title": "foo"}'));

        $this->assertInstanceOf('stdClass', $data);
        $this->assertEquals('foo', $data->title);
    }

    public function testWrite()
    {
        $entry = new Entry();
        $entry->setTitle('foo');

        $data = $this->processor->write(Payload::json($entry));

        $this->assertJsonStringEqualsJsonString('{"title": "foo"}', $data);
    }

    public function testTransform()
    {
        $entry = new Entry();
        $entry->setTitle('foo');

        $data = $this->processor->transform($entry);

        $this->assertInstanceOf('PSX\Data\Record', $data);
        $this->assertEquals('foo', $data->title);
    }

    /**
     * @expectedException \PSX\Schema\ValidationException
     */
    public function testAssimilateIncoming()
    {
        $data = new \stdClass();
        $data->title = 'foo';
        $data->bar = 'foo';

        $schema = $this->processor->getSchema(Entry::class);

        $this->processor->assimilate($data, $schema);
    }

    public function testAssimilateOutgoing()
    {
        $data = new \stdClass();
        $data->title = 'foo';
        $data->bar = 'foo';

        $schema = $this->processor->getSchema(Entry::class);

        // the outgoing visitor silently removes unknown properties
        $data = $this->processor->assimilate($data, $schema, null, null, new OutgoingVisitor());

        $this->assertInstanceOf('PSX\Data\Record', $data);
        $this->assertEquals('foo', $data->title);
        $this->assertEquals(['title' => 'foo'], $data->getProperties());
    }
}
