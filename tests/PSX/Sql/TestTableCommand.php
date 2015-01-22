<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
 *
 * This file is part of psx. psx is free software: you can
 * redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or any later version.
 *
 * psx is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with psx. If not, see <http://www.gnu.org/licenses/>.
 */

namespace PSX\Sql;

use PSX\Sql\TableAbstract;
use PSX\Sql\TableInterface;

/**
 * TestTableCommand
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
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
			'col_array' => self::TYPE_ARRAY,
			'col_object' => self::TYPE_OBJECT,
		);
	}
}
