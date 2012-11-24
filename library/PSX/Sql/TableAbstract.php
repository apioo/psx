<?php
/*
 *  $Id: TableAbstract.php 642 2012-09-30 22:47:38Z k42b3.x@googlemail.com $
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

/**
 * PSX_Sql_TableAbstract
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Sql
 * @version    $Revision: 642 $
 */
abstract class PSX_Sql_TableAbstract implements PSX_Sql_TableInterface
{
	protected $sql;
	protected $select;

	public function __construct(PSX_Sql $sql)
	{
		$this->sql = $sql;
	}

	public function getSql()
	{
		return $this->sql;
	}

	public function getDisplayName()
	{
		$name = $this->getName();
		$pos  = strrpos($name, '_');

		return $pos !== false ? substr($name, strrpos($name, '_') + 1) : $name;
	}

	public function getPrimaryKey()
	{
		return $this->getFirstColumnWithAttr(self::PRIMARY_KEY);
	}

	public function getFirstColumnWithAttr($searchAttr)
	{
		$columns = $this->getColumns();

		foreach($columns as $column => $attr)
		{
			if($attr & $searchAttr)
			{
				return $column;
			}
		}

		return null;
	}

	public function getFirstColumnWithType($searchType)
	{
		$columns = $this->getColumns();

		foreach($columns as $column => $attr)
		{
			if(((($attr >> 20) & 0xFF) << 20) === $searchType)
			{
				return $column;
			}
		}

		return null;
	}

	public function getValidColumns(array $columns)
	{
		return array_intersect($columns, array_keys($this->getColumns()));
	}

	public function select(array $columns = array(), $prefix = null)
	{
		$this->select = new PSX_Sql_Table_Select($this, $prefix);

		if(in_array('*', $columns))
		{
			$this->select->setColumns(array_keys($this->getColumns()));
		}
		else
		{
			$this->select->setColumns($columns);
		}

		return $this->select;
	}

	public function getLastSelect()
	{
		return $this->select;
	}

	public function getRecord($id = null)
	{
		$class = $this->getDefaultRecordClass();
		$args  = $this->getDefaultRecordArgs();

		if($id !== null)
		{
			$fields = implode(', ', array_map('PSX_Sql::helpQuote', array_keys($this->getColumns())));
			$sql    = 'SELECT ' . $fields . ' FROM `' . $this->getName() . '` WHERE `' . $this->getPrimaryKey() . '` = ?';
			$record = $this->sql->getRow($sql, array($id), PSX_Sql::FETCH_OBJECT, $class, $args);

			if(empty($record))
			{
				throw new PSX_Sql_Exception('Invalid record id');
			}
		}
		else
		{
			$ref    = new ReflectionClass($class);
			$record = $ref->newInstanceArgs($args);
		}

		return $record;
	}

	public function getAll(array $fields, PSX_Sql_Condition $condition = null, $sortBy = null, $sortOrder = 0, $startIndex = null, $count = 32)
	{
		$fields = $this->getValidColumns($fields);

		return $this->sql->select($this->getName(), $fields, $condition, PSX_Sql::SELECT_ALL, $sortBy, $sortOrder, $startIndex, $count);
	}

	public function getRow(array $fields, PSX_Sql_Condition $condition = null, $sortBy = null, $sortOrder = 0)
	{
		$fields = $this->getValidColumns($fields);

		return $this->sql->select($this->getName(), $fields, $condition, PSX_Sql::SELECT_ROW, $sortBy, $sortOrder);
	}

	public function getCol($field, PSX_Sql_Condition $condition = null, $sortBy = null, $sortOrder = 0, $startIndex = null, $count = 32)
	{
		$fields = $this->getValidColumns(array($field));

		return $this->sql->select($this->getName(), $fields, $condition, PSX_Sql::SELECT_COL, $sortBy, $sortOrder, $startIndex, $count);
	}

	public function getField($field, PSX_Sql_Condition $condition = null, $sortBy = null, $sortOrder = 0)
	{
		$fields = $this->getValidColumns(array($field));

		return $this->sql->select($this->getName(), $fields, $condition, PSX_Sql::SELECT_FIELD, $sortBy, $sortOrder);
	}

	public function count(PSX_Sql_Condition $condition = null)
	{
		return $this->sql->count($this->getName(), $condition);
	}

	public function insert($params, $modifier = 0)
	{
		if(is_array($params))
		{
		}
		else if($params instanceof PSX_Data_RecordInterface)
		{
			$params = $params->getData();
		}
		else
		{
			throw new PSX_Sql_Table_Exception('Params must be either an array or instance of PSX_Data_RecordInterface');
		}

		$fields = array_intersect_key($params, $this->getColumns());

		if(empty($fields))
		{
			throw new PSX_Sql_Table_Exception('No valid field set');
		}

		return $this->sql->insert($this->getName(), $fields, $modifier);
	}

	public function update($params, PSX_Sql_Condition $condition = null, $modifier = 0)
	{
		if(is_array($params))
		{
		}
		else if($params instanceof PSX_Data_RecordInterface)
		{
			$params = $params->getData();
		}
		else
		{
			throw new PSX_Sql_Table_Exception('Params must be either an array or instance of PSX_Data_RecordInterface');
		}

		$fields = array_intersect_key($params, $this->getColumns());

		if(!empty($fields))
		{
			if($condition === null)
			{
				$pk = $this->getPrimaryKey();

				if(isset($fields[$pk]))
				{
					$condition = new PSX_Sql_Condition(array($pk, '=', $fields[$pk]));
				}
				else
				{
					throw new PSX_Sql_Table_Exception('No primary key set');
				}
			}
		}
		else
		{
			throw new PSX_Sql_Table_Exception('No valid field set');
		}

		return $this->sql->update($this->getName(), $fields, $condition, $modifier);
	}

	public function replace($params, $modifier = 0)
	{
		if(is_array($params))
		{
		}
		else if($params instanceof PSX_Data_RecordInterface)
		{
			$params = $params->getData();
		}
		else
		{
			throw new PSX_Sql_Table_Exception('Params must be either an array or instance of PSX_Data_RecordInterface');
		}

		$fields = array_intersect_key($params, $this->getColumns());

		if(empty($fields))
		{
			throw new PSX_Sql_Table_Exception('No valid field set');
		}

		return $this->sql->replace($this->getName(), $fields, $modifier);
	}

	public function delete($params, $modifier = 0)
	{
		$condition = null;

		if(is_array($params))
		{
		}
		else if($params instanceof PSX_Data_RecordInterface)
		{
			$params = $params->getData();
		}
		else if($params instanceof PSX_Sql_Condition)
		{
			$condition = $params;
		}
		else
		{
			throw new PSX_Sql_Table_Exception('Params must be either an array or instance of PSX_Data_RecordInterface');
		}

		if($condition === null)
		{
			$fields = array_intersect_key($params, $this->getColumns());

			if(!empty($fields))
			{
				$pk = $this->getPrimaryKey();

				if(isset($fields[$pk]))
				{
					$condition = new PSX_Sql_Condition(array($pk, '=', $fields[$pk]));
				}
				else
				{
					throw new PSX_Sql_Table_Exception('No primary key set');
				}
			}
			else
			{
				throw new PSX_Sql_Table_Exception('No valid field set');
			}
		}

		return $this->sql->delete($this->getName(), $condition, $modifier);
	}

	public function getDefaultRecordClass()
	{
		return substr(get_class($this), 0, -6);
	}

	public function getDefaultRecordArgs()
	{
		return array($this);
	}
}
