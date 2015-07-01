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

namespace PSX\Http\Stream;

/**
 * StreamTestCase
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class StreamTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PSX\Http\StreamInterface
     */
    protected $stream;

    protected function setUp()
    {
        $this->stream = $this->getStream();
    }

    protected function tearDown()
    {
        $this->stream->close();
    }

    /**
     * Returns the stream wich gets tested. Must contain the string foobar
     *
     * @return \PSX\Http\StreamInterface
     */
    abstract protected function getStream();

    public function testGetSize()
    {
        $this->assertEquals(6, $this->stream->getSize());
    }

    public function testTell()
    {
        if ($this->stream->isSeekable()) {
            $this->assertEquals(0, $this->stream->tell());
            $this->stream->seek(2);
            $this->assertEquals(2, $this->stream->tell());
        }
    }

    public function testDetach()
    {
        $handle = $this->stream->detach();

        $this->assertTrue(is_resource($handle));

        $meta = stream_get_meta_data($handle);
        if ($meta['mode'] != 'w') {
            $this->assertEquals('foobar', stream_get_contents($handle, -1, 0));
        }

        // after detatching the stream object is in an unusable state but this
        // should not produce any error on further method calls
        $this->assertEquals('', $this->stream->__toString());
        $this->assertEquals(null, $this->stream->close());
        $this->assertEquals(null, $this->stream->detach());
        $this->assertEquals(null, $this->stream->getSize());
        $this->assertEquals(false, $this->stream->tell());
        // after detaching eof returns always true to not enter any while(!eof)
        $this->assertEquals(true, $this->stream->eof());
        $this->assertEquals(false, $this->stream->isSeekable());
        $this->assertEquals(false, $this->stream->seek(0));
        $this->assertEquals(false, $this->stream->isWritable());
        $this->assertEquals(false, $this->stream->write('foo'));
        $this->assertEquals(false, $this->stream->isReadable());
        $this->assertEquals(false, $this->stream->read(2));
        $this->assertEquals(null, $this->stream->getContents());
    }

    public function testEof()
    {
        if ($this->stream->isReadable()) {
            $content = '';

            while (!$this->stream->eof()) {
                $content.= $this->stream->read(5);
            }

            $this->assertEquals('foobar', $content);
        }
    }

    public function testRewind()
    {
        if ($this->stream->isSeekable()) {
            $this->assertEquals(0, $this->stream->tell());
            $this->stream->seek(2);
            $this->assertEquals(2, $this->stream->tell());
            $this->stream->rewind();
            $this->assertEquals(0, $this->stream->tell());
        }
    }

    public function testIsSeekable()
    {
        $result = $this->stream->isSeekable();

        $this->assertTrue(is_bool($result));
    }

    public function testSeek()
    {
        if ($this->stream->isSeekable()) {
            $this->assertEquals(0, $this->stream->tell());
            $this->stream->seek(2);
            $this->assertEquals(2, $this->stream->tell());
            $this->stream->seek(2, SEEK_CUR);
            $this->assertEquals(4, $this->stream->tell());
            $this->stream->seek(0, SEEK_END);
            $this->assertEquals(6, $this->stream->tell());
            $this->stream->seek(0);
            $this->assertEquals(0, $this->stream->tell());
        }
    }

    public function testIsWritable()
    {
        $result = $this->stream->isWritable();

        $this->assertTrue(is_bool($result));
    }

    public function testWrite()
    {
        if ($this->stream->isWritable()) {
            $this->assertEquals(0, $this->stream->tell());
            $this->stream->seek(0, SEEK_END);
            $this->assertEquals(6, $this->stream->tell());
            $this->stream->write('bar');
            $this->assertEquals(9, $this->stream->tell());
            $this->stream->write('fooooooo');
            $this->assertEquals(17, $this->stream->tell());
            $this->stream->seek(12);
            $this->assertEquals(12, $this->stream->tell());
            $this->stream->write('bar');
            $this->assertEquals(15, $this->stream->tell());

            if ($this->stream->isReadable()) {
                $this->assertEquals('foobarbarfoobaroo', (string) $this->stream);
            }
        }
    }

    public function testIsReadable()
    {
        $result = $this->stream->isReadable();

        $this->assertTrue(is_bool($result));
    }

    public function testRead()
    {
        if ($this->stream->isReadable()) {
            $this->assertEquals(0, $this->stream->tell());
            $this->assertEquals('fo', $this->stream->read(2));
            $this->assertEquals(2, $this->stream->tell());
        }
    }

    public function testGetContents()
    {
        if ($this->stream->isReadable() && $this->stream->isSeekable()) {
            $this->assertEquals(0, $this->stream->tell());
            $this->assertEquals('foobar', $this->stream->getContents());
            $this->assertEquals(6, $this->stream->tell());
            $this->stream->seek(2);
            $this->assertEquals(2, $this->stream->tell());
            $this->assertEquals('obar', $this->stream->getContents());
            $this->assertEquals(6, $this->stream->tell());
        }
    }

    public function testGetContentsOffset()
    {
        if ($this->stream->isReadable() && $this->stream->isSeekable()) {
            $this->assertEquals(0, $this->stream->tell());
            $this->assertEquals('foobar', $this->stream->getContents());
            $this->assertEquals(6, $this->stream->tell());
            $this->stream->seek(2);
            $this->assertEquals(2, $this->stream->tell());
            $this->assertEquals('ob', $this->stream->getContents(2));
            $this->assertEquals(4, $this->stream->tell());
        }
    }

    public function testGetMetadata()
    {
        $this->assertTrue(is_array($this->stream->getMetadata()));
    }

    public function testGetMetadataKey()
    {
        $this->assertEmpty($this->stream->getMetadata('foo'));

        // call an key which exists in the metadata
        $uri = $this->stream->getMetadata('uri');
    }

    public function testToString()
    {
        if ($this->stream->isReadable()) {
            $this->assertEquals(0, $this->stream->tell());
            $this->assertEquals('foobar', (string) $this->stream);
            $this->assertEquals(6, $this->stream->tell());
        } else {
            $this->assertEquals('', (string) $this->stream);
        }
    }
}
