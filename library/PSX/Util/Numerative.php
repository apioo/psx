<?php
/*
 *  $Id: Numerative.php 480 2012-05-01 18:13:54Z k42b3.x@googlemail.com $
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

namespace PSX\Util;

use UnexpectedValueException;

/**
 * PSX_Util_Numerative
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Util
 * @version    $Revision: 480 $
 */
class Numerative
{
	const BIN = 0x1;
	const OCT = 0x2;
	const DEC = 0x3;
	const HEX = 0x4;

	public static $systems = array(

		self::BIN => array(0 => '0', 1 => '1'),
		self::OCT => array(0 => '0', 1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5', 6 => '6', 7 => '7'),
		self::DEC => array(0 => '0', 1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5', 6 => '6', 7 => '7', 8 => '8', 9 => '9'),
		self::HEX => array(0 => '0', 1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5', 6 => '6', 7 => '7', 8 => '8', 9 => '9', 10 => 'A', 11 => 'B', 12 => 'C', 13 => 'D', 14 => 'E', 15 => 'F'),

	);

	public static function bin2oct($bin)
	{
		return self::decToX(self::OCT, self::xToDec(self::BIN, $bin));
	}

	public static function bin2dez($bin)
	{
		return self::decToX(self::DEC, self::xToDec(self::BIN, $bin));
	}

	public static function bin2hex($bin)
	{
		return self::decToX(self::HEX, self::xToDec(self::BIN, $bin));
	}

	public static function oct2bin($oct)
	{
		return self::decToX(self::BIN, self::xToDec(self::OCT, $oct));
	}

	public static function oct2dez($oct)
	{
		return self::decToX(self::DEC, self::xToDec(self::OCT, $oct));
	}

	public static function oct2hex($oct)
	{
		return self::decToX(self::HEX, self::xToDec(self::OCT, $oct));
	}

	public static function dez2bin($dez)
	{
		return self::decToX(self::BIN, $dez);
	}

	public static function dez2oct($dez)
	{
		return self::decToX(self::OCT, $dez);
	}

	public static function dez2hex($dez)
	{
		return self::decToX(self::HEX, $dez);
	}

	public static function hex2bin($hex)
	{
		return self::decToX(self::BIN, self::xToDec(self::HEX, $hex));
	}

	public static function hex2oct($hex)
	{
		return self::decToX(self::OCT, self::xToDec(self::HEX, $hex));
	}

	public static function hex2dez($hex)
	{
		return self::decToX(self::DEC, self::xToDec(self::HEX, $hex));
	}

	public static function xToDec($system, $x)
	{
		if(!isset(self::$systems[$system]))
		{
			throw new UnexpectedValueException('Invalid numerative system');
		}
		else
		{
			$d = '';
			$n = array_flip(self::$systems[$system]);
			$x = strrev(strval($x));
			$c = 1;

			for($i = 0; $i < strlen($x); $i++)
			{
				$d+= $n[$x{$i}] * $c;

				$c*= count($n);
			}

			return $d;
		}
	}

	public static function decToX($system, $dez)
	{
		if(!isset(self::$systems[$system]))
		{
			throw new UnexpectedValueException('Invalid numerative system');
		}
		else
		{
			$n = self::$systems[$system];
			$d = intval($dez);
			$b = count($n);
			$x = '';

			if($d == 0)
			{
				return $n[0];
			}

			if($d < 0)
			{
				throw new UnexpectedValueException('Cant convert negative numbers because we have no sign to display negative values');
			}

			while($d > 0)
			{
				$x.= $n[intval($d % $b)];
				$d = intval($d / $b);
			}

			return strrev($x);
		}
	}
}