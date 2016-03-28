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

namespace PSX\Model\Common;

use IteratorAggregate;
use Countable;
use ArrayIterator;

/**
 * Collection
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Collection implements IteratorAggregate, Countable
{
    protected $collection;

    public function __construct(array $collection = array())
    {
        $this->collection = $collection;
    }

    public function add($object)
    {
        $this->collection[] = $object;
    }

    public function clear()
    {
        $this->collection = array();
    }

    public function contains($object)
    {
        foreach ($this->collection as $value) {
            if ($value === $object) {
                return true;
            }
        }
        return false;
    }

    public function get($index)
    {
        return isset($this->collection[$index]) ? $this->collection[$index] : null;
    }

    public function indexOf($object)
    {
        foreach ($this->collection as $index => $value) {
            if ($value === $object) {
                return $index;
            }
        }
        return -1;
    }

    public function isEmpty()
    {
        return empty($this->collection);
    }

    public function remove($index)
    {
        if (isset($this->collection[$index])) {
            unset($this->collection[$index]);
        }
    }

    public function set($index, $object)
    {
        $this->collection[$index] = $object;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->collection);
    }

    public function count()
    {
        return count($this->collection);
    }
}
