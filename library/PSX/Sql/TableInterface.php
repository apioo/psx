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
 * Represents a class which describes a table
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
interface TableInterface extends TableQueryInterface, TableManipulationInterface
{
    const PRIMARY_KEY     = 0x10000000;
    const AUTO_INCREMENT  = 0x20000000;
    const IS_NULL         = 0x40000000;
    const UNSIGNED        = 0x80000000;

    // integer
    const TYPE_SMALLINT   = 0x100000;
    const TYPE_INT        = 0x200000;
    const TYPE_BIGINT     = 0x300000;
    const TYPE_BOOLEAN    = 0x400000;

    // float
    const TYPE_DECIMAL    = 0x500000;
    const TYPE_FLOAT      = 0x600000;

    // date
    const TYPE_DATE       = 0x700000;
    const TYPE_DATETIME   = 0x800000;
    const TYPE_TIME       = 0x900000;

    // string
    const TYPE_VARCHAR    = 0xA00000;
    const TYPE_TEXT       = 0xB00000;
    const TYPE_BLOB       = 0xC00000;

    // formats
    const TYPE_ARRAY      = 0xD00000;
    const TYPE_OBJECT     = 0xE00000;

    /**
     * Returns the name of the table
     *
     * @return string
     */
    public function getName();

    /**
     * Returns the columns of the table where the key is the name of the column
     * and the value contains OR connected informations. I.e.:
     * <code>
     * array(
     *  'id'    => self::TYPE_INT | 10 | self::PRIMARY_KEY,
     *  'title' => self::TYPE_VARCHAR | 256
     * )
     * </code>
     *
     * For better understanding here a 32 bit integer representation of the
     * example above:
     * <code>
     *             UNAP     T                L
     * array(      |||| |-------| |----------------------|
     *  'id'    => 0011 0000 0100 0000 0000 0000 0000 1010
     *  'title' => 0000 1100 0000 0000 0000 0001 0000 0000
     * )
     * </code>
     *
     * L: Length of the column max value is 0xFFFFF (decimal: 1048575)
     * T: Type of the column one of TYPE_* constant
     * P: Whether its a primary key
     * A: Whether its an auto increment value
     * N: Whether the column can be NULL
     * U: Whether the value is unsigned
     *
     * @return array
     */
    public function getColumns();

    /**
     * Returns a pretty representation of the table name. If the table is
     * seperated with underscores the last part could be the display name i.e.
     * "foo_bar" should return "bar"
     *
     * @return string
     */
    public function getDisplayName();

    /**
     * Returns the name of the primary key column
     *
     * @return string
     */
    public function getPrimaryKey();

    /**
     * Returns whether the table has the $column
     *
     * @param string $column
     * @return boolean
     */
    public function hasColumn($column);
}
