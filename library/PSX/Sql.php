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

namespace PSX;

use PDO;
use PSX\DateTime;
use PSX\Sql\Condition;
use PSX\Sql\Connection;
use stdClass;

/**
 * Sql
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Sql extends PDO implements Connection
{
	const LOW_PRIORITY  = 0x1;
	const DELAYED       = 0x2;
	const HIGH_PRIORITY = 0x4;
	const QUICK         = 0x8;
	const IGNORE        = 0x16;

	const FETCH_ASSOC   = 0x0;
	const FETCH_OBJECT  = 0x1;

	const SELECT_ALL    = 0x0;
	const SELECT_ROW    = 0x1;
	const SELECT_COL    = 0x2;
	const SELECT_FIELD  = 0x3;

	const SORT_ASC      = 0x0;
	const SORT_DESC     = 0x1;

	private $count = 0;

	public function __construct($host, $user, $pw, $db)
	{
		$dsn = sprintf('mysql:dbname=%s;host=%s', $db, $host);

		parent::__construct($dsn, $user, $pw);

		// set error handler
		$this->setAttribute(self::ATTR_ERRMODE, self::ERRMODE_EXCEPTION);

		// set default charset
		$this->exec('SET NAMES "utf8"');
	}

	public function assoc($sql, array $params = array(), $class = null, array $args = null)
	{
		// prepare statment
		$stmt = $this->prepare($sql);

		// bind params
		if(count($params) > 0)
		{
			foreach($params as $key => $value)
			{
				if($value instanceof \DateTime)
				{
					$value = $value->format(DateTime::SQL);
				}

				$key = is_numeric($key) ? $key + 1 : ':' . $key;

				$stmt->bindValue($key, $value, self::getType($value));
			}
		}

		// execute
		$stmt->execute();

		$this->count++;

		// fetch
		if($class === null)
		{
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		}
		else
		{
			return $stmt->fetchAll(PDO::FETCH_CLASS, $class, $args);
		}
	}

	public function execute($sql, array $params = array())
	{
		// prepare statment
		$stmt = $this->prepare($sql);

		// bind params
		if(count($params) > 0)
		{
			foreach($params as $key => $value)
			{
				if($value instanceof \DateTime)
				{
					$value = $value->format(DateTime::SQL);
				}

				if(is_numeric($key))
				{
					$key = $key + 1;
				}

				$stmt->bindValue($key, $value, self::getType($value));
			}
		}

		// execute
		$result = $stmt->execute();

		$this->count++;

		return $result;
	}

	public function getAll($sql, array $params = array(), $mode = 0, $class = 'stdClass', array $args = null)
	{
		$result = $this->getResult($sql, $params, $mode, $class, $args);

		if(!empty($result))
		{
			return $result;
		}

		return array();
	}

	public function getRow($sql, array $params = array(), $mode = 0, $class = 'stdClass', array $args = null)
	{
		$content = array();
		$result  = $this->getResult($sql, $params, $mode, $class, $args);

		if(!empty($result))
		{
			$content = current($result);

			unset($result);
		}

		return $content;
	}

	public function getCol($sql, array $params = array())
	{
		$content = array();
		$result  = $this->getResult($sql, $params, self::FETCH_ASSOC);

		if(!empty($result))
		{
			foreach($result as $row)
			{
				$content[] = current($row);
			}

			unset($result);
		}

		return $content;
	}

	public function getField($sql, array $params = array())
	{
		$content = false;
		$result  = $this->getResult($sql, $params, self::FETCH_ASSOC);

		if(!empty($result))
		{
			$row = current($result);

			unset($result);

			$content = current($row);
		}

		return $content;
	}

	public function select($table, array $fields, Condition $condition = null, $select = 0, $sortBy = null, $sortOrder = 0, $startIndex = null, $count = 32)
	{
		if(!empty($fields))
		{
			if($select === self::SELECT_FIELD && count($fields) > 1)
			{
				$fields = array_slice($fields, 0, 1);
			}

			if($condition !== null)
			{
				$sql    = 'SELECT ' . implode(', ', array_map(__CLASS__ . '::helpQuote', $fields)) . ' FROM `' . $table . '` ' . $condition->getStatment() . ' ';
				$params = $condition->getValues();
			}
			else
			{
				$sql    = 'SELECT ' . implode(', ', array_map(__CLASS__ . '::helpQuote', $fields)) . ' FROM `' . $table . '` ';
				$params = array();
			}

			if($sortBy !== null)
			{
				$sql.= 'ORDER BY `' . $sortBy . '` ' . ($sortOrder == self::SORT_ASC ? 'ASC' : 'DESC') . ' ';
			}

			if($select === self::SELECT_ROW || $select === self::SELECT_FIELD)
			{
				$sql.= 'LIMIT 0, 1';
			}
			else if($startIndex !== null)
			{
				$sql.= 'LIMIT ' . intval($startIndex) . ', ' . intval($count);
			}

			$result = null;

			switch($select)
			{
				case self::SELECT_ALL:
					$result = $this->getAll($sql, $params);
					break;

				case self::SELECT_ROW:
					$result = $this->getRow($sql, $params);
					break;

				case self::SELECT_COL:
					$result = $this->getCol($sql, $params);
					break;

				case self::SELECT_FIELD:
					$result = $this->getField($sql, $params);
					break;
			}

			return $result;
		}
		else
		{
			throw new Exception('Array must not be empty');
		}
	}

	public function insert($table, array $params, $modifier = 0)
	{
		if(!empty($params))
		{
			$keywords = '';

			if($modifier & self::LOW_PRIORITY)
			{
				$keywords.= ' LOW_PRIORITY ';
			}
			elseif($modifier & self::DELAYED)
			{
				$keywords.= ' DELAYED ';
			}
			elseif($modifier & self::HIGH_PRIORITY)
			{
				$keywords.= ' HIGH_PRIORITY ';
			}

			if($modifier & self::IGNORE)
			{
				$keywords.= ' IGNORE ';
			}

			$keys   = array_keys($params);
			$params = array_values($params);
			$sql    = 'INSERT ' . $keywords . ' `' . $table . '` SET ' . implode(', ', array_map(__CLASS__ . '::helpPrepare', $keys));

			return $this->execute($sql, $params);
		}
		else
		{
			throw new Exception('Array must not be empty');
		}
	}

	public function update($table, array $params, Condition $condition = null, $modifier = 0)
	{
		if(!empty($params))
		{
			$keywords = '';

			if($modifier & self::LOW_PRIORITY)
			{
				$keywords.= ' LOW_PRIORITY ';
			}

			if($modifier & self::IGNORE)
			{
				$keywords.= ' IGNORE ';
			}

			$keys = array_keys($params);

			if($condition !== null)
			{
				$sql    = 'UPDATE ' . $keywords . ' `' . $table . '` SET ' . implode(', ', array_map(__CLASS__ . '::helpPrepare', $keys)) . ' ' . $condition->getStatment();
				$params = array_merge(array_values($params), $condition->getValues());
			}
			else
			{
				$sql    = 'UPDATE ' . $keywords . ' `' . $table . '` SET ' . implode(', ', array_map(__CLASS__ . '::helpPrepare', $keys));
				$params = array_values($params);
			}

			return $this->execute($sql, $params);
		}
		else
		{
			throw new Exception('Array must not be empty');
		}
	}

	public function replace($table, array $params, $modifier = 0)
	{
		if(!empty($params))
		{
			$keywords = '';

			if($modifier & self::LOW_PRIORITY)
			{
				$keywords.= ' LOW_PRIORITY ';
			}
			elseif($modifier & self::DELAYED)
			{
				$keywords.= ' DELAYED ';
			}

			$keys   = array_keys($params);
			$params = array_values($params);
			$sql    = 'REPLACE ' . $keywords . ' `' . $table . '` SET ' . implode(', ', array_map(__CLASS__ . '::helpPrepare', $keys));

			return $this->execute($sql, $params);
		}
		else
		{
			throw new Exception('Array must not be empty');
		}
	}

	public function delete($table, Condition $condition = null, $modifier = 0)
	{
		$keywords = '';

		if($modifier & self::LOW_PRIORITY)
		{
			$keywords.= ' LOW_PRIORITY ';
		}

		if($modifier & self::QUICK)
		{
			$keywords.= ' QUICK ';
		}

		if($modifier & self::IGNORE)
		{
			$keywords.= ' IGNORE ';
		}

		if($condition !== null)
		{
			$sql    = 'DELETE ' . $keywords . ' FROM `' . $table . '` ' . $condition->getStatment();
			$params = $condition->getValues();
		}
		else
		{
			$sql    = 'DELETE ' . $keywords . ' FROM `' . $table . '`';
			$params = array();
		}

		return $this->execute($sql, $params);
	}

	public function count($table, Condition $condition = null)
	{
		if($condition !== null)
		{
			$sql    = 'SELECT COUNT(*) FROM `' . $table . '` ' . $condition->getStatment();
			$params = $condition->getValues();
		}
		else
		{
			$sql    = 'SELECT COUNT(*) FROM `' . $table . '`';
			$params = array();
		}

		return (integer) $this->getField($sql, $params);
	}

	/**
	 * Returns the number of executed sql queries
	 *
	 * @return integer
	 */
	public function getCount()
	{
		return $this->count;
	}

	public function getLastInsertId()
	{
		return $this->lastInsertId();
	}

	protected function getResult($sql, array $params = array(), $mode = 0, $class = 'stdClass', array $args = null)
	{
		switch($mode)
		{
			case self::FETCH_ASSOC:
				return $this->assoc($sql, $params);
				break;

			case self::FETCH_OBJECT:
				return $this->assoc($sql, $params, $class, $args);
				break;

			default:
				throw new Exception('Invalid mode');
		}
	}

	public static function helpQuote($str)
	{
		return '`' . $str . '`';
	}

	public static function helpPrepare($str)
	{
		return '`' . $str . '` = ?';
	}

	public static function getType($type)
	{
		switch(true)
		{
			case is_bool($type):
				return PDO::PARAM_BOOL;

			case is_null($type):
				return PDO::PARAM_NULL;

			case is_int($type):
				return PDO::PARAM_INT;

			default:
				return PDO::PARAM_STR;
		}
	}
}


