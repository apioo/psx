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

namespace PSX\Filter;

use PSX\FilterAbstract;

/**
 * Ip
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Ip extends FilterAbstract
{
	/**
	 * Returns true if $value is a valid ip address and not in the private
	 * range else false. You can use this method as filter
	 *
	 * @param mixed $value
	 * @return boolean
	 */
	public function apply($value)
	{
		return filter_var((string) $value, FILTER_VALIDATE_IP);
	}

	public function getErrorMsg()
	{
		return '%s must be a valid ip format (80.132.95.75)';
	}
}

