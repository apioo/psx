<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
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
 * RomanTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class RomanTest extends \PHPUnit_Framework_TestCase
{
    public function testEncode()
    {
        $this->assertEquals('I', Roman::encode(1));
        $this->assertEquals('XVI', Roman::encode(16));
        $this->assertEquals('MCMXLXLVI', Roman::encode(1986));
    }

    public function testDecode()
    {
        $this->assertEquals(1, Roman::decode('I'));
        $this->assertEquals(16, Roman::decode('XVI'));
        $this->assertEquals(1986, Roman::decode('MCMXLXLVI'));
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    public function testEncodeZero()
    {
        $this->assertEquals('', Roman::encode(0));
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    public function testEncodeNegativeNumber()
    {
        Roman::encode(-1);
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    public function testDecodeInvalidInput()
    {
        Roman::decode('foo');
    }
}
