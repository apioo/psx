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

use InvalidArgumentException;
use PSX\Data\Record;
use PSX\Data\RecordInterface;
use PSX\Data\SchemaInterface;
use PSX\Data\Schema\Property;
use PSX\Data\Schema\PropertyInterface;

/**
 * Assimilator
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Assimilator
{
	/**
	 * Takes an array and fits it accoring to the specification. Removes all 
	 * unknown keys. If an value doesnt fit or an required parameter is missing 
	 * it throws an exception
	 *
	 * @param array $data
	 * @param PSX\Data\SchemaInterface $schema
	 * @return array
	 */
	public function assimilate(SchemaInterface $schema, $data)
	{
		return $this->recAssimilate($schema->getDefinition(), $data);
	}

	protected function recAssimilate(PropertyInterface $type, $data)
	{
		if($type instanceof Property\ComplexType)
		{
			if($data instanceof RecordInterface)
			{
				$data = $data->getRecordInfo()->getData();
			}

			if(!is_array($data))
			{
				throw new InvalidArgumentException('Value of ' . $type->getName() . ' must be an array');
			}

			$properties = $type->getProperties();
			$result     = array();

			foreach($properties as $name => $property)
			{
				if(isset($data[$name]))
				{
					$result[$name] = $this->recAssimilate($property, $data[$name]);
				}
				else if($property->isRequired())
				{
					throw new InvalidArgumentException('Required parameter ' . $property->getName() . ' is missing');
				}
			}

			return new Record($type->getName(), $result);
		}
		else if($type instanceof Property\ArrayType)
		{
			if(!is_array($data))
			{
				throw new InvalidArgumentException('Value of ' . $type->getName() . ' must be an array');
			}

			$prototype = $type->getPrototype();
			$result    = array();

			foreach($data as $value)
			{
				$result[] = $this->recAssimilate($prototype, $value);
			}

			return $result;
		}
		else if($type instanceof Property\Integer)
		{
			return (int) $data;
		}
		else if($type instanceof Property\Float)
		{
			return (float) $data;
		}
		else if($type instanceof Property\Boolean)
		{
			return (bool) $data;
		}
		else if($type instanceof Property\DateTime || $type instanceof Property\Date)
		{
			return $data instanceof \DateTime ? $data : new \DateTime($data);
		}
		else
		{
			return (string) $data;
		}
	}
}
