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
 * AnnotationTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
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
		foreach($collection as $route)
		{
			if($route[RoutingCollection::ROUTING_PATH] == $path)
			{
				return $route;
			}
		}

		return null;
	}
}
