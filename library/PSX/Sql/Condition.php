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

namespace PSX\Sql;

use Countable;
use UnexpectedValueException;

/**
 * Condition
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Condition implements Countable
{
	const COLUMN      = 0x0;
	const OPERATOR    = 0x1;
	const VALUE       = 0x2;
	const CONJUNCTION = 0x3;
	const TYPE        = 0x4;

	const TYPE_SCALAR = 0x1;
	const TYPE_IN     = 0x2;
	const TYPE_RAW    = 0x3;

	private static $a_op = array('=', 'IS', '!=', 'IS NOT', 'LIKE', 'NOT LIKE', '<', '>', '<=', '>=', 'IN');
	private static $l_op = array('AND', 'OR', '&&', '||');

	private $values = array();
	private $stmt;
	private $str;

	public function __construct(array $condition = array())
	{
		if(count($condition) >= 3)
		{
			if(isset($condition[3]))
			{
				$this->add($condition[0], $condition[1], $condition[2], $condition[3]);
			}
			else
			{
				$this->add($condition[0], $condition[1], $condition[2]);
			}
		}
	}

	/**
	 * Adds an condition
	 *
	 * @param string $column
	 * @param string $operator
	 * @param string $value
	 * @param string $conjunction
	 * @param int $type
	 * @return PSX\Sql\Condition
	 */
	public function add($column, $operator, $value, $conjunction = 'AND', $type = 0x1)
	{
		if(!in_array($operator, self::$a_op))
		{
			throw new UnexpectedValueException('Invalid arithmetic operator (allowed: ' . implode(', ', self::$a_op) . ')');
		}

		if(!in_array($conjunction, self::$l_op))
		{
			throw new UnexpectedValueException('Invalid logic operator (allowed: ' . implode(', ', self::$l_op) . ')');
		}

		if($operator == 'IN')
		{
			$type = self::TYPE_IN;
		}

		$this->values[] = array(

			self::COLUMN      => $column,
			self::OPERATOR    => $operator,
			self::VALUE       => $value,
			self::CONJUNCTION => $conjunction,
			self::TYPE        => $type,

		);

		return $this;
	}

	/**
	 * Returns the count of conditions
	 *
	 * @return integer
	 */
	public function count()
	{
		return count($this->values);
	}

	/**
	 * Merges an existing condition
	 *
	 * @param PSX\Sql\Condition $condition
	 * @return PSX\Sql\Condition
	 */
	public function merge(Condition $condition)
	{
		$this->values = array_merge($this->values, $condition->toArray());

		return $this;
	}

	/**
	 * Removes an condition containing an specific column
	 *
	 * @return boolean
	 */
	public function remove($column)
	{
		foreach($this->values as $i => $value)
		{
			if($value[self::COLUMN] == $column)
			{
				unset($this->values[$i]);

				return true;
			}
		}

		return false;
	}

	/**
	 * Removes all columns and cleans the internal cache
	 *
	 * @return void
	 */
	public function removeAll()
	{
		$this->clear();

		$this->values = array();
	}

	/**
	 * Cleans the internal cache
	 *
	 * @return void
	 */
	public function clear()
	{
		$this->stmt = null;
		$this->str  = null;
	}

	/**
	 * Returns all conditions as array
	 *
	 * @return array
	 */
	public function toArray()
	{
		return $this->values;
	}

	/**
	 * Returns whether an condition exist
	 *
	 * @return boolean
	 */
	public function hasCondition()
	{
		return count($this->values) > 0;
	}

	/**
	 * Returnes the prepared statment containing questionmarks for each value.
	 *
	 * @return string
	 */
	public function getStatment()
	{
		if($this->stmt === null)
		{
			if(!empty($this->values))
			{
				$len        = count($this->values);
				$conditions = '';

				foreach($this->values as $i => $value)
				{
					switch($value[self::TYPE])
					{
						case self::TYPE_RAW:

							$conditions.= $value[self::COLUMN] . ' ' . $value[self::OPERATOR] . ' ' . $value[self::VALUE];
							break;

						case self::TYPE_IN:

							$conditions.= $value[self::COLUMN] . ' IN (' . implode(',', array_fill(0, count($value[self::VALUE]), '?')) . ')';
							break;

						case self::TYPE_SCALAR:
						default:

							$conditions.= $value[self::COLUMN] . ' ' . $value[self::OPERATOR] . ' ?';
							break;
					}

					$conditions.= ($i < $len - 1) ? ' ' . $value[self::CONJUNCTION] . ' ' : '';
				}

				return $this->stmt = 'WHERE ' . $conditions;
			}
			else
			{
				return $this->stmt = '';
			}
		}
		else
		{
			return $this->stmt;
		}
	}

	/**
	 * Returns all values wich belongs to the statment
	 *
	 * @return array
	 */
	public function getValues()
	{
		$params = array();

		foreach($this->values as $value)
		{
			switch($value[self::TYPE])
			{
				case self::TYPE_RAW:

					break;

				case self::TYPE_IN:

					$params+= $value[self::VALUE];
					break;

				case self::TYPE_SCALAR:
				default:

					$params[] = $value[self::VALUE];
					break;
			}
		}

		return $params;
	}

	/**
	 * Returns and identifier wich represents the values from this condition
	 *
	 * @return string
	 */
	public function toString()
	{
		if($this->str === null)
		{
			$len        = count($this->values);
			$conditions = '';

			foreach($this->values as $i => $value)
			{
				switch($value[self::TYPE])
				{
					case self::TYPE_RAW:

						$conditions.= $value[self::COLUMN] . '-' . $value[self::OPERATOR] . '-' . $value[self::VALUE];
						break;

					case self::TYPE_IN:

						$params = array();

						foreach($value[self::VALUE] as $val)
						{
							$params[] = $val;
						}

						$conditions.= $value[self::COLUMN] . '-' . implode(',', $params);
						break;

					case self::TYPE_SCALAR:
					default:

						$conditions.= $value[self::COLUMN] . '-' . $value[self::OPERATOR] . '-' . $value[self::VALUE];
						break;
				}

				$conditions.= ($i < $len - 1) ? ' ' . $value[self::CONJUNCTION] . ' ' : '';
			}

			return $this->str = md5($conditions);
		}
		else
		{
			return $this->str;
		}
	}

	public function __tostring()
	{
		return $this->toString();
	}

	public static function fromCriteria(array $criteria)
	{
		$condition = new self();

		foreach($criteria as $field => $value)
		{
			if(is_array($value))
			{
				$condition->add($field, 'IN', $value);
			}
			else if(is_null($value))
			{
				$condition->add($field, 'IS', 'NULL', 'AND', self::TYPE_RAW);
			}
			else
			{
				$condition->add($field, '=', $value);
			}
		}

		return $condition;
	}
}
