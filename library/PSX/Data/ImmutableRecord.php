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

namespace PSX\Data;

use BadMethodCallException;

/**
 * ImmutableRecord
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ImmutableRecord extends RecordAbstract
{
	protected $name;
	protected $fields;

	public function __construct($name = 'record', array $fields = array())
	{
		$this->name   = $name;
		$this->fields = $fields;
	}

	public function getRecordInfo()
	{
		return new RecordInfo($this->name, $this->fields);
	}

	public function __call($method, array $args)
	{
		$type = substr($method, 0, 3);
		$key  = lcfirst(substr($method, 3));

		if($type == 'get')
		{
			return isset($this->fields[$key]) ? $this->fields[$key] : null;
		}
		else
		{
			throw new BadMethodCallException('Invalid method call ' . $method);
		}
	}
}

