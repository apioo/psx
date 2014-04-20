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

use ArrayObject;

/**
 * This class offers methods to sanitize values that came from untrusted 
 * sources. The method validate returns the value if the validation was 
 * successful or false if it fails. You can cast the variable to string, 
 * integer, float or boolean. Optional you can pass an array of filters wich 
 * validates the value. Here some examples:
 * <code>
 * $validator = new Validate();
 *
 * // we use the length and email filter
 * $value = $validator->apply($input, Validate::TYPE_STRING, array(new Filter\Length(3, 32), new Filter\Email()));
 *
 * // we use the regular expression filter. The value must contain the string
 * // "php"
 * $value = $validator->apply($input, Validate::TYPE_STRING, array(new Filter\Regexp('/php/')));
 *
 * // we use the length filter and the value must be an interger wich is min 10
 * // and max 100
 * $value = $validator->apply($input, Validate::TYPE_INTEGER, array(new Filter\Length(10, 100)));
 * </code>
 *
 * If you have validate the values you can check whether their was an error.
 * <code>
 * if($validator->hasError())
 * {
 * 	echo 'The following errors occured:' . "\n";
 * 	echo implode("\n", $validator->getError());
 * }
 * else
 * {
 * 	echo 'No validation error occured';
 * }
 * </code>
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Validate
{
	const TYPE_INTEGER = 'integer';
	const TYPE_STRING  = 'string';
	const TYPE_FLOAT   = 'float';
	const TYPE_BOOLEAN = 'boolean';

	protected $error;

	public function __construct()
	{
		$this->error = array();
	}

	/**
	 * Applies the $filter array containing PSX\FilterAbstract on the $value. If
	 * an error occurs $returnValue is returned else the $value is returned.
	 * Note each filter can manipulate the $value. If $required is set to true
	 * an error will be added if $value is false.
	 *
	 * @param string $value
	 * @param integer $type
	 * @param array<PSX\FilterAbstract> $filters
	 * @param string $key
	 * @param string $title
	 * @param string $required
	 * @return false|$returnValue
	 */
	public function apply($value, $type = self::TYPE_STRING, array $filters = array(), $key = null, $title = null, $required = true, $returnValue = false)
	{
		if($key !== null && $title === null)
		{
			$title = ucfirst($key);
		}
		else if($title === null)
		{
			$title = 'Unknown';
		}

		// we check for $value === false because the input container returns 
		// explicit false if the value is not set
		if($required === true && $value === false)
		{
			$this->addError($key, sprintf('The field "%s" is not set', $title));

			return $returnValue;
		}

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
		}

		foreach($filters as $filter)
		{
			$return = $filter->apply($value);

			if($return === false)
			{
				if($required === true)
				{
					$errorMessage = $filter->getErrorMsg();

					if($errorMessage === null)
					{
						$errorMessage = 'The field "%s" is not valid';
					}

					$this->addError($key, sprintf($errorMessage, $title));
				}

				return $returnValue;
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

		return $value;
	}

	public function addError($key, $msg)
	{
		if($key === null)
		{
			$this->error[] = $msg;
		}
		else
		{
			$this->error[$key] = $msg;
		}
	}

	public function hasError()
	{
		return count($this->error) > 0;
	}

	public function getError($key = null)
	{
		if($key === null)
		{
			return $this->error;
		}
		else
		{
			return isset($this->error[$key]) ? $this->error[$key] : null;
		}
	}

	public function getLastError()
	{
		return end($this->error);
	}

	public function clearError()
	{
		$this->error = array();
	}
}
