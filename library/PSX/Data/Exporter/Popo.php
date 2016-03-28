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

namespace PSX\Data\Exporter;

use Doctrine\Common\Annotations\Reader;
use InvalidArgumentException;
use PSX\Data\ExporterInterface;
use PSX\Data\GraphTraverser;
use PSX\Data\Record;
use PSX\Data\RecordInterface;
use PSX\Schema\Parser\Popo\ObjectReader;
use ReflectionObject;

/**
 * Exports an arbitrary object to a record
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Popo implements ExporterInterface
{
    /**
     * @var \Doctrine\Common\Annotations\Reader
     */
    protected $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    public function export($data)
    {
        if (GraphTraverser::isObject($data)) {
            return $this->exportMap($data);
        } elseif (is_object($data)) {
            return $this->exportObject($data);
        } else {
            throw new InvalidArgumentException('Data must be an object');
        }
    }

    protected function exportObject($object)
    {
        $class  = new ReflectionObject($object);
        $result = new Record(lcfirst($class->getShortName()));
        $props  = ObjectReader::getProperties($this->reader, $class);

        foreach ($props as $name => $property) {
            $getters = [
                'get' . ucfirst($property->getName()),
                'is' . ucfirst($property->getName())
            ];

            foreach ($getters as $getter) {
                if ($class->hasMethod($getter)) {
                    $value = $class->getMethod($getter)->invoke($object);
                    $value = $this->exportValue($value);

                    if ($value !== null) {
                        $result->setProperty($name, $value);
                    }
                    break;
                }
            }
        }

        return $result;
    }

    protected function exportMap($object)
    {
        if ($object instanceof RecordInterface) {
            $result = $object;
            foreach ($object as $key => $value) {
                $value = $this->exportValue($value);
                if ($value !== null) {
                    $result->setProperty($key, $value);
                } else {
                    $result->removeProperty($key);
                }
            }
        } else {
            $result = new Record('record');
            foreach ($object as $key => $value) {
                $value = $this->exportValue($value);
                if ($value !== null) {
                    $result->setProperty($key, $value);
                }
            }
        }

        return $result;
    }

    protected function exportArray($array)
    {
        $result = [];
        foreach ($array as $value) {
            $value = $this->exportValue($value);
            if ($value !== null) {
                $result[] = $value;
            }
        }
        return $result;
    }

    protected function exportValue($value)
    {
        if ($value === null) {
            return null;
        } elseif (is_scalar($value) || $value instanceof \DateTime || $value instanceof \DateInterval) {
            return $value;
        } elseif (GraphTraverser::isObject($value)) {
            return $this->exportMap($value);
        } elseif (GraphTraverser::isArray($value)) {
            return $this->exportArray($value);
        } elseif (is_object($value)) {
            return $this->exportObject(GraphTraverser::reveal($value));
        } else {
            throw new InvalidArgumentException('Invalid data ' . gettype($value) . ' in model object');
        }
    }
}
