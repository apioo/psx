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

use Doctrine\Common\Cache\ArrayCache;
use PSX\Cache\Pool;
use PSX\Framework\Loader\RoutingParser\CachedParser;
use PSX\Framework\Loader\RoutingParser\RoutingFile;

/**
 * CachedParserTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class CachedParserTest extends \PHPUnit_Framework_TestCase
{
    public function testGetCollection()
    {
        $cache         = new Pool(new ArrayCache());
        $routing       = new RoutingFile(__DIR__ . '/../routes');
        $routingParser = new CachedParser($routing, $cache);

        // we remove previous cache
        $cache->deleteItems([CachedParser::CACHE_KEY]);

        // get collection from the parser
        $collection = $routingParser->getCollection();

        $this->assertInstanceOf('PSX\Framework\Loader\RoutingCollection', $collection);
        $this->assertEquals(15, count($collection));

        // get collection from the cache
        $collection = $routingParser->getCollection();

        $this->assertInstanceOf('PSX\Framework\Loader\RoutingCollection', $routingParser->getCollection());
        $this->assertEquals(15, count($collection));
    }
}
