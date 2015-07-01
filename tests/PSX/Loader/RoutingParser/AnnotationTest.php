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
 * AnnotationTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class AnnotationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Note the order in which the classes gets parsed is not predictable
     * because of that we search in this test for each specific route
     */
    public function testGetCollection()
    {
        $routingParser = new Annotation(array('tests/PSX/Loader/RoutingParser/Annotation'));
        $collection    = $routingParser->getCollection();

        $this->assertInstanceOf('PSX\Loader\RoutingCollection', $collection);

        $routing = $this->findRoute($collection, '/');

        $this->assertEquals(array('GET'), $routing[RoutingCollection::ROUTING_METHODS]);
        $this->assertEquals('/', $routing[RoutingCollection::ROUTING_PATH]);
        $this->assertEquals('PSX\Loader\RoutingParser\Annotation\BarController::doIndex', $routing[RoutingCollection::ROUTING_SOURCE]);

        $routing = $this->findRoute($collection, '/detail/:id');

        $this->assertEquals(array('GET'), $routing[RoutingCollection::ROUTING_METHODS]);
        $this->assertEquals('/detail/:id', $routing[RoutingCollection::ROUTING_PATH]);
        $this->assertEquals('PSX\Loader\RoutingParser\Annotation\BarController::doShowDetails', $routing[RoutingCollection::ROUTING_SOURCE]);

        $routing = $this->findRoute($collection, '/new');

        $this->assertEquals(array('POST'), $routing[RoutingCollection::ROUTING_METHODS]);
        $this->assertEquals('/new', $routing[RoutingCollection::ROUTING_PATH]);
        $this->assertEquals('PSX\Loader\RoutingParser\Annotation\BarController::doInsert', $routing[RoutingCollection::ROUTING_SOURCE]);

        $routing = $this->findRoute($collection, '/foo');

        $this->assertEquals(array('GET'), $routing[RoutingCollection::ROUTING_METHODS]);
        $this->assertEquals('/foo', $routing[RoutingCollection::ROUTING_PATH]);
        $this->assertEquals('PSX\Loader\RoutingParser\Annotation\FooController::doIndex', $routing[RoutingCollection::ROUTING_SOURCE]);

        $routing = $this->findRoute($collection, '/foo/detail/$foo<[0-9]+>');

        $this->assertEquals(array('GET'), $routing[RoutingCollection::ROUTING_METHODS]);
        $this->assertEquals('/foo/detail/$foo<[0-9]+>', $routing[RoutingCollection::ROUTING_PATH]);
        $this->assertEquals('PSX\Loader\RoutingParser\Annotation\FooController::doShowDetails', $routing[RoutingCollection::ROUTING_SOURCE]);

        $routing = $this->findRoute($collection, '/foo/new');

        $this->assertEquals(array('GET', 'POST'), $routing[RoutingCollection::ROUTING_METHODS]);
        $this->assertEquals('/foo/new', $routing[RoutingCollection::ROUTING_PATH]);
        $this->assertEquals('PSX\Loader\RoutingParser\Annotation\FooController::doInsert', $routing[RoutingCollection::ROUTING_SOURCE]);
    }

    protected function findRoute(RoutingCollection $collection, $path)
    {
        foreach ($collection as $route) {
            if ($route[RoutingCollection::ROUTING_PATH] == $path) {
                return $route;
            }
        }

        return null;
    }
}
