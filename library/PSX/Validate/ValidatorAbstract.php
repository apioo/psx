<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Validate;

use PSX\Data\Record;
use PSX\Validate;

/**
 * ValidatorAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class ValidatorAbstract implements ValidatorInterface
{
	protected $validate;
	protected $fields;
	protected $flag;

	protected $errors = array();

	/**
	 * @param PSX\Validate $validate
	 * @param array<PSX\Filter\Definition\Property> $fields
	 * @param integer $flag
	 */
	public function __construct(Validate $validate, array $fields = null, $flag = self::THROW_ERRORS)
	{
		$this->validate = $validate;
		$this->fields   = $fields;
		$this->flag     = $flag;
	}

	/**
	 * @param array<PSX\Filter\Definition\Property> $fields
	 */
	public function setFields(array $fields)
	{
		$this->fields = $fields;
	}

	/**
	 * @param integer $flag
	 */
	public function setFlag($flag)
	{
		$this->flag = $flag;
	}

	/**
	 * If the flag COLLECT_ERRORS is set this method returns all errors which 
	 * occured
	 *
	 * @return array
	 */
	public function getErrors()
	{
		return $this->errors;
	}

	/**
	 * Returns whether we have errors collected or not
	 *
	 * @return boolean
	 */
	public function isSuccessful()
	{
		return count($this->errors) == 0;
	}

	/**
	 * Returns an anonymous record based on the defined fields
	 *
	 * @return PSX\Data\RecordInterface
	 */
	public function getRecord()
	{
		$fields = array();

		foreach($this->fields as $property)
		{
			$fields[$property->getName()] = null;
		}

		return new Record('record', $fields);
	}

	/**
	 * Validates the given data against the defined field rules
	 *
	 * @param mixed $data
	 * @return mixed
	 */
	abstract public function validate($data);

	/**
	 * Returns the validated value or throws an exception. If the flag 
	 * COLLECT_ERRORS was set null gets returned on an invalid value
	 *
	 * @param PSX\Validate\Property $property
	 * @param mixed $value
	 * @return mixed
	 */
	protected function getPropertyValue(Property $property = null, $value, $key)
	{
		try
		{
			if($property !== null)
			{
				$result = $this->validate->apply($value, $property->getType(), $property->getFilters(), $property->getName(), $property->isRequired());

				// if we have no error and the value is not true the filter
				// has modified the value
				if($result !== true)
				{
					return $result;
				}
				else
				{
					return $value;
				}
			}
			else
			{
				$message = 'Field "' . $key . '" not defined';

				throw new ValidationException($message, $key, new Result(null, array($message)));
			}
		}
		catch(ValidationException $e)
		{
			if($this->flag == self::COLLECT_ERRORS)
			{
				$this->errors[$property->getName()] = $e->getResult();

				return null;
			}
			else
			{
				throw $e;
			}
		}
	}

	/**
	 * Returns the property defined by the name
	 *
	 * @param string $name
	 * @return PSX\Filter\Definition\Property
	 */
	protected function getProperty($name)
	{
		foreach($this->fields as $property)
		{
			if($property->getName() == $name)
			{
				return $property;
			}
		}

		return null;
	}

	/**
	 * Returns all available property names
	 *
	 * @return array
	 */
	protected function getPropertyNames()
	{
		$fields = array();

		foreach($this->fields as $property)
		{
			$fields[] = $property->getName();
		}

		return $fields;
	}
}
