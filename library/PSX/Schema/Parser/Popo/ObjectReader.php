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

namespace PSX\Schema\Parser\Popo;

use Doctrine\Common\Annotations\Reader;
use ReflectionClass;

/**
 * The exporter takes an arbitrary object and returns a record
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ObjectReader
{
    /**
     * Returns all available properties of an object
     *
     * @param \ReflectionClass $class
     * @return array
     */
    public static function getProperties(Reader $reader, ReflectionClass $class)
    {
        $props  = $class->getProperties();
        $result = [];

        foreach ($props as $property) {
            // skip statics
            if ($property->isStatic()) {
                continue;
            }

            // check whether we have an exclude annotation
            $exclude = $reader->getPropertyAnnotation($property, '\\PSX\\Schema\\Parser\\Popo\\Annotation\\Exclude');
            if ($exclude !== null) {
                continue;
            }

            // get the property name
            $key  = $reader->getPropertyAnnotation($property, '\\PSX\\Schema\\Parser\\Popo\\Annotation\\Key');
            $name = null;

            if ($key !== null) {
                $name = $key->getKey();
            }

            if (empty($name)) {
                $name = $property->getName();
            }

            $result[$name] = $property;
        }

        return $result;
    }
}
