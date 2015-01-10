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
 * Conversion
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Conversion
{
	public static $bi = array(

		'Yi' => 1208925819614629174706176, # yobi
		'Zi' => 1180591620717411303424,    # zebi
		'Ei' => 1152921504606846976,       # exbi
		'Pi' => 1125899906842624,          # pebi
		'Ti' => 1099511627776,             # tebi
		'Gi' => 1073741824,                # gibi
		'Mi' => 1048576,                   # mebi
		'Ki' => 1024,                      # kibi

	);

	public static $si = array(

		'Y'  => 1000000000000000000000000, # Yotta
		'Z'  => 1000000000000000000000,    # Zetta
		'E'  => 1000000000000000000,       # Exa
		'P'  => 1000000000000000,          # Peta
		'T'  => 1000000000000,             # Tera
		'G'  => 1000000000,                # Giga
		'M'  => 1000000,                   # Mega
		'k'  => 1000,                      # Kilo
		'h'  => 100,                       # Hekto
		'da' => 10,                        # Deka
		''   => 1,                         # Unit
		'd'  => 0.1,                       # Dezi
		'c'  => 0.01,                      # Zenti
		'm'  => 0.001,                     # Milli
		'U'  => 0.000001,                  # Mikro
		'n'  => 0.000000001,               # Nano
		'p'  => 0.000000000001,            # Pico
		'f'  => 0.000000000000001,         # Femto
		'a'  => 0.000000000000000001,      # Atto
		'z'  => 0.000000000000000000001,   # Zepto
		'y'  => 0.000000000000000000000001 # Yocto

	);

	public static function byte($byte)
	{
		if($byte < 1000)
		{
			return $byte . ' byte';
		}
		else
		{
			return self::si('B', $byte);
		}
	}

	public static function meter($meter)
	{
		return self::si('m', $meter);
	}

	public static function gram($gram)
	{
		return self::si('g', $gram);
	}

	public static function seconds($seconds)
	{
		return self::si('s', $seconds);
	}

	public static function bi($byte, $decimalPlace = 2)
	{
		foreach(self::$bi as $u => $v)
		{
			if($byte >= $v)
			{
				$r = $byte / $v;

				return round($r, $decimalPlace) . ' ' . $u . 'bi';
			}
		}

		return $byte . ' byte';
	}

	public static function si($unit, $value, $decimalPlace = 2)
	{
		foreach(self::$si as $u => $v)
		{
			if($value >= $v)
			{
				$r = $value / $v;

				return round($r, $decimalPlace) . ' ' . $u . $unit;
			}
		}

		$r = $value / end($this->si);
		$u = key($this->si);

		return round($r, $decimalPlace) . ' ' . $u . $unit;
	}
}
