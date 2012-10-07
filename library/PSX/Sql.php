<?php
/*
 *  $Id: Sql.php 641 2012-09-30 22:45:49Z k42b3.x@googlemail.com $
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
 * PSX_Sql
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Sql
 * @version    $Revision: 641 $
 */
class PSX_Sql
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

	private $driver;
	private $count = 0;
	private $list  = array();

	public function __construct($host, $user, $pw, $db, PSX_Sql_DriverInterface $driver = null)
	{
		$this->driver = $driver !== null ? $driver : new PSX_Sql_Driver_Pdo();

		if(!($this->driver->connect($host, $user, $pw, $db)))
		{
			throw new PSX_Sql_Exception('Couldnt connect to database!');
		}
		else
		{
			// set default charset
			$this->exec('SET NAMES "utf8"');
		}
	}

	/**
	 * Main method for data selection. It returns either an associative array
	 * with the data or false. It uses prepared statments so you can write
	 * questionmarks in your query and provide the values in the $params array
	 *
	 * @param string $sql
	 * @param array $params
	 * @return array|false
	 */
	public function assoc($sql, array $params = array())
	{
		$stmt = $this->prepare($sql);

		if(count($params) > 0)
		{
			foreach($params as $v)
			{
				$stmt->bindParam($v);
			}
		}

		$stmt->execute();


		$lastError = $stmt->error();

		if(!empty($lastError))
		{
			throw new PSX_Sql_Exception($lastError);
		}


		$this->count++;

		$content = false;

		if($stmt->numRows() > 0)
		{
			$content = $stmt->fetchAssoc();
		}

		return $content;
	}

	/**
	 * Method for data selection. This is an ORM like method where you get an
	 * array of instances of the $class. As third argument you can give an array
	 * of variables wich are passed to the constructor of the $class. This
	 * method is maybe slower then assoc because it creates for each record an
	 * instance of $class
	 *
	 * @param string $sql
	 * @param string $class
	 * @param array $args
	 * @param array $params
	 * @return array|false
	 */
	public function object($sql, array $params = array(), $class = 'stdClass', array $args = array())
	{
		$stmt = $this->prepare($sql);

		if(count($params) > 0)
		{
			foreach($params as $v)
			{
				$stmt->bindParam($v);
			}
		}

		$stmt->execute();


		$lastError = $stmt->error();

		if(!empty($lastError))
		{
			throw new PSX_Sql_Exception($lastError);
		}


		$this->count++;

		$content = false;

		if($stmt->numRows() > 0)
		{
			$content = $stmt->fetchObject($class, $args);
		}

		return $content;
	}

	/**
	 * Main method for data manipulation (INSERT, UPDATE, REPLACE, DELETE). It
	 * returns the number of affected rows. It uses prepared statments so you
	 * can write questionmarks in your query and provide the values in the
	 * $params array
	 *
	 * @param string $sql
	 * @param array $params
	 * @return boolean
	 */
	public function query($sql, array $params = array())
	{
		$stmt = $this->prepare($sql);

		if(count($params) > 0)
		{
			foreach($params as $v)
			{
				$stmt->bindParam($v);
			}
		}

		$stmt->execute();


		$lastError = $stmt->error();

		if(!empty($lastError))
		{
			throw new PSX_Sql_Exception($lastError);
		}


		$this->count++;

		return $stmt->numRows();
	}

	/**
	 * Main method for executing queries where you dont need prepared statments
	 * i.e. setting the charset or selecting another database.
	 *
	 * @param string $sql
	 * @return boolean
	 */
	public function exec($sql)
	{
		if($this->driver->exec($sql) === false)
		{
			throw new PSX_Sql_Exception($this->driver->error());
		}
		else
		{
			$this->count++;

			return true;
		}
	}

	/**
	 * Returns the result of the query as an array where each row is an
	 * associative where array([column] => [value])
	 *
	 * @param string $sql
	 * @param array $params
	 * @param integer $mode
	 * @param string $class
	 * @param array $args
	 * @return array
	 */
	public function getAll($sql, array $params = array(), $mode = 0, $class = 'stdClass', array $args = array())
	{
		$result = $this->getResult($sql, $params, $mode, $class, $args);

		if(!empty($result))
		{
			return $result;
		}

		return array();
	}

	/**
	 * Returns a single row as associative array where
	 * array([column] => [value])
	 *
	 * @param string $sql
	 * @param array $params
	 * @param integer $mode
	 * @param string $class
	 * @param array $args
	 * @return array
	 */
	public function getRow($sql, array $params = array(), $mode = 0, $class = 'stdClass', array $args = array())
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

	/**
	 * Returns all values from one column as array
	 *
	 * @param string $sql
	 * @param array $params
	 * @return array
	 */
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

	/**
	 * Returns the value of the first row and colum from the result
	 *
	 * @param string $sql
	 * @param array $params
	 * @return array
	 */
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

	public function getResult($sql, array $params = array(), $mode = 0, $class = 'stdClass', array $args = array())
	{
		$result = null;

		switch($mode)
		{
			case self::FETCH_ASSOC:

				$result = $this->assoc($sql, $params);

				break;

			case self::FETCH_OBJECT:

				$result = $this->object($sql, $params, $class, $args);

				break;

			default:

				throw new PSX_Sql_Exception('Invalid mode');
		}

		return $result;
	}

	/**
	 * Selects all $fields from the $table with the $condition. Calls depending
	 * on the $select value the getAll, getRow, getCol or getField method
	 *
	 * @param string $table
	 * @param array $fields
	 * @param PSX_Sql_Condition $condition
	 * @param integer $select
	 * @param string $sortBy
	 * @param integer $sortOrder
	 * @param integer $startIndex
	 * @param integer $count
	 * @return array
	 */
	public function select($table, array $fields, PSX_Sql_Condition $condition = null, $select = 0, $sortBy = null, $sortOrder = 0, $startIndex = null, $count = 32)
	{
		if(!empty($fields))
		{
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

			if($startIndex !== null)
			{
				if($select === self::SELECT_ROW || $select === self::SELECT_FIELD)
				{
					$sql.= 'LIMIT 0, 1';
				}
				else
				{
					$sql.= 'LIMIT ' . intval($startIndex) . ', ' . intval($count);
				}
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
			throw new PSX_Sql_Exception('Array must not be empty');
		}
	}

	/**
	 * Inserts into the $table the values $params
	 *
	 * @param string $table
	 * @param array $params
	 * @param integer $modifier
	 * @return boolean
	 */
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

			$keys = array_keys($params);
			$sql  = 'INSERT ' . $keywords . ' `' . $table . '` SET ' . implode(', ', array_map(__CLASS__ . '::helpPrepare', $keys));

			return $this->query($sql, $params);
		}
		else
		{
			throw new PSX_Sql_Exception('Array must not be empty');
		}
	}

	/**
	 * Update the $params on the $table with the $condition
	 *
	 * @param string $table
	 * @param array $params
	 * @param PSX_Sql_Condition $condition
	 * @param integer $modifier
	 * @return integer
	 */
	public function update($table, array $params, PSX_Sql_Condition $condition = null, $modifier = 0)
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

			return $this->query($sql, $params);
		}
		else
		{
			throw new PSX_Sql_Exception('Array must not be empty');
		}
	}

	/**
	 * Replace the $params on the $table with the $condition
	 *
	 * @param string $table
	 * @param array $params
	 * @param integer $modifier
	 * @return integer
	 */
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

			$keys = array_keys($params);
			$sql  = 'REPLACE ' . $keywords . ' `' . $table . '` SET ' . implode(', ', array_map(__CLASS__ . '::helpPrepare', $keys));

			return $this->query($sql, $params);
		}
		else
		{
			throw new PSX_Sql_Exception('Array must not be empty');
		}
	}

	/**
	 * Deletes the record on the $table with the $condition
	 *
	 * @param string $table
	 * @param PSX_Sql_Condition $condition
	 * @param integer $modifier
	 * @return integer
	 */
	public function delete($table, PSX_Sql_Condition $condition = null, $modifier = 0)
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

		return $this->query($sql, $params);
	}

	/**
	 * Returns the count of rows from the $table with the $condition
	 *
	 * @return integer
	 */
	public function count($table, PSX_Sql_Condition $condition = null)
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
	 * Starts an transaction if it is supported by the table engine
	 *
	 * @return void
	 */
	public function beginTransaction()
	{
		$this->driver->beginTransaction();
	}

	/**
	 * Commits an transaction if it is supported by the table engine
	 *
	 * @return void
	 */
	public function commit()
	{
		$this->driver->commit();
	}

	/**
	 * Rollback an transaction if it is supported by the table engine
	 *
	 * @return void
	 */
	public function rollback()
	{
		$this->driver->rollback();
	}

	/**
	 * Closes the connection to the database
	 *
	 * @return void
	 */
	public function close()
	{
		$this->driver->close();
	}

	/**
	 * Returns the last inserted id
	 *
	 * @return integer
	 */
	public function getLastInsertId()
	{
		return $this->driver->lastInsertId();
	}

	/**
	 * Places quotes around the input string (if required) and escapes special
	 * characters within the input string
	 *
	 * @return string
	 */
	public function quote($string)
	{
		return $this->driver->quote($string);
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

	/**
	 * Returns the used driver
	 *
	 * @return PSX_Sql_DriverInterface
	 */
	public function getDriver()
	{
		return $this->driver;
	}

	/**
	 * Prepares an raw sql query and returns an statment object. The query is
	 * cached that means if you call the method two times with the same query
	 * you will get the same statment object
	 *
	 * @param string $sql
	 * @return PSX_Sql_StmtInterface
	 */
	public function prepare($sql)
	{
		$key = md5($sql);

		if(!isset($this->list[$key]))
		{
			$stmt = $this->driver->prepare($sql);

			$this->list[$key] = $stmt;
		}
		else
		{
			$stmt = $this->list[$key];
		}

		return $stmt;
	}

	public static function helpQuote($str)
	{
		return '`' . $str . '`';
	}

	public static function helpPrepare($str)
	{
		return '`' . $str . '` = ?';
	}
}


