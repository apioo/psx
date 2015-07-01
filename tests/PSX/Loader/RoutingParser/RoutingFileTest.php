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

namespace PSX\Loader\RoutingParser;

use PSX\Loader\RoutingCollection;

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
        $routingFile = new RoutingFile('tests/PSX/Loader/routes');
        $collection  = $routingFile->getCollection();

        $this->assertInstanceOf('PSX\Loader\RoutingCollection', $collection);

        $routing = $collection->current();

        $this->assertEquals(array('GET'), $routing[RoutingCollection::ROUTING_METHODS]);
        $this->assertEquals('/', $routing[RoutingCollection::ROUTING_PATH]);
        $this->assertEquals('PSX\Loader\Foo1Controller', $routing[RoutingCollection::ROUTING_SOURCE]);
        $this->assertEquals(0, $collection->key());

        $routing = $collection->next();

        $this->assertEquals(array('GET'), $routing[RoutingCollection::ROUTING_METHODS]);
        $this->assertEquals('/foo/bar', $routing[RoutingCollection::ROUTING_PATH]);
        $this->assertEquals('PSX\Loader\Foo2Controller', $routing[RoutingCollection::ROUTING_SOURCE]);
        $this->assertEquals(1, $collection->key());

        $routing = $collection->next();

        $this->assertEquals(array('GET'), $routing[RoutingCollection::ROUTING_METHODS]);
        $this->assertEquals('/foo/:bar', $routing[RoutingCollection::ROUTING_PATH]);
        $this->assertEquals('PSX\Loader\Foo3Controller', $routing[RoutingCollection::ROUTING_SOURCE]);
        $this->assertEquals(2, $collection->key());

        $routing = $collection->next();

        $this->assertEquals(array('GET'), $routing[RoutingCollection::ROUTING_METHODS]);
        $this->assertEquals('/foo/:bar/:foo', $routing[RoutingCollection::ROUTING_PATH]);
        $this->assertEquals('PSX\Loader\Foo4Controller', $routing[RoutingCollection::ROUTING_SOURCE]);
        $this->assertEquals(3, $collection->key());

        $routing = $collection->next();

        $this->assertEquals(array('GET'), $routing[RoutingCollection::ROUTING_METHODS]);
        $this->assertEquals('/bar', $routing[RoutingCollection::ROUTING_PATH]);
        $this->assertEquals('PSX\Loader\Foo5Controller', $routing[RoutingCollection::ROUTING_SOURCE]);
        $this->assertEquals(4, $collection->key());

        $routing = $collection->next();

        $this->assertEquals(array('GET'), $routing[RoutingCollection::ROUTING_METHODS]);
        $this->assertEquals('/bar/foo', $routing[RoutingCollection::ROUTING_PATH]);
        $this->assertEquals('PSX\Loader\Foo6Controller', $routing[RoutingCollection::ROUTING_SOURCE]);
        $this->assertEquals(5, $collection->key());

        $routing = $collection->next();

        $this->assertEquals(array('GET'), $routing[RoutingCollection::ROUTING_METHODS]);
        $this->assertEquals('/bar/$foo<[0-9]+>', $routing[RoutingCollection::ROUTING_PATH]);
        $this->assertEquals('PSX\Loader\Foo7Controller', $routing[RoutingCollection::ROUTING_SOURCE]);
        $this->assertEquals(6, $collection->key());

        $routing = $collection->next();

        $this->assertEquals(array('GET'), $routing[RoutingCollection::ROUTING_METHODS]);
        $this->assertEquals('/bar/$foo<[0-9]+>/$bar<[0-9]+>', $routing[RoutingCollection::ROUTING_PATH]);
        $this->assertEquals('PSX\Loader\Foo8Controller', $routing[RoutingCollection::ROUTING_SOURCE]);
        $this->assertEquals(7, $collection->key());

        $routing = $collection->next();

        $this->assertEquals(array('POST'), $routing[RoutingCollection::ROUTING_METHODS]);
        $this->assertEquals('/bar', $routing[RoutingCollection::ROUTING_PATH]);
        $this->assertEquals('PSX\Loader\Foo9Controller', $routing[RoutingCollection::ROUTING_SOURCE]);
        $this->assertEquals(8, $collection->key());

        $routing = $collection->next();

        $this->assertEquals(array('GET'), $routing[RoutingCollection::ROUTING_METHODS]);
        $this->assertEquals('/whitespace', $routing[RoutingCollection::ROUTING_PATH]);
        $this->assertEquals('PSX\Loader\Foo10Controller', $routing[RoutingCollection::ROUTING_SOURCE]);
        $this->assertEquals(9, $collection->key());

        $routing = $collection->next();

        $this->assertEquals(array('GET', 'POST'), $routing[RoutingCollection::ROUTING_METHODS]);
        $this->assertEquals('/test', $routing[RoutingCollection::ROUTING_PATH]);
        $this->assertEquals('PSX\Loader\Foo11Controller', $routing[RoutingCollection::ROUTING_SOURCE]);
        $this->assertEquals(10, $collection->key());

        // test traversable
        foreach ($collection as $route) {
        }
    }
}
