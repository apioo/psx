<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2014 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Sql\Table;

/**
 * SelectInterface
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
interface SelectInterface
{
	/**
	 * Sets the selected columns
	 *
	 * @param array $columns
	 */
	public function select(array $columns);

	/**
	 * Joins another table on this selection
	 *
	 * @param integer $type
	 * @param PSX\Sql\TableInterface $table
	 * @param string $cardinality
	 * @param string $foreignKey
	 * @return PSX\Sql\SelectInterface
	 */
	public function join($type, $table, $cardinality = 'n:1', $foreignKey = null);

	/**
	 * Adds a condition to the select
	 *
	 * @param string $column
	 * @param string $operator
	 * @param string $value
	 * @param string $conjunction
	 * @return PSX\Sql\SelectInterface
	 */
	public function where($column, $operator, $value, $conjunction = 'AND');

	/**
	 * Groups the select by the given column
	 *
	 * @param string $column
	 * @return PSX\Sql\SelectInterface
	 */
	public function groupBy($column);

	/**
	 * Orders the result by the given column
	 *
	 * @param string $column
	 * @param integer $sort
	 * @return PSX\Sql\SelectInterface
	 */
	public function orderBy($column, $sort = 0x1);

	/**
	 * Limits the result
	 *
	 * @param integer $start
	 * @param integer $count
	 * @return PSX\Sql\SelectInterface
	 */
	public function limit($start, $count = null);
}

