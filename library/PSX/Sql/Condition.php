<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace PSX\Sql;

use Countable;
use UnexpectedValueException;

/**
 * Condition
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
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

	private static $arithmeticOperator = array('=', 'IS', '!=', 'IS NOT', 'LIKE', 'NOT LIKE', '<', '>', '<=', '>=', 'IN');
	private static $logicOperator = array('AND', 'OR', '&&', '||');

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
	 * @return \PSX\Sql\Condition
	 */
	public function add($column, $operator, $value, $conjunction = 'AND', $type = self::TYPE_SCALAR)
	{
		if(!in_array($operator, self::$arithmeticOperator))
		{
			throw new UnexpectedValueException('Invalid arithmetic operator (allowed: ' . implode(', ', self::$arithmeticOperator) . ')');
		}

		if(!in_array($conjunction, self::$logicOperator))
		{
			throw new UnexpectedValueException('Invalid logic operator (allowed: ' . implode(', ', self::$logicOperator) . ')');
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
	 * @param \PSX\Sql\Condition $condition
	 * @return \PSX\Sql\Condition
	 */
	public function merge(Condition $condition)
	{
		$this->values = array_merge($this->values, $condition->toArray());

		return $this;
	}

	/**
	 * Removes an condition containing an specific column
	 *
     * @param string $column
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

					if(is_array($value[self::VALUE]))
					{
						$params = array_merge($params, $value[self::VALUE]);
					}
					else
					{
						$params[] = $value[self::VALUE];
					}

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
	 * Returns an column => value array of this condition
	 *
	 * @return array
	 */
	public function getArray()
	{
		$params = array();

		foreach($this->values as $value)
		{
			$params[$value[self::COLUMN]] = $value[self::VALUE];
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
