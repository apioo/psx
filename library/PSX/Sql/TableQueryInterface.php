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

use PSX\Sql\Condition;

/**
 * TableQueryInterface
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
interface TableQueryInterface
{
	/**
	 * Returns an array of records matching the conditions
	 *
	 * @param integer $startIndex
	 * @param integer $count
	 * @param integer $sortBy
	 * @param integer $sortOrder
	 * @param PSX\Sql\Condition $condition
	 * @return array
	 */
	public function getAll($startIndex = null, $count = null, $sortBy = null, $sortOrder = null, Condition $condition = null);

	/**
	 * Returns an array of records matching the condition
	 *
	 * @param PSX\Sql\Condition $condition
	 * @return array
	 */
	public function getBy(Condition $condition);

	/**
	 * Returns an record by the condition
	 *
	 * @param PSX\Sql\Condition $condition
	 * @return array
	 */
	public function getOneBy(Condition $condition);

	/**
	 * Returns an record by the primary key
	 *
	 * @return array
	 */
	public function get($id);

	/**
	 * Returns all available fields of this handler
	 *
	 * @return array
	 */
	public function getSupportedFields();

	/**
	 * Returns the number of rows matching the given condition in the resultset
	 *
	 * @return integer
	 */
	public function getCount(Condition $condition = null);
}
