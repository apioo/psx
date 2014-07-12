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
	 * Returns an array of records matching the condition
	 *
	 * @return array<PSX\Data\RecordInterface>
	 */
	public function getAll(array $fields, $startIndex = 0, $count = 16, $sortBy = null, $sortOrder = null, Condition $con = null);

	/**
	 * Returns an array of records matching the condition
	 *
	 * @return array<PSX\Data\RecordInterface>
	 */
	public function getBy(Condition $con, array $fields = array());

	/**
	 * Returns an record by the condition
	 *
	 * @return PSX\Data\RecordInterface
	 */
	public function getOneBy(Condition $con, array $fields = array());

	/**
	 * Returns an record by the primary key
	 *
	 * @return PSX\Data\RecordInterface
	 */
	public function get($id, array $fields = array());

	/**
	 * Returns all available fields for this handler
	 *
	 * @return array
	 */
	public function getSupportedFields();

	/**
	 * Returns how many records exists matching the given condition
	 *
	 * @return integer
	 */
	public function getCount(Condition $con = null);

	/**
	 * Returns the record which can be used for import
	 *
	 * @return PSX\Data\RecordInterface
	 */
	public function getRecord();
}

