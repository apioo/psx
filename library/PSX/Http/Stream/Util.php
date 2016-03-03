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

use Psr\Http\Message\StreamInterface;

/**
 * Provides util methods to handle with streams
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Util
{
    /**
     * Converts a stream into a string and returns the result. The position of
     * the pointer will not change if the stream is seekable. Note this copies
     * the complete content of the stream into the memory
     *
     * @param \Psr\Http\Message\StreamInterface $stream
     * @return string
     */
    public static function toString(StreamInterface $stream)
    {
        if (!$stream->isReadable()) {
            return '';
        }

        if ($stream->isSeekable()) {
            $pos = $stream->tell();

            if ($pos > 0) {
                $stream->seek(0);
            }

            $content = $stream->getContents();

            $stream->seek($pos);
        } else {
            $content = $stream->getContents();
        }

        return $content;
    }
}
