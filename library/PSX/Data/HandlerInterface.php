<?php
/*
 *  $Id: HandlerInterface.php 480 2012-05-01 18:13:54Z k42b3.x@googlemail.com $
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

namespace PSX\Data;

use PSX\Sql\Condition;

/**
 * The handler is a concept in psx to abstract away sql queries from your 
 * business logic. Its similar to the repository concept of doctrine. So when 
 * you need a query i.e. to get the latest news you would add a method 
 * getLatestNews() to your news handler instead of writing the query in the 
 * controller.
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Data
 * @version    $Revision: 480 $
 */
interface HandlerInterface
{
	/**
	 * Returns an array of records matching the conditions
	 *
	 * @return array<RecordInterface>
	 */
	public function getAll(array $fields, 
		$startIndex = 0, 
		$count = 16, 
		$sortBy = null, 
		$sortOrder = null, 
		Condition $con = null, 
		$mode = 0, 
		$class = null, 
		array $args = array());

	/**
	 * Returns an record by the id
	 *
	 * @return RecordInterface
	 */
	public function getById($id, 
		array $fields = array(), 
		$mode = 0, 
		$class = null, 
		array $args = array());

	/**
	 * Returns all records matching the criteria and also gets the total amount
	 * of records. Returns an resultset wich can easily displayed on an html 
	 * page with a pagination or exported as XML or JSON for an API
	 *
	 * @return ResultSet
	 */
	public function getResultSet(array $fields, 
		$startIndex = 0, 
		$count = 16, 
		$sortBy = null, 
		$sortOrder = null, 
		Condition $con = null, 
		$mode = 0, 
		$class = null, 
		array $args = array());

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
	 * Returns a new record if the $id is not defined else an existing record
	 *
	 * @param integer $id
	 * @return PSX_Data_RecordInterface
	 */
	public function getRecord($id = null);

	/**
	 * Returns the class name of the record class
	 *
	 * @return string
	 */
	public function getClassName();

	/**
	 * Create the record
	 *
	 * @return void
	 */
	public function create(RecordInterface $record);

	/**
	 * Update the record
	 *
	 * @return void
	 */
	public function update(RecordInterface $record);

	/**
	 * Delete the record
	 *
	 * @return void
	 */
	public function delete(RecordInterface $record);
}

