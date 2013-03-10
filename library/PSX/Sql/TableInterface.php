<?php
/*
 *  $Id: TableInterface.php 640 2012-09-30 22:44:36Z k42b3.x@googlemail.com $
 *
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2012 Christoph Kappestein <k42b3.x@gmail.com>
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
 * PSX_Sql_TableInterface
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Sql
 * @version    $Revision: 640 $
 */
interface TableInterface
{
	const PRIMARY_KEY     = 0x10000000;
	const AUTO_INCREMENT  = 0x20000000;
	const IS_NULL         = 0x40000000;
	const UNSIGNED        = 0x80000000;

	// integer
	const TYPE_TINYINT    = 0x100000;
	const TYPE_SMALLINT   = 0x200000;
	const TYPE_MEDIUMINT  = 0x300000;
	const TYPE_INT        = 0x400000;
	const TYPE_BIGINT     = 0x500000;

	const TYPE_DECIMAL    = 0x600000;
	const TYPE_FLOAT      = 0x700000;
	const TYPE_DOUBLE     = 0x800000;
	const TYPE_REAL       = 0x900000;

	const TYPE_BIT        = 0xA00000;
	const TYPE_BOOLEAN    = 0xB00000;
	const TYPE_SERIAL     = 0xC00000;

	// date
	const TYPE_DATE       = 0xD00000;
	const TYPE_DATETIME   = 0xE00000;
	const TYPE_TIMESTAMP  = 0xF00000;
	const TYPE_TIME       = 0x1000000;
	const TYPE_YEAR       = 0x1100000;

	// string
	const TYPE_CHAR       = 0x1200000;
	const TYPE_VARCHAR    = 0x1300000;

	const TYPE_BINARY     = 0x1400000;
	const TYPE_VARBINARY  = 0x1500000;

	const TYPE_TINYTEXT   = 0x1600000;
	const TYPE_TEXT       = 0x1700000;
	const TYPE_MEDIUMTEXT = 0x1800000;
	const TYPE_LONGTEXT   = 0x1900000;

	const TYPE_TINYBLOB   = 0x1A00000;
	const TYPE_MEDIUMBLOB = 0x1B00000;
	const TYPE_BLOB       = 0x1C00000;
	const TYPE_LONGBLOB   = 0x1D00000;

	const TYPE_ENUM       = 0x1E00000;
	const TYPE_SET        = 0x1F00000;

	/**
	 * Returns the relation between the table and columns
	 *
	 * array(
	 * 	'column' => 'table'
	 * )
	 *
	 * @return array
	 */
	public function getConnections();

	/**
	 * Returns the name of the table
	 *
	 * @return string
	 */
	public function getName();

	/**
	 * Returns the columns of the table where the key is the name of the column
	 * and the value contains OR connected informations. I.e.:
	 *
	 * array(
	 *  'id'    => self::TYPE_INT | 10 | self::AUTO_INCREMENT | self::PRIMARY_KEY,
	 *  'title' => self::TYPE_VARCHAR | 256
	 * )
	 *
	 * For better understanding here an 32 bit integer representation of the
	 * example above:
	 *
	 *             UNAP     T                L
	 * array(      |||| |-------| |----------------------|
	 *  'id'    => 0011 0000 0100 0000 0000 0000 0000 1010
	 *  'title' => 0000 1100 0000 0000 0000 0001 0000 0000
	 * )
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
	 * Returns the underling sql object
	 *
	 * @return PSX_Sql
	 */
	public function getSql();

	/**
	 * Returns the name of the record wich should contain only alpha characters.
	 * Table names are typically seperated with _ (underscore). This method
	 * should return the last part of the table name i.e. "amun_system_request"
	 * should return "request"
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
	 * Returns the first column with a specific attribute
	 *
	 * @return string
	 */
	public function getFirstColumnWithAttr($searchAttr);

	/**
	 * Returns the first column from the type
	 *
	 * @return string
	 */
	public function getFirstColumnWithType($searchType);

