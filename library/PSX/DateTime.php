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

use DateTimeZone;
use DateInterval;

/**
 * DateTime
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class DateTime extends \DateTime
{
	const HTTP = 'D, d M Y H:i:s \G\M\T';
	const SQL  = 'Y-m-d H:i:s';

	public function __construct($time = 'now', DateTimeZone $timezone = null)
	{
		parent::__construct($time, $timezone);
	}

	public function __toString()
	{
		return parent::format(self::RFC3339);
	}

	/**
	 * Returns the seconds of an DateInterval recalculating years, months etc.
	 *
	 * @param DateInterval $interval
	 * @return integer
	 */
	public static function convertIntervalToSeconds(DateInterval $interval)
	{
		$map   = array(
			365 * 24 * 60 * 60, // year
			30 * 24 * 60 * 60, // month
			24 * 60 * 60, // day
			60 * 60, // hour
			60, // minute
			1, // second
		);
		$parts = explode('.', $interval->format('%y.%m.%d.%h.%i.%s'));
		$value = 0;

		foreach($parts as $key => $val)
		{
			$value+= $val * $map[$key];
		}

		return $value;
	}
}
