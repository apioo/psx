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

use ArrayAccess;
use BadMethodCallException;

/**
 * Record
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Record extends RecordAbstract implements ArrayAccess
{
    protected $name;
    protected $fields;

    public function __construct($name = 'record', array $fields = array())
    {
        $this->name   = $name;
        $this->fields = $fields;
    }

    public function getRecordInfo()
    {
        return new RecordInfo($this->name, $this->fields);
    }

    public function getProperty($name)
    {
        return isset($this->fields[$name]) ? $this->fields[$name] : null;
    }

    public function setProperty($name, $value)
    {
        $this->fields[$name] = $value;
    }

    public function removeProperty($name)
    {
        if (isset($this->fields[$name])) {
            unset($this->fields[$name]);
        }
    }

    public function hasProperty($name)
    {
        return array_key_exists($name, $this->fields);
    }

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

    public function __call($method, array $args)
    {
        $type = substr($method, 0, 3);
        $key  = lcfirst(substr($method, 3));

        if ($type == 'set') {
            $this->setProperty($key, current($args));
        } elseif ($type == 'get') {
            return $this->getProperty($key);
        } else {
            throw new BadMethodCallException('Invalid method call ' . $method);
        }
    }
}
