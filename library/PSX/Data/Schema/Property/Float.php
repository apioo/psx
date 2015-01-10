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

namespace PSX\Data\Schema\Property;

use PSX\Data\Schema\ValidationException;

/**
 * Float
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Float extends Decimal
{
	public function validate($data)
	{
		parent::validate($data);

		if($data === null)
		{
			return true;
		}

		if(is_float($data))
		{
		}
		else if(is_int($data))
		{
			$data = (float) $data;
		}
		else if(is_string($data))
		{
			$result = preg_match('/^((\+|-)?([0-9]+(\.[0-9]*)?|\.[0-9]+)([Ee](\+|-)?[0-9]+)?){1}$/', $data);

			if($result)
			{
				$data = (float) $data;
			}
			else
			{
				throw new ValidationException($this->getName() . ' must be an float');
			}
		}
		else
		{
			throw new ValidationException($this->getName() . ' must be an float');
		}

		if($this->max !== null)
		{
			if($data > $this->max)
			{
				throw new ValidationException($this->getName() . ' must be lower or equal then ' . $this->max);
			}
		}

		if($this->min !== null)
		{
			if($data < $this->min)
			{
				throw new ValidationException($this->getName() . ' must be greater or equal then ' . $this->min);
			}
		}

		return true;
	}
}
