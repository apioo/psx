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

namespace PSX\Util;

/**
 * CurveArray
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class CurveArray
{
	/**
	 * Converts an flat array into a nested using an seperator
	 *
	 * @param array $data
	 * @param string $seperator
	 * @return array
	 */
	public static function nest(array $data, $seperator = '_')
	{
		$result = array();

		foreach($data as $key => $value)
		{
			if(($pos = strpos($key, $seperator)) !== false)
			{
				$subKey = substr($key, 0, $pos);
				$name   = substr($key, $pos + 1);

				if(!isset($result[$subKey]))
				{
					$result[$subKey] = self::nest(self::getParts($data, $subKey . $seperator), $seperator);
				}
			}
			else
			{
				if(is_array($value))
				{
					$result[$key] = self::nest($value);
				}
				else
				{
					$result[$key] = $value;
				}
			}
		}

		return $result;
	}

	/**
	 * Converts an nested array into a flat using an seperator. The prefix and 
	 * result parameter are used internally for performance reason and should 
	 * not be used
	 *
	 * @param array $data
	 * @param string $seperator
	 * @return array
	 */
	public static function flatten(array $data, $seperator = '_', $prefix = null, array &$result = null)
	{
		if($result === null)
		{
			$result = array();
		}

		foreach($data as $key => $value)
		{
			if(is_array($value))
			{
				self::flatten($value, $seperator, $prefix . $key . $seperator, $result);
			}
			else
			{
				$result[$prefix . $key] = $value;
			}
		}

		return $result;
	}

	/**
	 * Returns whether an array is index based or associative
	 *
	 * @param array $array
	 * @return boolean
	 */
	public static function isAssoc(array $array)
	{
		if(isset($array[0]))
		{
			$n = count($array) - 1;

			return array_sum(array_keys($array)) != ($n * ($n + 1)) / 2;
		}
		else
		{
			return true;
		}
	}

	protected static function getParts(array $data, $prefix)
	{
		$result = array();

		foreach($data as $key => $value)
		{
			if(substr($key, 0, strlen($prefix)) == $prefix)
			{
				$name = substr($key, strlen($prefix));

				if(!empty($name))
				{
					$result[$name] = $value;
				}
			}
		}

		return $result;
	}
}
