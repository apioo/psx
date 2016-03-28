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
 * Regexp
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Regexp extends FilterAbstract
{
    private $regexp;

    public function __construct($regexp)
    {
        $this->regexp = $regexp;
    }

    /**
     * Returns true if the $exp valid for $value else false. This method is
     * called if you havent specify valid filter
     *
     * @param mixed $value
     * @return boolean
     */
    public function apply($value)
    {
        return preg_match($this->regexp, (string) $value) == 0 ? false : true;
    }

    public function getErrorMessage()
    {
        return '%s is not a valid value';
    }
}
