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

namespace PSX\Data;

use ArrayIterator;

/**
 * RecordAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class RecordAbstract implements RecordInterface
{
    public function offsetSet($offset, $value)
    {
        $this->setProperty($offset, $value);
    }

    public function offsetExists($offset)
    {
        return $this->hasProperty($offset);
    }

    public function offsetUnset($offset)
    {
        $this->removeProperty($offset);
    }

    public function offsetGet($offset)
    {
        return $this->getProperty($offset);
    }

    public function getIterator()
    {
        return new ArrayIterator($this->getProperties());
    }

    public function serialize()
    {
        return serialize([$this->getDisplayName(), $this->getProperties()]);
    }

    public function unserialize($data)
    {
        list($displayName, $properties) = unserialize($data);

        $this->setDisplayName($displayName);
        $this->setProperties($properties);
    }

    public function jsonSerialize()
    {
        return $this->getProperties();
    }

    public function __set($name, $value)
    {
        $this->setProperty($name, $value);
    }

    public function __get($name)
    {
        return $this->getProperty($name);
    }

    public function __isset($name)
    {
        return $this->hasProperty($name);
    }

    public function __unset($name)
    {
        $this->removeProperty($name);
    }
}
