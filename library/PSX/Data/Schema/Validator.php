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
use PSX\Data\RecordInterface;
use PSX\Data\SchemaInterface;
use PSX\Data\Schema\Property;
use PSX\Data\Schema\PropertyInterface;

/**
 * Validator
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Validator implements ValidatorInterface
{
	public function validate(SchemaInterface $schema, $data)
	{
		$this->recValidate($schema->getDefinition(), $data);

		return true;
	}

	protected function recValidate(PropertyInterface $type, $data)
	{
		$type->validate($data);

		if($type instanceof Property\ComplexType)
		{
			$properties = $type->getProperties();

			foreach($properties as $name => $property)
			{
				if(isset($data[$name]))
				{
					$this->recValidate($property, $data[$name]);
				}
				else if($property->isRequired())
				{
					throw new ValidationException('Required property "' . $property->getName() . '" not available');
				}
			}
		}
		else if($type instanceof Property\ArrayType)
		{
			$prototype = $type->getPrototype();

			foreach($data as $value)
			{
				$this->recValidate($prototype, $value);
			}
		}
	}
}
