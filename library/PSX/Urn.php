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

namespace PSX;

use InvalidArgumentException;

/**
 * Represents an URN. This class exists mostly to express in your code that 
 * you expect/return an URN. Also the value must have "urn" as scheme else an 
 * exception gets thrown
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 * @see     http://www.ietf.org/rfc/rfc2141.txt
 */
class Urn extends Uri
{
	/**
	 * Returns the NID (Namespace Identifier)
	 *
	 * @return string
	 */
	public function getNid()
	{
		return strstr($this->path, ':', true);
	}

	/**
	 * Returns the NSS (Namespace Specific String)
	 *
	 * @return string
	 */
	public function getNss()
	{
		return substr(strstr($this->path, ':'), 1);
	}

	protected function parse($urn)
	{
		// URNs are case insensitive
		$urn = strtolower((string) $urn);

		parent::parse($urn);

		// must have an urn scheme and path part
		if($this->scheme != 'urn' || empty($this->path))
		{
			throw new InvalidArgumentException('Invalid urn syntax');
		}
	}
}
