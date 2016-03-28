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

namespace PSX\Framework\Console\Reader;

use InvalidArgumentException;
use PSX\Framework\Console\ReaderInterface;

/**
 * Helper class which reads the stdin until an EOT character occurs or EOF is
 * reached
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Stdin implements ReaderInterface
{
    protected $handle;

    /**
     * Takes the stream on which the reader operates. If no stream is provided
     * STDIN is used
     *
     * @param resource $handle
     */
    public function __construct($handle = null)
    {
        if ($handle === null) {
            $this->handle = STDIN;
        } elseif (is_resource($handle)) {
            $this->handle = $handle;
        } else {
            throw new InvalidArgumentException('Must be an resource');
        }
    }

    public function read()
    {
        $body = '';

        while (!feof($this->handle)) {
            $line = fgets($this->handle);
            $pos  = strpos($line, chr(4));

            if ($pos !== false) {
                $body.= substr($line, 0, $pos);
                break;
            }

            $body.= $line;
        }

        return $body;
    }
}
