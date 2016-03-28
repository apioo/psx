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

namespace PSX\Framework\Tests\Console\Reader;

use PSX\Framework\Console\Reader\Stdin;

/**
 * StdinTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class StdinTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorEmpty()
    {
        $reader = new Stdin();

        $this->assertInstanceOf('PSX\Framework\Console\ReaderInterface', $reader);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorWrongType()
    {
        new Stdin(array());
    }

    public function testRead()
    {
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, 'foobar' . "\n" . 'foobar');
        rewind($stream);

        $reader = new Stdin($stream);

        $this->assertEquals('foobar' . "\n" . 'foobar', $reader->read());
    }

    public function testReadEOTCharacterMiddle()
    {
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, 'foobar' . "\n" . 'foo' . "\x04" . 'bar');
        rewind($stream);

        $reader = new Stdin($stream);

        $this->assertEquals('foobar' . "\n" . 'foo', $reader->read());
    }

    public function testReadEOTCharacterStart()
    {
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, 'foobar' . "\n" . "\x04" . 'foobar');
        rewind($stream);

        $reader = new Stdin($stream);

        $this->assertEquals('foobar' . "\n", $reader->read());
    }

    public function testReadEOTCharacterEnd()
    {
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, 'foobar' . "\x04" . "\n" . 'foobar');
        rewind($stream);

        $reader = new Stdin($stream);

        $this->assertEquals('foobar', $reader->read());
    }
}
