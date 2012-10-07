<?php
/*
 *  $Id: Json.php 606 2012-08-25 11:11:48Z k42b3.x@googlemail.com $
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
 * This class is a wrapper to the json_encode / json_decode functions. Here an
 * simple example howto use it.
 * <code>
 * $json = PSX_Json::encode(array('foo' => 'bar'));
 *
 * $php  = PSX_Json::decode($json);
 * </code>
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Json
 * @version    $Revision: 606 $
 */
class PSX_Json
{
	/**
	 * Returns the json encoded value as string of $value
	 *
	 * @param string $value
	 * @return string
	 */
	public static function encode($value)
	{
		return json_encode($value);
	}

	/**
	 * Returns a php variable from the json decoded value
	 *
	 * @param string $value
	 * @return mixed
	 */
	public static function decode($value)
	{
		return json_decode($value, true);
	}
}
