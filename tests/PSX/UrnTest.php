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

namespace PSX;

/**
 * UrnTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class UrnTest extends \PHPUnit_Framework_TestCase
{
    public function testUrn()
    {
        $urn = new Urn('urn:uuid:f81d4fae-7dec-11d0-a765-00a0c91e6bf6');

        $this->assertEquals('urn', $urn->getScheme());
        $this->assertEquals('uuid:f81d4fae-7dec-11d0-a765-00a0c91e6bf6', $urn->getPath());
        $this->assertEquals('uuid', $urn->getNid());
        $this->assertEquals('f81d4fae-7dec-11d0-a765-00a0c91e6bf6', $urn->getNss());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidUrn()
    {
        new Urn('foobar');
    }

    public function testUrnCompare()
    {
        $urns = array(
            'URN:foo:a123,456',
            'urn:foo:a123,456',
            'urn:FOO:a123,456',
            'urn:foo:A123,456',
            'urn:foo:a123%2C456',
            'URN:FOO:a123%2c456',
        );

        foreach ($urns as $rawUrn) {
            $urn = new Urn($rawUrn);

            $this->assertEquals('urn:foo:a123,456', $urn->__toString());
        }
    }
}
