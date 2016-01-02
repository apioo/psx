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

namespace PSX\Data\Record;

use InvalidArgumentException;
use PSX\Data\Record\Mapper\Rule;
use PSX\Data\RecordInterface;

/**
 * Mapper
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Mapper
{
    /**
     * Method wich can map all fields of an record to an arbitary class by
     * calling the fitting setter methods if available
     *
     * @param \PSX\Data\RecordInterface $source
     * @param object $destination
     * @param array $rules
     */
    public static function map(RecordInterface $source, $destination, array $rules)
    {
        if (!is_object($destination)) {
            throw new InvalidArgumentException('Destination must be an object');
        }

        $data = $source->getRecordInfo()->getData();

        foreach ($data as $key => $value) {
            // convert to camelcase if underscore is in name
            if (strpos($key, '_') !== false) {
                $key = implode('', array_map('ucfirst', explode('_', $key)));
            }

            $method = null;
            if (isset($rules[$key])) {
                if (is_string($rules[$key])) {
                    $method = 'set' . ucfirst($rules[$key]);
                } elseif ($rules[$key] instanceof Rule) {
                    $method = 'set' . ucfirst($rules[$key]->getName());
                    $value  = $rules[$key]->getValue($value, $data);
                }
            } else {
                $method = 'set' . ucfirst($key);
            }

            if (is_callable(array($destination, $method))) {
                $destination->$method($value);
            }
        }
    }
}
