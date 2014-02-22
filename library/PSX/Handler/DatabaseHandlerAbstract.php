<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2013 Christoph Kappestein <k42b3.x@gmail.com>
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

use DateTime;
use BadMethodCallException;
use InvalidArgumentException;
use PSX\Data\ResultSet;
use PSX\Data\Record;
use PSX\Data\RecordInterface;
use PSX\Sql;
use PSX\Sql\Condition;
use PSX\Sql\TableInterface;
use PSX\Sql\TableManagerInterface;
use RuntimeException;

/**
 * Database handler wich implements all necessary methods using an
 * TableInterface. The TableInterface only simplyfies creating sql queries you
 * could also write an handler wich uses another DBAL or simply pure sql. Each
 * handler must specify the sql table and an select wich represents the view
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class DatabaseHandlerAbstract extends HandlerAbstract
{
	protected $manager;
	protected $table;

	protected $_select;

	public function __construct(TableManagerInterface $tm)
	{
		$this->manager = $tm;
		$this->table   = $this->getSelect()->getTable();
	}

	public function getAll(array $fields = array(), $startIndex = 0, $count = 16, $sortBy = null, $sortOrder = null, Condition $con = null)
	{
		$startIndex = $startIndex !== null ? (integer) $startIndex : 0;
		$count      = !empty($count)       ? (integer) $count      : 16;
		$sortBy     = $sortBy     !== null ? $sortBy               : $this->table->getPrimaryKey();
		$sortOrder  = $sortOrder  !== null ? (integer) $sortOrder  : Sql::SORT_DESC;

		$select = $this->getSelect();
		$fields = array_intersect($fields, $this->getSupportedFields());

		if(empty($fields))
		{
			$fields = $this->getSupportedFields();
		}

		$select->setColumns($fields)
			->orderBy($sortBy, $sortOrder)
			->limit($startIndex, $count);

		if($con !== null && $con->hasCondition())
		{
			$values = $con->toArray();

			foreach($values as $row)
			{
				$select->where($row[Condition::COLUMN], 
					$row[Condition::OPERATOR], 
					$row[Condition::VALUE], 
					$row[Condition::CONJUNCTION], 
					$row[Condition::TYPE]);
			}
		}

		$result = $select->getAll();
		$return = array();

		foreach($result as $row)
		{
			$return[] = new Record($this->table->getDisplayName(), $this->convertColumnTypes($row));
		}

		return $return;
	}

	public function get($id, array $fields = array())
	{
		$con = new Condition(array($this->table->getPrimaryKey(), '=', $id));

		return $this->getOneBy($con, $fields);
	}

	public function getSupportedFields()
	{
		return array_diff($this->getSelect()->getSupportedFields(), $this->getRestrictedFields());
	}

	public function getCount(Condition $con = null)
	{
		$select = $this->getSelect();

		if($con !== null && $con->hasCondition())
		{
			$values = $con->toArray();

			foreach($values as $row)
			{
				$select->where($row[Condition::COLUMN], 
					$row[Condition::OPERATOR], 
					$row[Condition::VALUE], 
					$row[Condition::CONJUNCTION], 
					$row[Condition::TYPE]);
			}
		}

		return $select->getTotalResults();
	}

	public function create(RecordInterface $record)
	{
		$this->table->insert($record);

		// set id to record
		$method = 'set' . ucfirst($this->table->getPrimaryKey());

		if(method_exists($record, $method))
		{
			$record->$method($this->table->getSql()->getLastInsertId());
		}
	}

	public function update(RecordInterface $record)
	{
		$this->table->update($record);
	}

	public function delete(RecordInterface $record)
	{
		$this->table->delete($record);
	}

	/**
	 * Returns the default select interface. The handler should select the 
	 * needed fields and join the fitting tables
	 *
	 * @return PSX\Sql\Table\SelectInterface
	 */
	abstract protected function getDefaultSelect();

	protected function getSelect()
	{
		if($this->_select === null)
		{
			$this->_select = $this->getDefaultSelect();
		}

		$select = clone $this->_select;

		return $select;
	}

	protected function convertColumnTypes(array $row)
	{
		$columns = $this->table->getColumns();
		$result  = array();

		foreach($row as $key => $value)
		{
			if(isset($columns[$key]))
			{
				$type = (($columns[$key] >> 20) & 0xFF) << 20;

				switch($type)
				{
					case TableInterface::TYPE_TINYINT:
					case TableInterface::TYPE_SMALLINT:
					case TableInterface::TYPE_MEDIUMINT:
					case TableInterface::TYPE_INT:
					case TableInterface::TYPE_BIGINT:
					case TableInterface::TYPE_BIT:
					case TableInterface::TYPE_SERIAL:
						$result[$key] = (integer) $value;
						break;

					case TableInterface::TYPE_DECIMAL:
					case TableInterface::TYPE_FLOAT:
					case TableInterface::TYPE_DOUBLE:
					case TableInterface::TYPE_REAL:
						$result[$key] = (float) $value;
						break;

					case TableInterface::TYPE_BOOLEAN:
						$result[$key] = (boolean) $value;
						break;

					case TableInterface::TYPE_DATE:
					case TableInterface::TYPE_DATETIME:
						$result[$key] = new DateTime($value);
						break;

					default:
						$result[$key] = $value;
						break;
				}
			}
			else
			{
				// @TODO how to handle columns from an join which are not listed 
				// in the table ... probably we can look at the table
				// connections to determine the field type but this is expensive
				$result[$key] = $value;
			}
		}

		return $result;
	}
}
