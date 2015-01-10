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

namespace PSX\Data\Schema;

/**
 * PropertySimpleAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class PropertySimpleAbstract extends PropertyAbstract
{
	protected $pattern;
	protected $enumeration;

	/**
	 * @param string $pattern
	 * @return $this
	 */
	public function setPattern($pattern)
	{
		$this->pattern = $pattern;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getPattern()
	{
		return $this->pattern;
	}

	/**
	 * @param string $enumeration
	 * @return $this
	 */
	public function setEnumeration(array $enumeration)
	{
		$this->enumeration = $enumeration;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getEnumeration()
	{
		return $this->enumeration;
	}

	/**
	 * @return boolean
	 */
	public function validate($data)
	{
		parent::validate($data);

		if($data === null)
		{
			return true;
		}

		if($this->pattern !== null)
		{
			$result = preg_match('/^(' . $this->pattern . '){1}$/', $data);

			if(!$result)
			{
				throw new ValidationException($this->getName() . ' does not match pattern');
			}
		}

		if($this->enumeration !== null)
		{
			if(!in_array($data, $this->enumeration))
			{
				throw new ValidationException($this->getName() . ' is not in enumeration');
			}
		}
	}
}
