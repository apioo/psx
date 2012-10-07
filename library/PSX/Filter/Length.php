<?php
/*
 *  $Id: Length.php 643 2012-09-30 22:48:24Z k42b3.x@googlemail.com $
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
 * PSX_Filter_Length
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Filter
 * @version    $Revision: 643 $
 */
class PSX_Filter_Length extends PSX_FilterAbstract
{
	public function __construct($min = null, $max = null)
	{
		$this->min = $min;
		$this->max = $max;
	}

	/**
	 * If $value is an integer or float the $min and $max value is meaned as
	 * the current value. If it is a string it is meaned as the length of
	 * $value. If its an array $min and $max relate to the array size.
	 *
	 * @param mixed $value
	 * @return boolean
	 */
	public function apply($value)
	{
		if(is_int($value) || is_float($value))
		{
			return $this->compare($value);
		}
		elseif(is_array($value))
		{
			return $this->compare(count($value));
		}
		else
		{
			$value = (string) $value;

			return $this->compare(strlen($value));
		}

		return false;
	}

	public function getErrorMsg()
	{
		return '%s has an invalid length min ' . $this->min . ' and max ' . $this->max . ' signs';
	}

	private function compare($len)
	{
		if($this->min === null && $this->max === null)
		{
			return true;
		}
		else if($this->min !== null && $this->max === null)
		{
			return $len <= $this->min;
		}
		else if($this->min !== null && $this->max !== null)
		{
			return $len >= $this->min && $len <= $this->max;
		}
	}
}
