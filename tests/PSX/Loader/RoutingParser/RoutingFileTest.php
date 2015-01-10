<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
 *
 * This file is part of psx. psx is free software: you can
 * redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or any later version.
 *
 * psx is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with psx. If not, see <http://www.gnu.org/licenses/>.
 */

namespace PSX\Loader\RoutingParser;

use PSX\Loader\RoutingCollection;

/**
 * RoutingFileTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
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
		foreach($collection as $route)
		{
		}
	}
}
