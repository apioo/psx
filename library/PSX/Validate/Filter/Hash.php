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

use InvalidArgumentException;
use PSX\Validate\FilterAbstract;

/**
 * Hash
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Hash extends FilterAbstract
{
    protected $algo;

    public function __construct($algo = 'sha1')
    {
        if (in_array($algo, hash_algos())) {
            $this->algo = $algo;
        } else {
            throw new InvalidArgumentException('Unsupported hash algorithm');
        }
    }

    /**
     * Returns an representation of $value depending on the selected algorithm
     *
     * @param mixed $value
     * @return boolean
     */
    public function apply($value)
    {
        return hash($this->algo, (string) $value);
    }
}
