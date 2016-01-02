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
 * FileTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class FileTest extends \PHPUnit_Framework_TestCase
{
    private $path;

    protected function setUp()
    {
        $this->path = PSX_PATH_CACHE . '/' . $this->getFileName();
    }

    protected function tearDown()
    {
        //unlink($this->path);
    }

    public function testFileWrite()
    {
        $file = new File($this->path);
        $file = $file->openFile('w');

        $this->assertEquals($this->getFileName(), $file->getFilename());
        $this->assertEquals(PSX_PATH_CACHE, $file->getPath());
        $this->assertEquals(true, $file->isFile());
        $this->assertEquals(true, File::exists($this->path));

        $bytes = $file->fwrite('foobar');

        $this->assertEquals(6, $bytes);

        unset($file);

        $this->assertEquals('foobar', File::getContents($this->path));
    }

    /**
     * @depends testFileWrite
     */
    public function testFileRead()
    {
        $file = File::open($this->path, 'r');

        $buffer = '';

        while (!$file->eof()) {
            $buffer.= $file->fgets();
        }

        $this->assertEquals('foobar', $buffer);

        // we are in read mode we cant write
        $bytes = $file->fwrite('something');

        $this->assertEquals(0, $bytes);


        unset($file);
    }

    public function testPutGetContents()
    {
        $this->assertEquals(2, File::putContents($this->path, 'Oo'));
        $this->assertEquals('Oo', File::getContents($this->path));
    }

    public function testNormalizeName()
    {
        $this->assertEquals('foo.txt', File::normalizeName('foo.txt'));
        $this->assertEquals('--.0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ_abcdefghijklmnopqrstuvwxyz', File::normalizeName("\x00\x01\x02\x03\x04\x05\x06\x07\x08\x09\x0a\x0b\x0c\x0d\x0e\x0f\x10\x11\x12\x13\x14\x15\x16\x17\x18\x19\x1a\x1b\x1c\x1d\x1e\x1f\x20\x21\x22\x23\x24\x25\x26\x27\x28\x29\x2a\x2b\x2c\x2d\x2e\x2f\x30\x31\x32\x33\x34\x35\x36\x37\x38\x39\x3a\x3b\x3c\x3d\x3e\x3f\x40\x41\x42\x43\x44\x45\x46\x47\x48\x49\x4a\x4b\x4c\x4d\x4e\x4f\x50\x51\x52\x53\x54\x55\x56\x57\x58\x59\x5a\x5b\x5c\x5d\x5e\x5f\x60\x61\x62\x63\x64\x65\x66\x67\x68\x69\x6a\x6b\x6c\x6d\x6e\x6f\x70\x71\x72\x73\x74\x75\x76\x77\x78\x79\x7a\x7b\x7c\x7d\x7e\x7f"));
    }

    private function getFileName()
    {
        return 'FileTest.txt';
    }
}
