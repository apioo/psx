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

namespace PSX\Data;

/**
 * CollectionAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class CollectionAbstract extends RecordAbstract implements CollectionInterface
{
    protected $collection;

    private $_pointer;

    public function __construct(array $collection = array())
    {
        $this->collection = $collection;
    }

    public function add(RecordInterface $record)
    {
        $this->collection[] = $record;
    }

    public function clear()
    {
        $this->collection = array();
        $this->rewind();
    }

    public function isEmpty()
    {
        return $this->count() == 0;
    }

    public function get($key)
    {
        return isset($this->collection[$key]) ? $this->collection[$key] : null;
    }

    public function set($key, RecordInterface $record)
    {
        $this->collection[$key] = $record;
    }

    public function toArray()
    {
        return $this->collection;
    }

    // Iterator
    public function current()
    {
        return current($this->collection);
    }

    public function key()
    {
        return key($this->collection);
    }

    public function next()
    {
        return $this->_pointer = next($this->collection);
    }

    public function rewind()
    {
        $this->_pointer = reset($this->collection);
    }

    public function valid()
    {
        return $this->_pointer;
    }

    // Countable
    public function count()
    {
        return count($this->collection);
    }
}
