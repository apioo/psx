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

namespace PSX\Framework\Tests\Loader\RoutingParser;

use PSX\Framework\Loader\RoutingCollection;
use PSX\Framework\Loader\RoutingParser\RoutingFile;

/**
 * RoutingFileTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class RoutingFileTest extends \PHPUnit_Framework_TestCase
{
    public function testGetCollection()
    {
        $routingFile = new RoutingFile(__DIR__ . '/../routes');
        $collection  = $routingFile->getCollection();

        $this->assertInstanceOf('PSX\Framework\Loader\RoutingCollection', $collection);

        $routing = $collection->current();

        $this->assertEquals(['GET'], $routing[RoutingCollection::ROUTING_METHODS]);
        $this->assertEquals('/', $routing[RoutingCollection::ROUTING_PATH]);
        $this->assertEquals('PSX\Framework\Loader\Foo1Controller', $routing[RoutingCollection::ROUTING_SOURCE]);
        $this->assertEquals(0, $collection->key());

        $routing = $collection->next();

        $this->assertEquals(['GET'], $routing[RoutingCollection::ROUTING_METHODS]);
        $this->assertEquals('/foo/bar', $routing[RoutingCollection::ROUTING_PATH]);
        $this->assertEquals('PSX\Framework\Loader\Foo2Controller', $routing[RoutingCollection::ROUTING_SOURCE]);
        $this->assertEquals(1, $collection->key());

        $routing = $collection->next();

        $this->assertEquals(['GET'], $routing[RoutingCollection::ROUTING_METHODS]);
        $this->assertEquals('/foo/:bar', $routing[RoutingCollection::ROUTING_PATH]);
        $this->assertEquals('PSX\Framework\Loader\Foo3Controller', $routing[RoutingCollection::ROUTING_SOURCE]);
        $this->assertEquals(2, $collection->key());

        $routing = $collection->next();

        $this->assertEquals(['GET'], $routing[RoutingCollection::ROUTING_METHODS]);
        $this->assertEquals('/foo/:bar/:foo', $routing[RoutingCollection::ROUTING_PATH]);
        $this->assertEquals('PSX\Framework\Loader\Foo4Controller', $routing[RoutingCollection::ROUTING_SOURCE]);
        $this->assertEquals(3, $collection->key());

        $routing = $collection->next();

        $this->assertEquals(['GET'], $routing[RoutingCollection::ROUTING_METHODS]);
        $this->assertEquals('/bar', $routing[RoutingCollection::ROUTING_PATH]);
        $this->assertEquals('PSX\Framework\Loader\Foo5Controller', $routing[RoutingCollection::ROUTING_SOURCE]);
        $this->assertEquals(4, $collection->key());

        $routing = $collection->next();

        $this->assertEquals(['GET'], $routing[RoutingCollection::ROUTING_METHODS]);
        $this->assertEquals('/bar/foo', $routing[RoutingCollection::ROUTING_PATH]);
        $this->assertEquals('PSX\Framework\Loader\Foo6Controller', $routing[RoutingCollection::ROUTING_SOURCE]);
        $this->assertEquals(5, $collection->key());

        $routing = $collection->next();

        $this->assertEquals(['GET'], $routing[RoutingCollection::ROUTING_METHODS]);
        $this->assertEquals('/bar/$foo<[0-9]+>', $routing[RoutingCollection::ROUTING_PATH]);
        $this->assertEquals('PSX\Framework\Loader\Foo7Controller', $routing[RoutingCollection::ROUTING_SOURCE]);
        $this->assertEquals(6, $collection->key());

        $routing = $collection->next();

        $this->assertEquals(['GET'], $routing[RoutingCollection::ROUTING_METHODS]);
        $this->assertEquals('/bar/$foo<[0-9]+>/$bar<[0-9]+>', $routing[RoutingCollection::ROUTING_PATH]);
        $this->assertEquals('PSX\Framework\Loader\Foo8Controller', $routing[RoutingCollection::ROUTING_SOURCE]);
        $this->assertEquals(7, $collection->key());

        $routing = $collection->next();

        $this->assertEquals(['POST'], $routing[RoutingCollection::ROUTING_METHODS]);
        $this->assertEquals('/bar', $routing[RoutingCollection::ROUTING_PATH]);
        $this->assertEquals('PSX\Framework\Loader\Foo9Controller', $routing[RoutingCollection::ROUTING_SOURCE]);
        $this->assertEquals(8, $collection->key());

        $routing = $collection->next();

        $this->assertEquals(['GET'], $routing[RoutingCollection::ROUTING_METHODS]);
        $this->assertEquals('/whitespace', $routing[RoutingCollection::ROUTING_PATH]);
        $this->assertEquals('PSX\Framework\Loader\Foo10Controller', $routing[RoutingCollection::ROUTING_SOURCE]);
        $this->assertEquals(9, $collection->key());

        $routing = $collection->next();

        $this->assertEquals(['GET', 'POST'], $routing[RoutingCollection::ROUTING_METHODS]);
        $this->assertEquals('/test', $routing[RoutingCollection::ROUTING_PATH]);
        $this->assertEquals('PSX\Framework\Loader\Foo11Controller', $routing[RoutingCollection::ROUTING_SOURCE]);
        $this->assertEquals(10, $collection->key());

        $routing = $collection->next();

        $this->assertEquals(['GET'], $routing[RoutingCollection::ROUTING_METHODS]);
        $this->assertEquals('/alias', $routing[RoutingCollection::ROUTING_PATH]);
        $this->assertEquals('~/foo/bar', $routing[RoutingCollection::ROUTING_SOURCE]);
        $this->assertEquals(11, $collection->key());

        $routing = $collection->next();

        $this->assertEquals(['GET'], $routing[RoutingCollection::ROUTING_METHODS]);
        $this->assertEquals('/files/*path', $routing[RoutingCollection::ROUTING_PATH]);
        $this->assertEquals('PSX\Framework\Loader\Foo12Controller', $routing[RoutingCollection::ROUTING_SOURCE]);
        $this->assertEquals(12, $collection->key());

        $routing = $collection->next();

        $this->assertEquals(['GET'], $routing[RoutingCollection::ROUTING_METHODS]);
        $this->assertEquals('http://cdn.foo.com/serve/*path', $routing[RoutingCollection::ROUTING_PATH]);
        $this->assertEquals('PSX\Framework\Loader\Foo13Controller', $routing[RoutingCollection::ROUTING_SOURCE]);
        $this->assertEquals(13, $collection->key());

        $routing = $collection->next();

        $this->assertEquals(['GET', 'HEAD', 'POST', 'PUT', 'DELETE', 'PATCH'], $routing[RoutingCollection::ROUTING_METHODS]);
        $this->assertEquals('/baz', $routing[RoutingCollection::ROUTING_PATH]);
        $this->assertEquals('PSX\Framework\Loader\Foo14Controller', $routing[RoutingCollection::ROUTING_SOURCE]);
        $this->assertEquals(14, $collection->key());

        // test traversable
        foreach ($collection as $route) {
        }
    }
}
