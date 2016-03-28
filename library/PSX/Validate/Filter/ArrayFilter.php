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
use PSX\Validate\FilterInterface;

/**
 * ArrayFilter
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ArrayFilter extends FilterAbstract
{
    protected $filter;

    public function __construct(FilterInterface $filter)
    {
        $this->filter = $filter;
    }

    /**
     * Returns true if all values in $value apply to the filter. If the parent
     * filter has changed an value the modified array gets returned
     *
     * @param mixed $value
     * @return boolean
     */
    public function apply($value)
    {
        $data     = array();
        $modified = false;

        if (is_array($value)) {
            foreach ($value as $key => $val) {
                $result = $this->filter->apply($val);

                if ($result === false) {
                    return false;
                } elseif ($result === true) {
                    $data[$key] = $val;
                } else {
                    $modified = true;

                    $data[$key] = $result;
                }
            }
        } else {
            return false;
        }

        return $modified ? $data : true;
    }

    public function getErrorMessage()
    {
        return '%s contains invalid values';
    }
}
