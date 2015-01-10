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

use PSX\Cache;
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
		$cache         = new Cache(new Cache\Handler\Memory());
		$routing       = new RoutingFile('tests/PSX/Loader/routes');
		$routingParser = new CachedParser($routing, $cache);

		// we remove previous cache
		$cache->deleteItems([CachedParser::CACHE_KEY]);

		// get collection from the parser
		$collection = $routingParser->getCollection();

		$this->assertInstanceOf('PSX\Loader\RoutingCollection', $collection);
		$this->assertEquals(14, count($collection));

		// get collection from the cache
		$collection = $routingParser->getCollection();

		$this->assertInstanceOf('PSX\Loader\RoutingCollection', $routingParser->getCollection());
		$this->assertEquals(14, count($collection));
	}
}
