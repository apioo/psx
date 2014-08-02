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

namespace PSX\Data\Record\Importer;

use InvalidArgumentException;
use PSX\Data\Record as DataRecord;
use PSX\Data\Record\ImporterInterface;
use PSX\Data\SchemaInterface;
use PSX\Data\Schema\Property;
use PSX\Data\Schema\PropertyInterface;
use PSX\Data\Schema\ValidatorInterface;

/**
 * Imports data based on an given schema. Validates also the data if an 
 * validator was set
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Schema implements ImporterInterface
{
	protected $validator;

	public function __construct(ValidatorInterface $validator)
	{
		$this->validator = $validator;
	}

	public function accept($schema)
	{
		return $schema instanceof SchemaInterface;
	}

	public function import($schema, $data)
	{
		if(!$schema instanceof SchemaInterface)
		{
			throw new InvalidArgumentException('Schema must be an instanceof PSX\Data\SchemaInterface');
		}

		if(!is_array($data))
		{
			throw new InvalidArgumentException('Data must be an array');
		}

		$this->validator->validate($schema, $data);

		return $this->recImport($schema->getDefinition(), $data);
	}

	protected function recImport(PropertyInterface $type, $data)
	{
		if($type instanceof Property\ComplexType)
		{
			$children = $type->getChildren();
			$fields   = array();

			foreach($children as $child)
			{
				if(isset($data[$child->getName()]))
				{
					$fields[$child->getName()] = $this->recImport($child, $data[$child->getName()]);
				}
			}

			return new DataRecord($type->getName(), $fields);
		}
		else if($type instanceof Property\ArrayType)
		{
			$prototype = $type->getPrototype();
			$values    = array();

			foreach($data as $value)
			{
				$values[] = $this->recImport($prototype, $value);
			}

			return $values;
		}
		else if($type instanceof Property\Boolean)
		{
			return $data === 'false' ? false : (bool) $data;
		}
		else if($type instanceof Property\Date || $type instanceof Property\DateTime)
		{
			return new \DateTime($data);
		}
		else if($type instanceof Property\Duration)
		{
			return new \DateInterval($data);
		}
		else if($type instanceof Property\Float)
		{
			return (float) $data;
		}
		else if($type instanceof Property\Integer)
		{
			return (int) $data;
		}
		else
		{
			return (string) $data;
		}
	}
}
