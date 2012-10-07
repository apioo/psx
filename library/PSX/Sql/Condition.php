<?php
/*
 *  $Id: Condition.php 582 2012-08-15 21:27:02Z k42b3.x@googlemail.com $
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
 * PSX_Sql_Condition
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Sql
 * @version    $Revision: 582 $
 */
class PSX_Sql_Condition implements Countable
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

	public function __construct(array $con_1 = array(), array $con_2 = array(), array $con_3 = array())
	{
		if(count($con_1) >= 3)
		{
			if(isset($con_1[3]))
			{
				$this->add($con_1[0], $con_1[1], $con_1[2], $con_1[3]);
			}
			else
			{
				$this->add($con_1[0], $con_1[1], $con_1[2]);
			}
		}

		if(count($con_2) >= 3)
		{
			if(isset($con_2[3]))
			{
				$this->add($con_2[0], $con_2[1], $con_2[2], $con_2[3]);
			}
			else
			{
				$this->add($con_2[0], $con_2[1], $con_2[2]);
			}
		}

		if(count($con_3) >= 3)
		{
			if(isset($con_1[3]))
			{
				$this->add($con_3[0], $con_3[1], $con_3[2], $con_3[3]);
			}
			else
			{
				$this->add($con_3[0], $con_3[1], $con_3[2]);
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
	 * @return PSX_Sql_Condition
	 */
	public function add($column, $operator, $value, $conjunction = 'AND', $type = 0x1)
	{
		if(!in_array($operator, self::$a_op))
		{
			throw new PSX_Sql_Condition_Exception('Invalid arithmetic operator (allowed: ' . implode(', ', self::$a_op) . ')');
		}

		if(!in_array($conjunction, self::$l_op))
		{
			throw new PSX_Sql_Condition_Exception('Invalid logic operator (allowed: ' . implode(', ', self::$l_op) . ')');
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
	 * @param PSX_Sql_Condition $condition
	 * @return PSX_Sql_Condition
	 */
	public function merge(PSX_Sql_Condition $condition)
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
	 * Returns the conditions as SQL wich can be appended to any query. If the
	 * value is an string the mysql_real_escape_string function is used to
	 * escape
	 *
	 * @return string
	 */
	public function toString()
	{
		if($this->str === null)
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

							$params = array();

							foreach($value[self::VALUE] as $val)
							{
								$params[] = self::escape($val);
							}

							$conditions.= $value[self::COLUMN] . ' IN (' . implode(',', $params) . ')';
							break;

						case self::TYPE_SCALAR:
						default:

							$conditions.= $value[self::COLUMN] . ' ' . $value[self::OPERATOR] . ' ' . self::escape($value[self::VALUE]);
							break;
					}

					$conditions.= ($i < $len - 1) ? ' ' . $value[self::CONJUNCTION] . ' ' : '';
				}

				return $this->str = 'WHERE ' . $conditions;
			}
			else
			{
				return $this->str = '';
			}
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

	public static function escape($value)
	{
		if(is_int($value) || is_float($value) || is_bool($value))
		{
			return $value;
		}
		else if(is_null($value))
		{
			return 'NULL';
		}
		else
		{
			return '"' . mysql_real_escape_string($value) . '"';
		}
	}

	public static function parse($stmt)
	{
		$condition = new self();

		do
		{
			$column      = self::getColumn($stmt);
			$operator    = self::getOperator($stmt);
			$value       = self::getValue($stmt);
			$conjunction = self::getConjunction($stmt);

			if(!empty($conjunction))
			{
				$condition->add($column, $operator, $value, $conjunction);
			}
			else
			{
				$condition->add($column, $operator, $value);
			}
		}
		while(!empty($column) && !empty($operator) && !empty($value));

		return $condition;
	}

	private static function getColumn(&$str)
	{
		$str = trim($str);
		$pos = strpos($str, ' ');

		if($pos !== false)
		{
			$column = substr($str, 0, $pos);
			$str    = substr($str, $pos);

			return $column;
		}
		else
		{
			$column = $str;
			$str    = '';

			return $column;
		}
	}

	private static function getOperator(&$str)
	{
		$str = trim($str);

		foreach(self::$a_op as $op)
		{
			$len = strlen($op);

			if(strcasecmp(substr($str, 0, $len), $op) == 0)
			{
				$operator = substr($str, 0, $len);
				$str      = substr($str, $len);

				return $operator;
			}
		}

		return false;
	}

	private static function getValue(&$str)
	{
		$str = trim($str);

		if(empty($str))
		{
			return false;
		}

		if($str[0] == '"')
		{
			$pos = strpos($str, '"', 1);

			if($pos !== false)
			{
				$value = substr($str, 1, $pos);
				$str   = substr($str, $pos);

				return $value;
			}
		}
		elseif($str[0] == '\'')
		{
			$pos = strpos($str, '\'', 1);

			if($pos !== false)
			{
				$value = substr($str, 1, $pos);
				$str   = substr($str, $pos);

				return $value;
			}
		}
		else
		{
			$len = strlen($str);

			for($i = 0; $i < $len; $i++)
			{
				if(!trim($str[$i]))
				{
					$value = substr($str, 0, $i);
					$str   = substr($str, $i);

					return $value;
				}
			}

			$value = $str;
			$str   = '';

			return $value;
		}
	}

	private static function getConjunction(&$str)
	{
		$str = trim($str);

		foreach(self::$l_op as $op)
		{
			$len = strlen($op);

			if(strcasecmp(substr($str, 0, $len), $op) == 0)
			{
				$conjunction = substr($str, 0, $len);
				$str         = substr($str, $len);

				return $conjunction;
			}
		}

		return false;
	}
}