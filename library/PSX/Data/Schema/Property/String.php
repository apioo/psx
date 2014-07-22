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

namespace PSX\Data\Schema\Property;

use PSX\Data\Schema\PropertyAbstract;
use PSX\Data\Schema\ValidationException;

/**
 * String
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class String extends PropertyAbstract
{
	protected $minLength;
	protected $maxLength;

	public function setMinLength($minLength)
	{
		$this->minLength = $minLength;

		return $this;
	}

	public function getMinLength()
	{
		return $this->minLength;
	}

	public function setMaxLength($maxLength)
	{
		$this->maxLength = $maxLength;

		return $this;
	}

	public function getMaxLength()
	{
		return $this->maxLength;
	}

	public function hasConstraints()
	{
		return parent::hasConstraints() || $this->minLength || $this->maxLength;
	}

	public function validate($data)
	{
		parent::validate($data);

		if($data === null)
		{
			return true;
		}

		// must be an string or an object which can be casted to an string
		if(is_string($data))
		{
		}
		else if(is_object($data) && method_exists($data, '__toString'))
		{
			$data = (string) $data;
		}
		else
		{
			throw new ValidationException($this->getName() . ' must be a string');
		}

		if($this->minLength !== null)
		{
			if(strlen($data) < $this->minLength)
			{
				throw new ValidationException($this->getName() . ' must contain more then ' . $this->minLength . ' characters');
			}
		}

		if($this->maxLength !== null)
		{
			if(strlen($data) > $this->maxLength)
			{
				throw new ValidationException($this->getName() . ' must contain less then ' . $this->maxLength . ' characters');
			}
		}

		return true;
	}
}
