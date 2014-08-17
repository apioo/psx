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

use DateTime;
use PSX\Sql\Condition;

/**
 * Abstract handler which can be used to access internal data structures through 
 * the query interface i.e. to use an DOMDocument or an simple array
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class DataHandlerQueryAbstract extends HandlerQueryAbstract
{
	/**
	 * Unserialize a string into the fitting php type
	 *
	 * @param string $data
	 * @param integer $type
	 * @return mixed
	 */
	protected function unserializeType($data, $type)
	{
		$type = (($type >> 20) & 0xFF) << 20;

		switch($type)
		{
			case MappingAbstract::TYPE_INTEGER:
				return (int) $data;
				break;

			case MappingAbstract::TYPE_FLOAT:
				return (float) $data;
				break;

			case MappingAbstract::TYPE_BOOLEAN:
				return $data == 'false' ? false : (bool) $data;
				break;

			case MappingAbstract::TYPE_DATETIME:
				return new DateTime($data);
				break;

			default:
			case MappingAbstract::TYPE_STRING:
				return (string) $data;
				break;
		}
	}

	/**
	 * Serializes a given type into a string representation wich can be 
	 * unserialized to the given php type
	 *
	 * @param mixed $data
	 * @param integer $type
	 * @return string
	 */
	protected function serializeType($data, $type)
	{
		switch($type)
		{
			case MappingAbstract::TYPE_BOOLEAN:
				return $data ? '1' : '0';
				break;

			case MappingAbstract::TYPE_DATETIME:
				return $data instanceof DateTime ? $data->format(DateTime::RFC3339) : (string) $data;
				break;

			default:
			case MappingAbstract::TYPE_INTEGER:
			case MappingAbstract::TYPE_FLOAT:
			case MappingAbstract::TYPE_STRING:
				return (string) $data;
				break;
		}
	}

	/**
	 * Checks whether an row fulfills the condition specified in $condition
	 *
	 * @param PSX\Sql\Condition $condition
	 * @param array $row
	 * @return boolean
	 */
	protected function isConditionFulfilled(Condition $condition, array $row)
	{
		$values  = $condition->toArray();
		$isValid = true;

		foreach($values as $value)
		{
			if(isset($row[$value[Condition::COLUMN]]))
			{
				if($value[Condition::OPERATOR] == '=' || $value[Condition::OPERATOR] == 'IS')
				{
					$isValid = $row[$value[Condition::COLUMN]] == $value[Condition::VALUE];
				}
				else if($value[Condition::OPERATOR] == '!=' || $value[Condition::OPERATOR] == 'IS NOT')
				{
					$isValid = $row[$value[Condition::COLUMN]] != $value[Condition::VALUE];
				}
				else if($value[Condition::OPERATOR] == 'LIKE')
				{
					$isValid = $row[$value[Condition::COLUMN]] == $value[Condition::VALUE];
				}
				else if($value[Condition::OPERATOR] == 'NOT LIKE')
				{
					$isValid = $row[$value[Condition::COLUMN]] != $value[Condition::VALUE];
				}
				else if($value[Condition::OPERATOR] == '<')
				{
					$isValid = $row[$value[Condition::COLUMN]] < $value[Condition::VALUE];
				}
				else if($value[Condition::OPERATOR] == '>')
				{
					$isValid = $row[$value[Condition::COLUMN]] > $value[Condition::VALUE];
				}
				else if($value[Condition::OPERATOR] == '<=')
				{
					$isValid = $row[$value[Condition::COLUMN]] <= $value[Condition::VALUE];
				}
				else if($value[Condition::OPERATOR] == '>=')
				{
					$isValid = $row[$value[Condition::COLUMN]] >= $value[Condition::VALUE];
				}
				else if($value[Condition::OPERATOR] == 'IN')
				{
					$isValid = is_array($value[Condition::VALUE]) ? in_array($row[$value[Condition::COLUMN]], $value[Condition::VALUE]) : $row[$value[Condition::COLUMN]] == $value[Condition::VALUE];
				}

				if(!$isValid && ($value[Condition::CONJUNCTION] == 'AND' || $value[Condition::CONJUNCTION] == '&&'))
				{
					return false;
				}
				else if($isValid && ($value[Condition::CONJUNCTION] == 'OR' || $value[Condition::CONJUNCTION] == '||'))
				{
					return true;
				}
			}
		}

		return $isValid;
	}
}
