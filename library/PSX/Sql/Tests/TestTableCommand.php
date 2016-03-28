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

namespace PSX\Sql\Tests;

use PSX\Sql\TableAbstract;

/**
 * TestTableCommand
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class TestTableCommand extends TableAbstract
{
    public function getName()
    {
        return 'psx_table_command_test';
    }

    public function getColumns()
    {
        return array(
            'id' => self::TYPE_INT | self::AUTO_INCREMENT | self::PRIMARY_KEY,
            'col_bigint' => self::TYPE_BIGINT,
            'col_blob' => self::TYPE_BLOB,
            'col_boolean' => self::TYPE_BOOLEAN,
            'col_datetime' => self::TYPE_DATETIME,
            'col_datetimetz' => self::TYPE_DATETIME,
            'col_date' => self::TYPE_DATE,
            'col_decimal' => self::TYPE_DECIMAL,
            'col_float' => self::TYPE_FLOAT,
            'col_integer' => self::TYPE_INT,
            'col_smallint' => self::TYPE_SMALLINT,
            'col_text' => self::TYPE_TEXT,
            'col_time' => self::TYPE_TIME,
            'col_string' => self::TYPE_VARCHAR,
            'col_array' => self::TYPE_ARRAY | self::IS_NULL,
            'col_object' => self::TYPE_OBJECT | self::IS_NULL,
        );
    }
}
