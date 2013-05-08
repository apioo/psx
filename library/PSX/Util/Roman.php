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

namespace PSX\Util;

use UnexpectedValueException;

/**
 * Roman
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Roman
{
	public static $rm = array(

		1000 => 'M',
		900  => 'CM',
		500  => 'D',
		400  => 'CD',
		100  => 'C',
		90   => 'XC',
		40   => 'XL',
		50   => 'L',
		10   => 'X',
		9    => 'IX',
		5    => 'V',
		4    => 'IV',
		1    => 'I',

	);

	public static function encode($decimal)
	{
		$decimal = intval($decimal);
		$result  = 0;
		$numbers = array();

		if($decimal <= 0)
		{
			throw new UnexpectedValueException('Number must be an integer greater zero');
		}
		else
		{
			while($result != $decimal)
			{
				if($result < $decimal)
				{
					foreach(self::$rm as $k => $v)
					{
						if(($result + $k) <= $decimal)
						{
							$numbers[] = $k;

							$result+= $k;

							continue(2);
						}
					}
				}
				else
				{
					break;
				}

				$result = array_sum($numbers);
			}

			$roman = '';

			foreach($numbers as $v)
			{
				$roman.= self::$rm[$v];
			}

			return $roman;
		}
	}

	public static function decode($roman)
	{
		$rm     = array_flip(self::$rm);
		$result = 0;
		$roman  = strval($roman);
		$len    = strlen($roman);

		for($i = 0; $i < $len; $i++)
		{
			$v = $roman[$i];

			if(!array_key_exists($v, $rm))
			{
				throw new UnexpectedValueException('Invalid roman number');
			}
			else
			{
				if(isset($roman[$i + 1]))
				{
					$t = $roman[$i + 1];

					if($rm[$t] > $rm[$v])
					{
						$result+= $rm[$t] - $rm[$v];
						$i+= 1;
					}
					else
					{
						$result+= $rm[$v];
					}
				}
				else
				{
					$result+= $rm[$v];
				}
			}
		}

		return $result;
	}
}