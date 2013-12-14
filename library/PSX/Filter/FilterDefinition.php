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

namespace PSX\Filter;

use PSX\Data\RecordInterface;
use PSX\Filter\Definition\NotDefinedException;
use PSX\Filter\Definition\Property;
use PSX\Filter\Definition\ValidationException;
use PSX\Validate;

/**
 * Class wich validates record fields based on a set of property definitions. A 
 * property has a name, type and optional filters
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class FilterDefinition
{
	protected $fields;
	protected $validator;

	/**
	 * @param PSX\Validate $validator
	 * @param array<PSX\Filter\Definition\Property> $fields
	 */
	public function __construct(Validate $validator, array $fields = null)
	{
		$this->validator = $validator;
		$this->fields    = $fields;
	}

	/**
	 * @param PSX\Validate $validator
	 */
	public function setValidator(Validate $validator)
	{
		$this->validator = $validator;
	}

	/**
	 * @param array<PSX\Filter\Definition\Property $fields
	 */
	public function setFields(array $fields)
	{
		$this->fields = $fields;
	}

	/**
	 * Validates the given record against the defined field rules. If the record 
	 * has fields wich are not set in the definition an exception gets thrown
	 *
	 * @param PSX\Data\RecordInterface $record
	 * @return void
	 */
	public function validate(RecordInterface $record)
	{
		$data = $record->getRecordInfo()->getData();

		foreach($data as $key => $value)
		{
			$property = $this->getProperty($key);

			if($property instanceof Property)
			{
				$value = $this->validator->apply($data[$property->getName()], $property->getType(), $property->getFilters(), $key, ucfirst($key));

				if(!$this->validator->hasError())
				{
					// if we have no error and the value is not true the filter
					// has modified the value so we set it in the record
					if($value !== true)
					{
						$method = 'set' . ucfirst($key);

						if(method_exists($record, $method))
						{
							$record->$method($value);
						}
					}
				}
				else
				{
					throw new ValidationException($this->validator->getLastError());
				}
			}
			else
			{
				throw new NotDefinedException('Field ' . $key . ' not defined');
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
}
