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

use PSX\Data\SchemaInterface;
use PSX\Data\Schema\GeneratorInterface;
use PSX\Data\Schema\Property;
use PSX\Data\Schema\PropertyInterface;

/**
 * JsonSchema
 *
 * @see     http://tools.ietf.org/html/draft-zyp-json-schema-04
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class JsonSchema implements GeneratorInterface
{
	protected $targetNamespace;

	public function __construct($targetNamespace)
	{
		$this->targetNamespace = $targetNamespace;
	}

	public function generate(SchemaInterface $schema)
	{
		return json_encode($this->generateRootElement($schema->getDefinition()));
	}

	protected function generateRootElement(Property\ComplexType $type)
	{
		$children   = $type->getChildren();
		$properties = array();
		$required   = array();

		foreach($children as $child)
		{
			$properties[$child->getName()] = $this->generateType($child);

			if($child->isRequired())
			{
				$required[] = $child->getName();
			}
		}

		$result = array(
			'id'         => $this->targetNamespace,
			'type'       => 'object',
			'properties' => $properties,
		);

		if(!empty($required))
		{
			$result['required'] = $required;
		}

		$result['additionalProperties'] = false;

		return $result;
	}

	protected function generateType(PropertyInterface $type)
	{
		if($type instanceof Property\ComplexType)
		{
			$children   = $type->getChildren();
			$properties = array();
			$required   = array();

			foreach($children as $child)
			{
				$properties[$child->getName()] = $this->generateType($child);

				if($child->isRequired())
				{
					$required[] = $child->getName();
				}
			}

			$result = array(
				'type'       => 'object',
				'properties' => $properties,
			);

			if(!empty($required))
			{
				$result['required'] = $required;
			}

			$result['additionalProperties'] = false;

			return $result;
		}
		else if($type instanceof Property\ArrayType)
		{
			$result = array(
				'type'  => 'array',
				'items' => $this->generateType($type->getPrototype()),
			);

			$minLength = $type->getMinLength();
			if($minLength)
			{
				$result['minItems'] = $minLength;
			}

			$maxLength = $type->getMaxLength();
			if($maxLength)
			{
				$result['maxItems'] = $maxLength;
			}

			return $result;
		}
		else
		{
			$result = array();
			$result['type'] = $this->getPropertyTypeName($type);

			if($type instanceof Property\String)
			{
				$minLength = $type->getMinLength();
				if($minLength)
				{
					$result['minLength'] = $minLength;
				}

				$maxLength = $type->getMaxLength();
				if($maxLength)
				{
					$result['maxLength'] = $maxLength;
				}

				$pattern = $type->getPattern();
				if($pattern)
				{
					$result['pattern'] = $pattern;
				}
			}
			else if($type instanceof Property\Decimal)
			{
				$min = $type->getMin();
				if($min)
				{
					$result['minimum'] = $min;
				}

				$max = $type->getMax();
				if($max)
				{
					$result['maximum'] = $max;
				}
			}

			$enumeration = $type->getEnumeration();
			if($enumeration)
			{
				$result['enum'] = $enumeration;
			}

			return $result;
		}
	}

	protected function getPropertyTypeName(PropertyInterface $type)
	{
		$parts = explode('\\', get_class($type));
		$end   = end($parts);

		switch($end)
		{
			case 'Float':
				return 'number';

			case 'Integer':
				return 'integer';

			case 'Boolean':
				return 'boolean';

			default:
				return 'string';
		}
	}
}
