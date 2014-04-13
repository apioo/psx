<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2014 Christoph Kappestein <k42b3.x@gmail.com>
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
 * CachedParserTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class CachedParserTest extends \PHPUnit_Framework_TestCase
{
	public function testGetCollection()
	{
		$routing = new RoutingFile('tests/PSX/Loader/routes');
		$cache   = $this->getMock('PSX\Cache\HandlerInterface');

		$cache->expects($this->once())
			->method('load')
			->with($this->equalTo('routing_file'))
			->will($this->returnValue(false));

		$cache->expects($this->once())
			->method('write')
			->with($this->equalTo('routing_file'), $this->equalTo(json_encode($routing->getCollection()->getAll())));

		$routingParser = new CachedParser($routing, $cache);
		$collection    = $routingParser->getCollection();

		$this->assertInstanceOf('PSX\Loader\RoutingCollection', $collection);
	}
}
