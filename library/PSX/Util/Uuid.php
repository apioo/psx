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

/**
 * Util class to generate time based, pseudo random or name based UUIDs
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 * @see     http://www.ietf.org/rfc/rfc4122.txt
 */
class Uuid
{
	const V_1 = 0x1000;
	const V_2 = 0x2000;
	const V_3 = 0x3000;
	const V_4 = 0x4000;
	const V_5 = 0x5000;

	public static function timeBased()
	{
		return self::generate(self::V_1, sha1(microtime()));
	}

	public static function pseudoRandom()
	{
		return self::generate(self::V_4, sha1(uniqid(rand(), true)));
	}

	public static function nameBased($name)
	{
		return self::generate(self::V_5, sha1($name));
	}

	public static function generate($version, $hash)
	{
		$timeLow            = substr($hash, 0, 8);
		$timeMid            = substr($hash, 8, 4);
		$timeHighVersion    = dechex(hexdec(substr($hash, 12, 4)) & 0x0FFF | $version);
		$clockSeqHiReserved = dechex(hexdec(substr($hash, 16, 2)) & 077 | 0200);
		$clockSeqLow        = substr($hash, 17, 2);
		$node               = substr($hash, 18, 12);

		return $timeLow . '-' . $timeMid . '-' . $timeHighVersion . '-' . $clockSeqHiReserved . $clockSeqLow . '-' . $node;
	}
}

