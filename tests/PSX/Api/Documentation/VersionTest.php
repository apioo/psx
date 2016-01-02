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
 * VersionTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class VersionTest extends \PHPUnit_Framework_TestCase
{
    public function testVersion()
    {
        $resource1 = new Resource(Resource::STATUS_ACTIVE, '/foo');
        $resource2 = new Resource(Resource::STATUS_ACTIVE, '/foo');
        $version   = new Version('foo');

        $version->addResource(1, $resource1);
        $version->addResource(2, $resource2);

        $this->assertTrue($version->hasResource(1));
        $this->assertTrue($version->hasResource(2));
        $this->assertFalse($version->hasResource(8));
        $this->assertEquals($resource1, $version->getResource(1));
        $this->assertEquals($resource2, $version->getResource(2));
        $this->assertEquals(null, $version->getResource(8));
        $this->assertEquals(array(1 => $resource1, 2 => $resource2), $version->getResources());
        $this->assertEquals(2, $version->getLatestVersion());
        $this->assertTrue($version->isVersionRequired());
        $this->assertEquals('foo', $version->getDescription());
    }

    public function testGetLatestVersionNoResource()
    {
        $version = new Version();

        $this->assertEquals(1, $version->getLatestVersion());
    }
}
