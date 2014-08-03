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

namespace PSX\Data\Schema\Generator;

use PSX\Data\Record;
use PSX\Data\SchemaInterface;
use PSX\Data\Schema\GeneratorInterface;
use PSX\Data\Schema\Property;
use PSX\Data\Schema\PropertyInterface;
use PSX\Data\Schema\PropertySimpleAbstract;
use PSX\Data\Writer;
use RuntimeException;

/**
 * Generates an json or xml sample request which can be used for documentation 
 * purpose
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Sample implements GeneratorInterface
{
	const FORMAT_JSON = 0;
	const FORMAT_XML  = 1;

	protected $format;
	protected $data;

	public function __construct($format = self::FORMAT_JSON)
	{
		$this->format = $format;
	}

	public function setData(array $data)
	{
		$this->data = $data;
	}

	public function generate(SchemaInterface $schema)
	{
		$record = $this->generateType($schema->getDefinition(), $this->data);

		switch($this->format)
		{
			case self::FORMAT_XML:
				$writer = new Writer\Xml();
				break;

			case self::FORMAT_JSON:
			default:
				$writer = new Writer\Json();
				break;
		}

		return $writer->write($record);
	}

	protected function generateType(PropertyInterface $type, $data)
	{
		if($type instanceof Property\ComplexType)
		{
			$fields = array();

			foreach($type->getChildren() as $child)
			{
				if(isset($data[$child->getName()]))
				{
					$fields[$child->getName()] = $this->generateType($child, $data[$child->getName()]);
				}
				else if($child->isRequired())
				{
					throw new RuntimeException('Missing sample data of required property ' . $child->getName());
				}
			}

			return new Record($type->getName(), $fields);
		}
		else if($type instanceof Property\ArrayType)
		{
			if(is_array($data))
			{
				$values = array();
				foreach($data as $value)
				{
					$values[] = $this->generateType($type->getPrototype(), $value);
				}

				return $values;
			}
			else if($type->isRequired())
			{
				throw new RuntimeException('Missing sample data of required property ' . $type->getName());
			}
		}
		else if($type instanceof Property\Boolean)
		{
			return (bool) $data;
		}
		else if($type instanceof Property\Integer)
		{
			return (int) $data;
		}
		else if($type instanceof Property\Float)
		{
			return (float) $data;
		}
		else
		{
			return (string) $data;
		}
	}
}
