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

namespace PSX\Framework\Tests\Util;

use PSX\Framework\Util\Uuid;

/**
 * UuidTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class UuidTest extends \PHPUnit_Framework_TestCase
{
    public function testTimeBase()
    {
        $uuid = Uuid::timeBased();
        sleep(1);
        $this->assertTrue($uuid != Uuid::timeBased());
    }

    public function testPseudoRandom()
    {
        $uuid = Uuid::pseudoRandom();

        $this->assertTrue($uuid != Uuid::pseudoRandom());
    }

    public function testNameBased()
    {
        // test UUID parts
        $uuid = explode('-', Uuid::nameBased('bar'));

        $this->assertEquals(5, count($uuid));
        $this->assertEquals(true, ctype_xdigit($uuid[0]), 'time-low');
        $this->assertEquals(true, ctype_xdigit($uuid[1]), 'time-mid');
        $this->assertEquals(true, ctype_xdigit($uuid[2]), 'time-high-and-version');
        $this->assertEquals(true, ctype_xdigit($uuid[3]), 'clock-seq-and-reserved / clock-seq-low');
        $this->assertEquals(true, ctype_xdigit($uuid[4]), 'node');
        $this->assertEquals(8, strlen($uuid[0]), 'time-low');
        $this->assertEquals(4, strlen($uuid[1]), 'time-mid');
        $this->assertEquals(4, strlen($uuid[2]), 'time-high-and-version');
        $this->assertEquals(4, strlen($uuid[3]), 'clock-seq-and-reserved / clock-seq-low');
        $this->assertEquals(12, strlen($uuid[4]), 'node');

        $this->assertEquals(5, hexdec($uuid[2]) >> 12, 'Set the four most significant bits (bits 12 through 15) of the time_hi_and_version field to the appropriate 4-bit version number from Section 4.1.3.');
        $this->assertEquals(2, hexdec($uuid[3]) >> 14, 'Set the two most significant bits (bits 6 and 7) of the clock_seq_hi_and_reserved to zero and one, respectively.');

        // the UUIDs generated at different times from the same name in the same
        // namespace MUST be equal.
        $uuid = Uuid::nameBased('foobar');
        sleep(1);
        $this->assertEquals($uuid, Uuid::nameBased('foobar'));

        // the UUIDs generated from two different names in the same namespace
        // should be different (with very high probability).
        $this->assertTrue(Uuid::nameBased('foobar') != Uuid::nameBased('bar'));
    }
}
