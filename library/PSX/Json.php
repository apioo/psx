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

/**
 * This class is a wrapper to the json_encode / json_decode functions. Here an
 * simple example howto use it.
 * <code>
 * $json = Json::encode(array('foo' => 'bar'));
 * $php  = Json::decode($json);
 * </code>
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Json
{
	/**
	 * Returns the json encoded value as string of $value
	 *
	 * @param mixed $value
	 * @return string
	 */
	public static function encode($value)
	{
		return json_encode($value);
	}

	/**
	 * Returns a php variable from the json decoded value. Throws an exception 
	 * if decoding the data is not possible
	 *
	 * @param string $value
	 * @return mixed
	 */
	public static function decode($value)
	{
		$data = json_decode((string) $value, true);

		switch(json_last_error())
		{
			case JSON_ERROR_NONE:
				return $data;
				break;

			case JSON_ERROR_DEPTH:
				throw new Exception('Maximum stack depth exceeded');
				break;

			case JSON_ERROR_STATE_MISMATCH:
				throw new Exception('Underflow or the modes mismatch');
				break;

			case JSON_ERROR_CTRL_CHAR:
				throw new Exception('Unexpected control character found');
				break;

			case JSON_ERROR_SYNTAX:
				throw new Exception('Syntax error, malformed JSON');
				break;

			case JSON_ERROR_UTF8:
				throw new Exception('Malformed UTF-8 characters, possibly incorrectly encoded');
				break;

			default:
				throw new Exception('Unknown error');
				break;
		}
	}
}
