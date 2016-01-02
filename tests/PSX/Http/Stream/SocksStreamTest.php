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

namespace PSX\Http\Stream;

/**
 * SocksStreamTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class SocksStreamTest extends StreamTestCase
{
    protected function getStream()
    {
        $resource = fopen('php://memory', 'r+');
        fwrite($resource, 'foobar');
        rewind($resource);

        return new SocksStream($resource, 6, false);
    }

    public function testChunkedEncoding()
    {
        $data = '4' . "\r\n";
        $data.= 'Wiki' . "\r\n";
        $data.= '5' . "\r\n";
        $data.= 'pedia' . "\r\n";
        $data.= 'e' . "\r\n";
        $data.= ' in' . "\r\n\r\n" . 'chunks.' . "\r\n";
        $data.= '0' . "\r\n";
        $data.= "\r\n";

        $resource = fopen('php://memory', 'r+');
        fwrite($resource, $data);
        rewind($resource);

        $stream  = new SocksStream($resource, 0, true);
        $content = '';

        $this->assertTrue($stream->isChunkEncoded());

        do {
            $size    = $stream->getChunkSize();
            $content.= $stream->getContents($size);

            $stream->readLine();
        } while ($size > 0);

        $this->assertEquals('Wikipedia in' . "\r\n\r\n" . 'chunks.', $content);

        $stream->seek(0);

        $this->assertEquals('Wikipedia in' . "\r\n\r\n" . 'chunks.', (string) $stream);
    }
}
