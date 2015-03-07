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

use DateTimeZone;
use DateInterval;

/**
 * DateTime
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
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
