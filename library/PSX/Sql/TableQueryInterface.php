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

namespace PSX\Sql;

/**
 * TableQueryInterface
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
interface TableQueryInterface
{
    /**
     * Returns an array of records matching the conditions
     *
     * @param integer $startIndex
     * @param integer $count
     * @param string $sortBy
     * @param integer $sortOrder
     * @param \PSX\Sql\Condition $condition
     * @return array
     */
    public function getAll($startIndex = null, $count = null, $sortBy = null, $sortOrder = null, Condition $condition = null, Fields $fields = null);

    /**
     * Returns an array of records matching the condition
     *
     * @param \PSX\Sql\Condition $condition
     * @return array
     */
    public function getBy(Condition $condition, Fields $fields = null);

    /**
     * Returns an record by the condition
     *
     * @param \PSX\Sql\Condition $condition
     * @return \PSX\Data\RecordInterface
     */
    public function getOneBy(Condition $condition, Fields $fields = null);

    /**
     * Returns an record by the primary key
     *
     * @param string $id
     * @return \PSX\Data\RecordInterface
     */
    public function get($id, Fields $fields = null);

    /**
     * Returns all available fields of this handler
     *
     * @return array
     */
    public function getSupportedFields();

    /**
     * Returns the number of rows matching the given condition in the resultset
     *
     * @param \PSX\Sql\Condition $condition
     * @return integer
     */
    public function getCount(Condition $condition = null);
}
