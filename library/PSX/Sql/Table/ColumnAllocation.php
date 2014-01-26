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

namespace PSX\Sql\Table;

use UnexpectedValueException;

/**
 * Some implementations needs to know the name of specific columns i.e. the sql 
 * cache handler needs to know the data or date column. This class contains
 * such informations
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ColumnAllocation
{
	protected $columns;

	public function __construct(array $columns = array())
	{
		$this->columns = $columns;
	}

	public function set($key, $name)
	{
		$this->columns[$key] = $name;
	}

	public function get($key)
	{
		$name = isset($this->columns[$key]) ? $this->columns[$key] : null;

		if(!empty($name))
		{
			return $name;
		}
		else
		{
			throw new UnexpectedValueException('Missing column allocation');
		}
	}
}
