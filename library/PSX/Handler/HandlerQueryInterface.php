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

namespace PSX\Handler;

use PSX\Sql\Condition;

/**
 * HandlerQueryInterface
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
interface HandlerQueryInterface
{
	/**
	 * Returns an array of records matching the conditions
	 *
	 * @param array $fields
	 * @param integer $startIndex
	 * @param integer $count
	 * @param integer $sortBy
	 * @param integer $sortOrder
	 * @param PSX\Sql\Condition $condition
	 * @return array<PSX\Data\RecordInterface>
	 */
	public function getAll(array $fields = null, $startIndex = null, $count = null, $sortBy = null, $sortOrder = null, Condition $condition = null);

	/**
	 * Returns an array of records matching the condition
	 *
	 * @param PSX\Sql\Condition $condition
	 * @param array $fields
	 * @return array<PSX\Data\RecordInterface>
	 */
	public function getBy(Condition $condition, array $fields = null);

	/**
	 * Returns an record by the condition
	 *
	 * @param PSX\Sql\Condition $condition
	 * @param array $fields
	 * @return PSX\Data\RecordInterface
	 */
	public function getOneBy(Condition $condition, array $fields = null);

	/**
	 * Returns an record by the primary key
	 *
	 * @return PSX\Data\RecordInterface
	 */
	public function get($id);

	/**
	 * Returns how many records exists matching the given condition
	 *
	 * @return integer
	 */
	public function getCount(Condition $condition = null);

	/**
	 * Returns all available fields of this handler
	 *
	 * @return array
	 */
	public function getSupportedFields();
}
