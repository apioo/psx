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

namespace PSX\Handler;

use InvalidArgumentException;
use RuntimeException;

/**
 * MappingAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class MappingAbstract
{
	const ID_PROPERTY   = 0x10000000;

	const TYPE_INTEGER  = 0x100000;
	const TYPE_FLOAT    = 0x200000;
	const TYPE_STRING   = 0x300000;
	const TYPE_BOOLEAN  = 0x400000;
	const TYPE_DATETIME = 0x500000;

	/**
	 * @var array
	 */
	protected $fields;

	/**
	 * @var string
	 */
	protected $idProperty;

	public function __construct(array $fields)
	{
		$this->fields = $fields;
	}

	public function getFields()
	{
		return $this->fields;
	}

	public function getIdProperty()
	{
		if($this->idProperty === null)
		{
			return $this->getFirstColumnWithAttr(self::ID_PROPERTY);
		}
		else
		{
			return $this->idProperty;
		}
	}

	public function setIdProperty($idProperty)
	{
		if(in_array($idProperty, $this->fields))
		{
			$this->idProperty = $idProperty;
		}
		else
		{
			throw new InvalidArgumentException('Is not a valid field');
		}
	}

	public function getFirstColumnWithAttr($searchAttr)
	{
		foreach($this->fields as $column => $attr)
		{
			if($attr & $searchAttr)
			{
				return $column;
			}
		}

		throw new RuntimeException('No id property defined');
	}
}
