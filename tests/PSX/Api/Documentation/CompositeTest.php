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

namespace PSX\Api\Documentation;

use PSX\Api\Resource;

/**
 * CompositeTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class CompositeTest extends \PHPUnit_Framework_TestCase
{
    public function testVersion()
    {
        $resource1 = new Resource(Resource::STATUS_ACTIVE, '/foo');
        $resource2 = new Resource(Resource::STATUS_ACTIVE, '/foo');

        $doc1 = new Explicit(1, $resource1, 'foo');
        $doc2 = new Explicit(2, $resource2, 'foo');
        $doc  = new Composite([$doc1, $doc2], 'foo');

        $this->assertTrue($doc->hasResource(1));
        $this->assertTrue($doc->hasResource(2));
        $this->assertFalse($doc->hasResource(8));
        $this->assertEquals($resource1, $doc->getResource(1));
        $this->assertEquals($resource2, $doc->getResource(2));
        $this->assertEquals(null, $doc->getResource(8));
        $this->assertEquals([1 => $resource1, 2 => $resource2], $doc->getResources());
        $this->assertEquals(2, $doc->getLatestVersion());
        $this->assertTrue($doc->isVersionRequired());
        $this->assertEquals('foo', $doc->getDescription());
    }

    public function testGetLatestVersionNoResource()
    {
        $doc = new Composite([]);

        $this->assertEquals(1, $doc->getLatestVersion());
    }
}
