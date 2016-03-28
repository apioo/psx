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

/**
 * Record
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Record extends RecordAbstract
{
    protected $_displayName;
    protected $_properties;

    public function __construct($displayName = 'record', array $properties = array())
    {
        $this->_displayName = $displayName;
        $this->_properties  = $properties;
    }

    public function getDisplayName()
    {
        return $this->_displayName;
    }

    public function setDisplayName($displayName)
    {
        $this->_displayName = $displayName;
    }

    public function getProperties()
    {
        return array_filter($this->_properties, function($value){
            return $value !== null;
        });
    }

    public function setProperties(array $properties)
    {
        $this->_properties = $properties;
    }

    public function getProperty($name)
    {
        return isset($this->_properties[$name]) ? $this->_properties[$name] : null;
    }

    public function setProperty($name, $value)
    {
        $this->_properties[$name] = $value;
    }

    public function removeProperty($name)
    {
        if (isset($this->_properties[$name])) {
            unset($this->_properties[$name]);
        }
    }

    public function hasProperty($name)
    {
        return array_key_exists($name, $this->_properties);
    }

    /**
     * @param array $data
     * @param string $name
     * @return \PSX\Data\RecordInterface
     */
    public static function fromArray(array $data, $name = null)
    {
        return new static($name === null ? 'record' : $name, $data);
    }

    /**
     * @param \stdClass $data
     * @param string $name
     * @return \PSX\Data\RecordInterface
     */
    public static function fromStdClass(\stdClass $data, $name = null)
    {
        return new static($name === null ? 'record' : $name, (array) $data);
    }

    /**
     * @param mixed $data
     * @param string $name
     * @return \PSX\Data\RecordInterface
     */
    public static function from($data, $name = null)
    {
        if ($data instanceof RecordInterface) {
            if ($name !== null) {
                $data->setDisplayName($name);
            }
            return $data;
        } elseif ($data instanceof \stdClass) {
            return self::fromStdClass($data, $name);
        } elseif (is_array($data)) {
            return self::fromArray($data, $name);
        } else {
            throw new \InvalidArgumentException('Can create record only from stdClass or array');
        }
    }

    /**
     * Merges data from two records into a new record. The right record
     * overwrites values from the left record
     *
     * @param \PSX\Data\RecordInterface $left
     * @param \PSX\Data\RecordInterface $right
     * @return \PSX\Data\RecordInterface
     */
    public static function merge(RecordInterface $left, RecordInterface $right)
    {
        return Record::fromArray(array_merge($left->getProperties(), $right->getProperties()), $right->getDisplayName());
    }
}
