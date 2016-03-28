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

namespace PSX\Json;

use InvalidArgumentException;
use PSX\Data\RecordInterface;

/**
 * Pointer
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 * @see     https://tools.ietf.org/html/rfc6901
 */
class Pointer
{
    protected $path;
    protected $parts;

    public function __construct($path)
    {
        $this->path  = $path;
        $this->parts = $this->parsePointer($path);
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getParts()
    {
        return $this->parts;
    }

    public function evaluate($data)
    {
        foreach ($this->parts as $part) {
            if (is_array($data)) {
                $data = isset($data[$part]) ? $data[$part] : null;
            } elseif ($data instanceof \stdClass) {
                $data = isset($data->$part) ? $data->$part : null;
            } elseif ($data instanceof RecordInterface) {
                $data = $data->getProperty($part);
            } else {
                $data = null;
            }

            if ($data === null) {
                break;
            }
        }

        return $data;
    }

    private function parsePointer($path)
    {
        if (empty($path)) {
            return [];
        }

        $parts = explode('/', $path);
        if (array_shift($parts) !== '') {
            throw new InvalidArgumentException('Pointer must start with a /');
        }

        return array_map(function ($value) {
            return str_replace(['~1', '~0'], ['/', '~'], $value);
        }, $parts);
    }
}
