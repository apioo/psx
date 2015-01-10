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

/**
 * Represents a class which describes an table
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
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
	const TYPE_INT        = 0x400000;
	const TYPE_BIGINT     = 0x500000;
	const TYPE_BOOLEAN    = 0xB00000;

	// float
	const TYPE_DECIMAL    = 0x600000;
	const TYPE_FLOAT      = 0x700000;

	// date
	const TYPE_DATE       = 0xD00000;
	const TYPE_DATETIME   = 0xE00000;
	const TYPE_TIME       = 0x1000000;

	// string
	const TYPE_VARCHAR    = 0x1300000;
	const TYPE_TEXT       = 0x1700000;
	const TYPE_BLOB       = 0x1C00000;

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
	 * For better understanding here an 32 bit integer representation of the
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
	 * Returns an pretty representation of the table name. If the table is 
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
	 * @return boolean
	 */
	public function hasColumn($column);
}
