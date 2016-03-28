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

namespace PSX\Validate\Filter;

use PSX\Validate\FilterAbstract;

/**
 * Filter wich returns either an datetime object or if a format is specified
 * the date in the given format as string
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://amun.phpsx.org
 */
class DateTime extends FilterAbstract
{
    protected $format;

    public function __construct($format = null)
    {
        $this->format = $format;
    }

    public function apply($value)
    {
        try {
            $date = $value instanceof \DateTime ? $value : new \DateTime((string) $value);

            if ($this->format === null) {
                return $date;
            } else {
                return $date->format($this->format);
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getErrorMessage()
    {
        return '%s has not a valid date format';
    }
}
