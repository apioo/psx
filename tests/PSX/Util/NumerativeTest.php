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

namespace PSX\Util;

/**
 * NumerativeTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class NumerativeTest extends \PHPUnit_Framework_TestCase
{
    public function testNumerativeBin()
    {
        $this->assertEquals(20, Numerative::bin2oct(10000));
        $this->assertEquals(16, Numerative::bin2dez(10000));
        $this->assertEquals(10, Numerative::bin2hex(10000));
    }

    public function testNumerativeOct()
    {
        $this->assertEquals(10000, Numerative::oct2bin(20));
        $this->assertEquals(16, Numerative::oct2dez(20));
        $this->assertEquals(10, Numerative::oct2hex(20));
    }

    public function testNumerativeDez()
    {
        $this->assertEquals(10000, Numerative::dez2bin(16));
        $this->assertEquals(20, Numerative::dez2oct(16));
        $this->assertEquals(10, Numerative::dez2hex(16));
    }

    public function testNumerativeHex()
    {
        $this->assertEquals(10000, Numerative::hex2bin(10));
        $this->assertEquals(20, Numerative::hex2oct(10));
        $this->assertEquals(16, Numerative::hex2dez(10));
    }
}
