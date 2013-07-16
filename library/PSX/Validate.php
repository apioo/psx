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

namespace PSX;

use ArrayObject;

/**
 * This class offers methods to sanitize values that came from untrusted sources
 * The apply methods is the heart of this class here an example how you can
 * easily validate data.
 * <code>
 * $validator = new Validate();
 * $value     = $validator->apply($input, 'string', array(new Filter\Length(3, 8)));
 *
 * if($value === false)
 * {
 * 	// validation fails
 * }
 * else
 * {
 * 	// the variable $value is an string with min 3 and max 8 chars. The
 * 	// method validate cast the variable to a string
 * }
 * </code>
 *
 * The method validate returns the value if the validation was successful or
 * false if it fails. You can cast the variable to string, integer, float or
 * boolean. Optional you can pass an array of filters wich validates the value.
 * Here some examples:
 * <code>
 * $validator = new Validate();
 *
 * // we use the length and email filter
 * $value = $validator->apply($input, 'string', array(new Filter\Length(3, 32), new Filter\Email()));
 *
 * // we use the regular expression filter. The value must contain the string
 * // "php"
 * $value = $validator->apply($input, 'string', array(new Filter\Regexp('/php/')));
 *
 * // we us the length filter and the value must be an interger wich is min 10
 * // and max 100
 * $value = $validator->apply($input, 'integer', array(new Filter\Length(10, 100)));
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
class Validate extends ArrayObject
{
	private $error;

	public function __construct()
	{
		parent::__construct($this->error = array());
	}

	/**
	 * Applies the $filter array containing PSX\FilterAbstract on the $value. If
	 * an error occurs $returnValue is returned else the $value is returned.
	 * Note each filter can manipulate the $value. If $required is set to true
	 * an error will be added if $value is false.
	 *
	 * @param string $value
	 * @param string $type
	 * @param array<PSX\FilterAbstract> $filter
	 * @param string $key
	 * @param string $title
	 * @param string $required
	 * @return false|$returnValue
	 */
	public function apply($value, $type = 'string', array $filter = array(), $key = null, $title = null, $required = true, $returnValue = false)
	{
		$title = $title === null ? $key : $title;

		if($required === true && $value === false)
		{
			$this->addError($key, $title . ' not set');

			return $returnValue;
		}

		switch($type)
		{
			case 'int':
			case 'integer':
				$value = (integer) $value;
				break;

			case 'str':
			case 'string':
				$value = (string)  $value;
				break;

			case 'double':
			case 'float':
				$value = (float)   $value;
				break;

			case 'bool':
			case 'boolean':
				$value = (boolean) $value;
				break;
		}

		foreach($filter as $f)
		{
			$return = $f->apply($value);

			if($return === false)
			{
				if($required === true)
				{
					$this->addError($key, sprintf($f->getErrorMsg(), $title));
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
		$this->offsetSet($key, $msg);
	}

	public function hasError()
	{
		return $this->count() > 0;
	}

	public function getError()
	{
		return $this->getArrayCopy();
	}

	public function getLastError()
	{
		$error = $this->getError();

		return end($error);
	}

	public function clearError()
	{
		$this->exchangeArray($this->error = array());
	}
}
