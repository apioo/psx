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

namespace PSX\Sql;

/**
 * Connection
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
interface Connection
{
	/**
	 * Main method for data selection. It returns either an associative array
	 * with the data or false. It uses prepared statments so you can write
	 * questionmarks in your query and provide the values in the $params array.
	 * If the $class parameter is not null instances of the specific class will
	 * be returned in the array
	 *
	 * @param string $sql
	 * @param array $params
	 * @param string $class
	 * @param array $args
	 * @return array
	 */
	public function assoc($sql, array $params = array(), $class = 'stdClass', array $args = null);

	/**
	 * Executes a query as prepared statment without returning any kind of 
	 * result 
	 *
	 * @param string $sql
	 * @param array $params
	 * @return boolean
	 */
	public function execute($sql, array $params = array());

	/**
	 * Returns the result of the query as an array where each row is an
	 * associative array where array([column] => [value])
	 *
	 * @param string $sql
	 * @param array $params
	 * @param integer $mode
	 * @param string $class
	 * @param array $args
	 * @return array
	 */
	public function getAll($sql, array $params = array(), $mode = 0, $class = 'stdClass', array $args = null);

	/**
	 * Returns a single row as associative array where
	 * array([column] => [value])
	 *
	 * @param string $sql
	 * @param array $params
	 * @param integer $mode
	 * @param string $class
	 * @param array $args
	 * @return array
	 */
	public function getRow($sql, array $params = array(), $mode = 0, $class = 'stdClass', array $args = null);

	/**
	 * Returns all values from one column as array
	 *
	 * @param string $sql
	 * @param array $params
	 * @return array
	 */
	public function getCol($sql, array $params = array());

	/**
	 * Returns the value of the first row and colum from the result
	 *
	 * @param string $sql
	 * @param array $params
	 * @return array
	 */
	public function getField($sql, array $params = array());

	/**
	 * Selects all $fields from the $table with the $condition. Calls depending
	 * on the $select value the getAll, getRow, getCol or getField method
	 *
	 * @param string $table
	 * @param array $fields
	 * @param PSX\Sql\Condition $condition
	 * @param integer $select
	 * @param string $sortBy
	 * @param integer $sortOrder
	 * @param integer $startIndex
	 * @param integer $count
	 * @return array
	 */
	public function select($table, array $fields, Condition $condition = null, $select = 0, $sortBy = null, $sortOrder = 0, $startIndex = null, $count = 32);

	/**
	 * Inserts into the $table the values $params
	 *
	 * @param string $table
	 * @param array $params
	 * @param integer $modifier
	 * @return boolean
	 */
	public function insert($table, array $params, $modifier = 0);

	/**
	 * Update the $params on the $table with the $condition
	 *
	 * @param string $table
	 * @param array $params
	 * @param PSX\Sql\Condition $condition
	 * @param integer $modifier
	 * @return integer
	 */
	public function update($table, array $params, Condition $condition = null, $modifier = 0);

	/**
	 * Replace the $params on the $table with the $condition
	 *
	 * @param string $table
	 * @param array $params
	 * @param integer $modifier
	 * @return integer
	 */
	public function replace($table, array $params, $modifier = 0);

	/**
	 * Deletes the record on the $table with the $condition
	 *
	 * @param string $table
	 * @param PSX\Sql\Condition $condition
	 * @param integer $modifier
	 * @return integer
	 */
	public function delete($table, Condition $condition = null, $modifier = 0);

	/**
	 * Returns the count of rows from the $table with the $condition
	 *
	 * @param string $table
	 * @param PSX\Sql\Condition $condition
	 * @return integer
	 */
	public function count($table, Condition $condition = null);
}
