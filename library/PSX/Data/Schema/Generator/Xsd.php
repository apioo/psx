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

namespace PSX\Data\Schema\Generator;

use PSX\Data\SchemaInterface;
use PSX\Data\Schema\GeneratorInterface;
use PSX\Data\Schema\Property;
use PSX\Data\Schema\PropertyInterface;
use PSX\Data\Schema\PropertySimpleAbstract;

/**
 * Xsd
 *
 * @see     http://www.w3.org/XML/Schema
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Xsd implements GeneratorInterface
{
	protected $writer;
	protected $targetNamespace;

	private $_types = array();

	public function __construct($targetNamespace)
	{
		$this->writer = new \XMLWriter();
		$this->writer->openMemory();

		$this->targetNamespace = $targetNamespace;
	}

	public function generate(SchemaInterface $schema)
	{
		$this->writer->startDocument('1.0', 'UTF-8');
		$this->writer->startElement('xs:schema');
		$this->writer->writeAttribute('xmlns:xs', 'http://www.w3.org/2001/XMLSchema');
		$this->writer->writeAttribute('xmlns:tns', $this->targetNamespace);
		$this->writer->writeAttribute('targetNamespace', $this->targetNamespace);
		$this->writer->writeAttribute('elementFormDefault', 'qualified');

		// generate elements
		$this->generateRootElement($schema->getDefinition());

		$this->writer->endElement();
		$this->writer->endDocument();

		return $this->writer->outputMemory(true);
	}

	protected function generateRootElement(Property\ComplexType $type)
	{
		$this->writer->startElement('xs:element');
		$this->writer->writeAttribute('name', $type->getName());
		$this->writer->startElement('xs:complexType');
		$this->writer->startElement('xs:sequence');

		$children = $type->getChildren();

		foreach($children as $child)
		{
			if($child instanceof Property\ArrayType)
			{
				$this->writer->startElement('xs:element');
				$this->writer->writeAttribute('name', $child->getName());
				$this->writer->writeAttribute('type', $this->getPropertyTypeName($child->getPrototype(), true));

				$this->generateTypeArray($child);

				$this->writer->endElement();
			}
			else
			{
				$this->writer->startElement('xs:element');
				$this->writer->writeAttribute('name', $child->getName());
				$this->writer->writeAttribute('type', $this->getPropertyTypeName($child, true));
				$this->writer->writeAttribute('minOccurs', $child->isRequired() ? 1 : 0);
				$this->writer->writeAttribute('maxOccurs', 1);
				$this->writer->endElement();
			}
		}

		$this->writer->endElement();
		$this->writer->endElement();
		$this->writer->endElement();

		foreach($children as $child)
		{
			if($child instanceof Property\ArrayType)
			{
				$child = $child->getPrototype();
			}

			if($this->hasConstraints($child))
			{
				$this->generateType($child);
			}
		}
	}

	protected function generateType(PropertyInterface $type)
	{
		$typeName = $this->getPropertyTypeName($type);

		if(in_array($typeName, $this->_types))
		{
			return;
		}

		$this->_types[] = $typeName;

		if($type instanceof Property\ComplexType)
		{
			$this->writer->startElement('xs:complexType');
			$this->writer->writeAttribute('name', $typeName);
			$this->writer->startElement('xs:sequence');

			$children = $type->getChildren();

			foreach($children as $child)
			{
				if($child instanceof Property\ArrayType)
				{
					$this->writer->startElement('xs:element');
					$this->writer->writeAttribute('name', $child->getName());
					$this->writer->writeAttribute('type', $this->getPropertyTypeName($child->getPrototype(), true));

					$this->generateTypeArray($child);

					$this->writer->endElement();
				}
				else
				{
					$this->writer->startElement('xs:element');
					$this->writer->writeAttribute('name', $child->getName());
					$this->writer->writeAttribute('type', $this->getPropertyTypeName($child, true));
					$this->writer->writeAttribute('minOccurs', $child->isRequired() ? 1 : 0);
					$this->writer->writeAttribute('maxOccurs', 1);
					$this->writer->endElement();
				}
			}

			$this->writer->endElement();
			$this->writer->endElement();

			foreach($children as $child)
			{
				if($child instanceof Property\ArrayType)
				{
					$child = $child->getPrototype();
				}

				if($this->hasConstraints($child))
				{
					$this->generateType($child);
				}
			}
		}
		else if($type instanceof Property\ArrayType)
		{
			$this->writer->startElement('xs:complexType');
			$this->writer->writeAttribute('name', $typeName);
			$this->writer->startElement('xs:sequence');

			$prototype = $type->getPrototype();

			$this->writer->startElement('xs:element');
			$this->writer->writeAttribute('name', $prototype->getName());
			$this->writer->writeAttribute('type', $this->getPropertyTypeName($prototype, true));

			$this->generateTypeArray($type);

			$this->writer->endElement();

			$this->writer->endElement();
			$this->writer->endElement();

			if($this->hasConstraints($prototype))
			{
				$this->generateType($prototype);
			}
		}
		else
		{
			$this->writer->startElement('xs:simpleType');
			$this->writer->writeAttribute('name', $typeName);
			$this->writer->startElement('xs:restriction');
			$this->writer->writeAttribute('base', $this->getBasicType($type, true));

			if($type instanceof Property\String)
			{
				$this->generateTypeString($type);
			}
			else if($type instanceof Property\Decimal)
			{
				$this->generateTypeDecimal($type);
			}

			$pattern = $type->getPattern();
			if($pattern)
			{
				$this->writer->startElement('xs:pattern');
				$this->writer->writeAttribute('value', $pattern);
				$this->writer->endElement();
			}

			$enumeration = $type->getEnumeration();
			if($enumeration)
			{
				foreach($enumeration as $value)
				{
					$this->writer->startElement('xs:enumeration');
					$this->writer->writeAttribute('value', $value);
					$this->writer->endElement();
				}
			}

			$this->writer->endElement();
			$this->writer->endElement();
		}
	}

	protected function generateTypeArray(Property\ArrayType $type)
	{
		$minOccurs = $type->getMinLength();
		$maxOccurs = $type->getMaxLength();

		if($minOccurs)
		{
			$this->writer->writeAttribute('minOccurs', $minOccurs);
			$this->writer->writeAttribute('maxOccurs', 'unbounded');
		}
		else if($maxOccurs)
		{
			$this->writer->writeAttribute('minOccurs', 'unbounded');
			$this->writer->writeAttribute('maxOccurs', $maxOccurs);
		}
		else
		{
			$this->writer->writeAttribute('minOccurs', 0);
			$this->writer->writeAttribute('maxOccurs', 'unbounded');
		}
	}

	protected function generateTypeDecimal(Property\Decimal $type)
	{
		$max = $type->getMax();
		if($max)
		{
			$this->writer->startElement('xs:maxInclusive');
			$this->writer->writeAttribute('value', $max);
			$this->writer->endElement();
		}

		$min = $type->getMin();
		if($min)
		{
			$this->writer->startElement('xs:minInclusive');
			$this->writer->writeAttribute('value', $min);
			$this->writer->endElement();
		}
	}

	protected function generateTypeString(Property\String $type)
	{
		$minLength = $type->getMinLength();
		if($minLength)
		{
			$this->writer->startElement('xs:minLength');
			$this->writer->writeAttribute('value', $minLength);
			$this->writer->endElement();
		}

		$maxLength = $type->getMaxLength();
		if($maxLength)
		{
			$this->writer->startElement('xs:maxLength');
			$this->writer->writeAttribute('value', $maxLength);
			$this->writer->endElement();
		}
	}

	protected function getPropertyTypeName(PropertyInterface $type, $withNamespace = false)
	{
		if($this->hasConstraints($type))
		{
			return ($withNamespace ? 'tns:' : '') . 'type' . $type->getId();
		}
		else
		{
			return ($withNamespace ? 'xs:' : '') . $this->getBasicType($type);
		}
	}

	protected function getBasicType(PropertyInterface $type, $withNamespace = false)
	{
		$parts = explode('\\', get_class($type));

		return ($withNamespace ? 'xs:' : '') . lcfirst(end($parts));
	}

	protected function hasConstraints(PropertyInterface $type)
	{
		if($type instanceof Property\ComplexType || $type instanceof Property\ArrayType)
		{
			return true;
		}
		else if($type instanceof PropertySimpleAbstract)
		{
			if($type instanceof Property\Decimal)
			{
				if($type->getMin() !== null || $type->getMax() !== null)
				{
					return true;
				}
			}
			else if($type instanceof Property\String)
			{
				if($type->getMinLength() !== null || $type->getMaxLength() !== null)
				{
					return true;
				}
			}

			if($type->getPattern() !== null || $type->getEnumeration() !== null)
			{
				return true;
			}
		}

		return false;
	}
}
