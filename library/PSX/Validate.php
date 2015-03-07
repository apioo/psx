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

namespace PSX;

use InvalidArgumentException;
use PSX\Validate\Result;
use PSX\Validate\ValidationException;

/**
 * This class offers methods to sanitize values that came from untrusted
 * sources
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Validate
{
	const TYPE_INTEGER = 'integer';
	const TYPE_STRING  = 'string';
	const TYPE_FLOAT   = 'float';
	const TYPE_BOOLEAN = 'boolean';
	const TYPE_ARRAY   = 'array';
	const TYPE_OBJECT  = 'object';

	/**
	 * Applies filter on the given value and returns the value on success or 
	 * throws an exception if an error occured
	 *
	 * @param string $value
	 * @param integer $type
	 * @param array<PSX\FilterAbstract> $filters
	 * @param string $title
	 * @param string $required
	 * @return mixed
	 */
	public function apply($value, $type = self::TYPE_STRING, array $filters = array(), $title = null, $required = true)
	{
		$result = $this->validate($value, $type, $filters, $title, $required);

		if($result->hasError())
		{
			throw new ValidationException($result->getFirstError(), $title, $result);
		}
		else if($result->isSuccessful())
		{
			return $result->getValue();
		}
	}

	/**
	 * Applies the $filter array containing PSX\FilterInterface on the $value. 
	 * Returns an result object which contains the value and error messages from 
	 * the filter. If $required is set to true an error will be added if $value 
	 * is null
	 *
	 * @param string $value
	 * @param integer $type
	 * @param array<PSX\FilterInterface> $filters
	 * @param string $title
	 * @param string $required
	 * @return PSX\Validate\Result
	 */
	public function validate($value, $type = self::TYPE_STRING, array $filters = array(), $title = null, $required = true)
	{
		$result = new Result();

		if($title === null)
		{
			$title = 'Unknown';
		}

		// we check whether the value is not null
		if($required === true && $value === null)
		{
			$result->addError(sprintf('The field "%s" is not set', $title));

			return $result;
		}

		if($value !== null)
		{
			switch($type)
			{
				case self::TYPE_INTEGER:
					$value = (int) $value;
					break;

				case self::TYPE_STRING:
					$value = (string) $value;
					break;

				case self::TYPE_FLOAT:
					$value = (float) $value;
					break;

				case self::TYPE_BOOLEAN:
					$value = (bool) $value;
					break;

				case self::TYPE_ARRAY:
					$value = (array) $value;
					break;

				case self::TYPE_OBJECT:
					if(!is_object($value))
					{
						throw new InvalidArgumentException('Value must be an object');
					}
					break;
			}
		}

		foreach($filters as $filter)
		{
			$error = null;

			if($filter instanceof FilterInterface)
			{
				$return = $filter->apply($value);
				$error  = $filter->getErrorMessage();
			}
			else if(is_callable($filter))
			{
				$return = call_user_func_array($filter, array($value));
			}
			else
			{
				throw new InvalidArgumentException('Filter must be either an callable or instanceof PSX\FilterInterface');
			}

			if($return === false)
			{
				if($required === true)
				{
					if($error === null)
					{
						$error = 'The field "%s" is not valid';
					}

					$result->addError(sprintf($error, $title));
				}

				return $result;
			}
			else if($return === true)
			{
				// the filter returns true so the validation was successful
			}
			else
			{
				$value = $return;
			}
		}

		$result->setValue($value);

		return $result;
	}
}
