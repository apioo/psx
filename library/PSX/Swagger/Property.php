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

namespace PSX\Swagger;

use InvalidArgumentException;
use PSX\Data\RecordAbstract;
use PSX\Data\RecordInfo;

/**
 * Property
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Property extends RecordAbstract
{
	const TYPE_INTEGER    = 'integer';
	const TYPE_NUMBER     = 'number';
	const TYPE_BOOLEAN    = 'boolean';
	const TYPE_STRING     = 'string';

	const FORMAT_INT32    = 'int32';
	const FORMAT_INT64    = 'int64';
	const FORMAT_FLOAT    = 'float';
	const FORMAT_DOUBLE   = 'double';
	const FORMAT_BYTE     = 'byte';
	const FORMAT_DATE     = 'date';
	const FORMAT_DATETIME = 'date-time';

	protected $id;
	protected $type;
	protected $format;
	protected $description;
	protected $defaultValue;
	protected $enum;
	protected $minimum;
	protected $maximum;
	protected $items;
	protected $uniqueItems;

	public function __construct($id = null, $type = null, $description = null)
	{
		$this->id          = $id;
		$this->description = $description;

		if($type !== null)
		{
			$this->setType($type);
		}
	}

	public function setId($id)
	{
		$this->id = $id;
	}
	
	public function getId()
	{
		return $this->id;
	}

	public function setType($type)
	{
		if(!in_array($type, array(self::TYPE_INTEGER, self::TYPE_NUMBER, self::TYPE_STRING, self::TYPE_BOOLEAN)))
		{
			throw new InvalidArgumentException('Type must be one of integer, number, string, boolean');
		}

		$this->type = $type;
	}
	
	public function getType()
	{
		return $this->type;
	}

	public function setFormat($format)
	{
		if(!in_array($format, array(self::FORMAT_INT32, self::FORMAT_INT64, self::FORMAT_FLOAT, self::FORMAT_DOUBLE, self::FORMAT_BYTE, self::FORMAT_DATE, self::FORMAT_DATETIME)))
		{
			throw new InvalidArgumentException('Type must be one of int32, int64, float, double, byte, date, date-time');
		}

		$this->format = $format;
	}

	public function getFormat()
	{
		return $this->format;
	}

	public function setDescription($description)
	{
		$this->description = $description;
	}
	
	public function getDescription()
	{
		return $this->description;
	}

	public function setDefaultValue($defaultValue)
	{
		$this->defaultValue = $defaultValue;
	}
	
	public function getDefaultValue()
	{
		return $this->defaultValue;
	}

	public function setEnum(array $enum)
	{
		$this->enum = $enum;
	}
	
	public function getEnum()
	{
		return $this->enum;
	}

	public function setMinimum($minimum)
	{
		$this->minimum = $minimum;
	}
	
	public function getMinimum()
	{
		return $this->minimum;
	}

	public function setMaximum($maximum)
	{
		$this->maximum = $maximum;
	}
	
	public function getMaximum()
	{
		return $this->maximum;
	}
}
