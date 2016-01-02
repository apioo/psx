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

namespace PSX\Sql\Table;

use UnexpectedValueException;

/**
 * Some implementations needs to know the name of specific columns i.e. the sql
 * cache handler needs to know the data or date column. This class contains
 * such informations
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ColumnAllocation
{
    protected $columns;

    public function __construct(array $columns = array())
    {
        $this->columns = $columns;
    }

    public function set($key, $name)
    {
        $this->columns[$key] = $name;
    }

    public function get($key)
    {
        $name = isset($this->columns[$key]) ? $this->columns[$key] : null;

        if (!empty($name)) {
            return $name;
        } else {
            throw new UnexpectedValueException('Unknown column name given');
        }
    }
}
