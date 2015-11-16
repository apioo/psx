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

namespace PSX\Json;

use InvalidArgumentException;

/**
 * Class to apply patch operations on a json object. Based on the json-patch-php
 * library but works with stdClass instead of associative arrays
 * 
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 * @see     https://github.com/mikemccabe/json-patch-php
 * @see     https://tools.ietf.org/html/rfc6902
 */
class Patch
{
    protected $operations;

    public function __construct(array $operations)
    {
        $this->operations = $operations;
    }

    public function patch($data)
    {
        foreach ($this->operations as $operation) {
            $op    = isset($operation->op)    ? $operation->op    : null;
            $path  = isset($operation->path)  ? $operation->path  : null;
            $value = isset($operation->value) ? $operation->value : null;
            $from  = isset($operation->from)  ? $operation->from  : null;

            switch ($op) {
                case 'add':
                case 'append':
                case 'replace':
                    $pointer = new Pointer($path);
                    $data    = $this->doOperation($data, $pointer->getParts(), $op, $path, $value);
                    break;

                case 'remove':
                    $pointer = new Pointer($path);
                    $data    = $this->doOperation($data, $pointer->getParts(), $op, $path, null);
                    break;

                case 'test':
                    $this->doTest($data, new Pointer($path), $path, $value);
                    break;

                case 'copy':
                    $pointer = new Pointer($from);
                    $value   = $pointer->evaluate($data);

                    $pointer = new Pointer($path);
                    $data    = $this->doOperation($data, $pointer->getParts(), 'add', $path, $value);
                    break;

                case 'move':
                    $pointer = new Pointer($from);
                    $value   = $pointer->evaluate($data);
                    $data    = $this->doOperation($data, $pointer->getParts(), 'remove', $path, null);
                    
                    $pointer = new Pointer($path);
                    $data    = $this->doOperation($data, $pointer->getParts(), 'add', $path, $value);
                    break;
            }
        }

        return $data;
    }

    protected function doOperation($data, array $parts, $op, $path, $value)
    {
        if (count($parts) == 0) {
            if ($op == 'add' || $op == 'replace') {
                return $value;
            }
        }

        $part = array_shift($parts);

        if (count($parts) > 0) {
            if (is_array($data)) {
                if (isset($data[$part])) {
                    $data[$part] = $this->doOperation($data[$part], $parts, $op, $path, $value);
                }
            } elseif ($data instanceof \stdClass) {
                if (isset($data->$part)) {
                    $data->$part = $this->doOperation($data->$part, $parts, $op, $path, $value);
                }
            } else {
                throw new InvalidArgumentException('Invalid path ' . $path);
            }

            return $data;
        }

        if (is_array($data)) {
            if ($part == '-' || preg_match('/^(0|[1-9][0-9]*)$/', $part)) {
                if ($op == 'add' || $op == 'append') {
                    $index = ($part == '-') ? count($data) : $part;
                    if ($op == 'append') {
                        array_splice($data, $index, 0, $value);
                    } else {
                        array_splice($data, $index, 0, array($value));
                    }
                } elseif ($op == 'replace') {
                    array_splice($data, $part, 1, array($value));
                } elseif ($op == 'remove') {
                    array_splice($data, $part, 1);
                }
            }
        } elseif ($data instanceof \stdClass) {
            if ($part !== '') {
                if ($op == 'add' || $op == 'append') {
                    $data->$part = $value;
                } elseif ($op == 'replace') {
                    if (property_exists($data, $part)) {
                        $data->$part = $value;
                    }
                } elseif ($op == 'remove') {
                    if (property_exists($data, $part)) {
                        unset($data->$part);
                    }
                }
            }
        }

        return $data;
    }

    protected function doTest($data, Pointer $pointer, $path, $value)
    {
        $actual = $pointer->evaluate($data);

        if (!Comparator::compare($value, $actual)) {
            throw new InvalidArgumentException('Test value is different');
        }
    }
}
