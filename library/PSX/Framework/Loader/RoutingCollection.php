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

namespace PSX\Framework\Loader;

use Countable;
use Iterator;

/**
 * RoutingCollection
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class RoutingCollection implements Iterator, Countable
{
    const ROUTING_METHODS = 0;
    const ROUTING_PATH    = 1;
    const ROUTING_SOURCE  = 2;

    protected $routings;

    private $_pointer;

    public function __construct(array $routings = array())
    {
        $this->routings = $routings;
    }

    public function add(array $methods, $path, $source)
    {
        $this->routings[] = array($methods, $path, $source);
    }

    public function getAll()
    {
        return $this->routings;
    }

    public function current()
    {
        return current($this->routings);
    }

    public function key()
    {
        return key($this->routings);
    }

    public function next()
    {
        return $this->_pointer = next($this->routings);
    }

    public function rewind()
    {
        $this->_pointer = reset($this->routings);
    }

    public function valid()
    {
        return $this->_pointer;
    }

    public function count()
    {
        return count($this->routings);
    }
}
