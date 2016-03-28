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
use PSX\Sql\Condition;
use PSX\Sql\TableInterface;

/**
 * Checks whether the value is available in the primary key column of the table
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class PrimaryKey extends FilterAbstract
{
    protected $table;

    public function __construct(TableInterface $table)
    {
        $this->table = $table;
    }

    /**
     * Returns true if value is in the table
     *
     * @param mixed $value
     * @return boolean
     */
    public function apply($value)
    {
        $pk = $this->table->getPrimaryKey();
        if (!empty($pk)) {
            return $this->table->getCount(new Condition([$pk, '=', $value])) > 0;
        }

        return false;
    }

    public function getErrorMessage()
    {
        return '%s does not exist in table';
    }
}