	/**
	 * Returns an array containing all valid columns of the array $columns
	 *
	 * @return array
	 */
	public function getValidColumns(array $columns);

	/**
	 * Starts a new complex selection on this table
	 *
	 * @return PSX_Sql_Table_SelectInterface
	 */
	public function select(array $columns = array(), $prefix = null);

	/**
	 * Returns the last selection wich was created by the method select()
	 *
	 * @return PSX_Sql_Table_SelectInterface
	 */
	public function getLastSelect();

	/**
	 * Returns a new record for the table. The class name is build from this
	 * class without the "_Table" suffix. If an $id is provided the record
	 * contains all fields from the table. If the record does not exist an
	 * exception is thrown
	 *
	 * @param integer $id
	 * @return PSX_Data_RecordInterface
	 */
	public function getRecord($id = null);

	/**
	 * Simple method to get an associative array from the table containing the 
	 * columns defined in $fields and matching the the $condition. Use the 
	 * select method if you want join between tables
	 *
	 * @param array $fields
	 * @param PSX_Sql_Condition $condition
	 * @param string $sortBy
	 * @param integer $sortOrder
	 * @param integer $startIndex
	 * @param integer $count
	 * @return array
	 */
	public function getAll(array $fields, Condition $condition = null, $sortBy = null, $sortOrder = 0, $startIndex = null, $count = 32);

	/**
	 * Method wich returns an single row as associatve array containing all 
	 * $fields
	 *
	 * @param array $fields
	 * @param PSX_Sql_Condition $condition
	 * @param string $sortBy
	 * @param integer $sortOrder
	 * @return array
	 */
	public function getRow(array $fields, Condition $condition = null, $sortBy = null, $sortOrder = 0);

	/**
	 * Returns the values of one column as array
	 *
	 * @param string $field
	 * @param PSX_Sql_Condition $condition
	 * @param string $sortBy
	 * @param integer $sortOrder
	 * @param integer $startIndex
	 * @param integer $count
	 * @return array
	 */
	public function getCol($field, Condition $condition = null, $sortBy = null, $sortOrder = 0, $startIndex = null, $count = 32);

	/**
	 * Returns the first value from the $field
	 *
	 * @param string $field
	 * @param PSX_Sql_Condition $condition
	 * @param string $sortBy
	 * @param integer $sortOrder
	 * @return string
	 */
	public function getField($field, Condition $condition = null, $sortBy = null, $sortOrder = 0);

	/**
	 * Returns the number of rows according to the $condition or the complete
	 * count of the table if $condition is null
	 *
	 * @param PSX_Sql_Condition $condition
	 * @return integer
	 */
	public function count(Condition $condition = null);

	/**
	 * Inserts an row into the table. It validates all columns  according to the 
	 * table definition
	 *
	 * @param array|PSX_Data_RecordInterface $params
	 * @param integer $modifier
	 * @return integer
	 */
	public function insert($params, $modifier = 0);

	/**
	 * Updates an row on the table. If no condition is specified the method 
	 * looks for the primary key in the $params and uses this value to update 
	 * the row
	 *
	 * @param array|PSX_Data_RecordInterface $params
	 * @param PSX_Sql_Condition $condition
	 * @param integer $modifier
	 * @return integer
	 */
	public function update($params, Condition $condition = null, $modifier = 0);

	/**
	 * Replaces an row on the table. If no condition is specified the method 
	 * looks for the primary key in the $params and uses this value to replace 
	 * the row
	 *
	 * @param array|PSX_Data_RecordInterface $params
	 * @param integer $modifier
	 * @return integer
	 */
	public function replace($params, $modifier = 0);

	/**
	 * Deletes an row on the table. If $params is an array or record the 
	 * primary key field is used to determine the row wich should be deleted. 
	 * If $params is an condition all rows according to the condition will be
	 * deleted
	 *
	 * @param array|PSX_Data_RecordInterface|PSX_Sql_Condition $params
	 * @param PSX_Sql_Condition $condition
	 * @param integer $modifier
	 * @return integer
	 */
	public function delete($params, $modifier = 0);
}

