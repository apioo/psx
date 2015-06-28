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

use BadMethodCallException;
use InvalidArgumentException;
use PSX\Data\Record;

/**
 * TableQueryAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class TableQueryAbstract implements TableQueryInterface
{
	protected $restrictedFields = array();

	public function getBy(Condition $condition)
	{
		return $this->getAll(null, null, null, null, $condition);
	}

	public function getOneBy(Condition $condition)
	{
		$result = $this->getAll(0, 1, null, null, $condition);

		return current($result);
	}

	public function getRecord()
	{
		$supported = $this->getSupportedFields();
		$fields    = array_combine($supported, array_fill(0, count($supported), null));

		return new Record('record', $fields);
	}

	/**
	 * Returns an array of fields wich can not be used from the handler even if 
	 * the fields can be selected through the handler. This is useful for fields
	 * with sensetive data i.e. passwords
	 *
	 * @return array
	 */
	public function getRestrictedFields()
	{
		return $this->restrictedFields;
	}

	/**
	 * Sets the restricted fields
	 *
	 * @param array $restrictedFields
	 */
	public function setRestrictedFields(array $restrictedFields)
	{
		$this->restrictedFields = $restrictedFields;
	}

	/**
	 * Magic method to make conditional selection
	 *
	 * @param string $method
	 * @param string $arguments
	 * @return mixed
	 */
	public function __call($method, $arguments)
	{
		if(substr($method, 0, 8) == 'getOneBy')
		{
			$column = lcfirst(substr($method, 8));
			$value  = isset($arguments[0]) ? $arguments[0] : null;
			$fields = isset($arguments[1]) ? $arguments[1] : null;

			if(!empty($value))
			{
				$condition = new Condition(array($column, '=', $value));
			}
			else
			{
				throw new InvalidArgumentException('Value required');
			}

			return $this->getOneBy($condition, $fields);
		}
		else if(substr($method, 0, 5) == 'getBy')
		{
			$column = lcfirst(substr($method, 5));
			$value  = isset($arguments[0]) ? $arguments[0] : null;
			$fields = isset($arguments[1]) ? $arguments[1] : null;

			if(!empty($value))
			{
				$condition = new Condition(array($column, '=', $value));
			}
			else
			{
				throw new InvalidArgumentException('Value required');
			}

			return $this->getBy($condition, $fields);
		}
		else
		{
			throw new BadMethodCallException('Undefined method ' . $method);
		}
	}
}
