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

namespace PSX\Data;

use PSX\Validate;

/**
 * Accessor
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Accessor
{
	protected $validate;
	protected $source;

	public function __construct(Validate $validate, array $source)
	{
		$this->validate = $validate;
		$this->source   = $source;
	}

	public function getSource()
	{
		return $this->source;
	}

	public function get($key, $type = Validate::TYPE_STRING, array $filter = array())
	{
		$parts = explode('.', $key);
		$value = $this->searchArray($parts, $this->source);

		return $this->validate->apply($value, $type, $filter, $key);
	}

	protected function searchArray(array $parts, array $value)
	{
		foreach($parts as $part)
		{
			if(is_array($value))
			{
				$value = isset($value[$part]) ? $value[$part] : null;
			}
			else
			{
				$value = null;
			}

			if($value === null)
			{
				break;
			}
		}

		return $value;
	}
}
