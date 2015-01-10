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
use PSX\Exception;
use PSX\Util\Annotation;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use Serializable;

/**
 * RecordAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class RecordAbstract implements RecordInterface, Serializable
{
	public function getRecordInfo()
	{
		$parts  = explode('\\', get_class($this));
		$name   = lcfirst(end($parts));
		$vars   = get_object_vars($this);
		$fields = array();

		foreach($vars as $k => $v)
		{
			if($k[0] != '_')
			{
				$fields[$k] = $this->$k;
			}
		}

		return new RecordInfo($name, $fields);
	}

	public function serialize()
	{
		$vars = get_object_vars($this);
		$data = array();

		foreach($vars as $k => $v)
		{
			if($k[0] != '_')
			{
				$data[$k] = $this->$k;
			}
		}

		return serialize($data);
	}

	public function unserialize($data)
	{
		$data = unserialize($data);

		if(is_array($data))
		{
			foreach($data as $k => $v)
			{
				$this->$k = $v;
			}
		}
	}
}

