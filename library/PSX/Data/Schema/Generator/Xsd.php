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
 * Xsd
 *
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
		$this->writer->writeAttribute('xmlns', $this->targetNamespace);
		$this->writer->writeAttribute('xmlns:xs', 'http://www.w3.org/2001/XMLSchema');
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
			$this->writer->startElement('xs:element');
			$this->writer->writeAttribute('name', $child->getName());
			$this->writer->writeAttribute('type', $child->hasConstraints() ? $child->getName() : $this->getPropertyTypeName($child));
			$this->writer->writeAttribute('minOccurs', $child->isRequired() ? 1 : 0);
			$this->writer->writeAttribute('maxOccurs', 1);
			$this->writer->endElement();
		}

		$this->writer->endElement();
		$this->writer->endElement();
		$this->writer->endElement();

		foreach($children as $child)
		{
			if($child->hasConstraints())
			{
				$this->generateType($child);
			}
		}
	}

	protected function generateType(PropertyInterface $type)
	{
		if(in_array($type->getName(), $this->_types))
		{
			return;
		}

		$this->_types[] = $type->getName();

		if($type instanceof Property\ComplexType)
		{
			$this->writer->startElement('xs:complexType');
			$this->writer->writeAttribute('name', $type->getName());
			$this->writer->startElement('xs:sequence');

			$children = $type->getChildren();

			foreach($children as $child)
			{
				$this->writer->startElement('xs:element');
				$this->writer->writeAttribute('name', $child->getName());
				$this->writer->writeAttribute('type', $child->hasConstraints() ? $child->getName() : $this->getPropertyTypeName($child));
				$this->writer->writeAttribute('minOccurs', $child->isRequired() ? 1 : 0);
				$this->writer->writeAttribute('maxOccurs', 1);
				$this->writer->endElement();
			}

			$this->writer->endElement();
			$this->writer->endElement();

			foreach($children as $child)
			{
				if($child->hasConstraints())
				{
					$this->generateType($child);
				}
			}
		}
		else if($type instanceof Property\ArrayType)
		{
			$this->writer->startElement('xs:complexType');
			$this->writer->writeAttribute('name', $type->getName());
			$this->writer->startElement('xs:sequence');

			$prototype = $type->getPrototype();

			$this->writer->startElement('xs:element');
			$this->writer->writeAttribute('name', $prototype->getName());
			$this->writer->writeAttribute('type', $prototype->hasConstraints() ? $prototype->getName() : $this->getPropertyTypeName($prototype));

			$length    = $type->getMinLength();
			$minOccurs = $type->getMinLength();
			$maxOccurs = $type->getMaxLength();

			if($length)
			{
				$this->writer->writeAttribute('minOccurs', $length);
				$this->writer->writeAttribute('maxOccurs', $length);
			}
			else if($minOccurs)
			{
				$this->writer->writeAttribute('minOccurs', $minOccurs);
			}
			else if($maxOccurs)
			{
				$this->writer->writeAttribute('maxOccurs', $maxOccurs);
			}
			else
			{
				$this->writer->writeAttribute('minOccurs', 0);
				$this->writer->writeAttribute('maxOccurs', 'unbounded');
			}

			$this->writer->endElement();

			$this->writer->endElement();
			$this->writer->endElement();

			if($prototype->hasConstraints())
			{
				$this->generateType($prototype);
			}
		}
		else
		{
			$this->writer->startElement('xs:simpleType');
			$this->writer->writeAttribute('name', $type->getName());
			$this->writer->startElement('xs:restriction');
			$this->writer->writeAttribute('base', $this->getPropertyTypeName($type));

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
		$length = $type->getLength();
		if($length)
		{
			$this->writer->startElement('xs:length');
			$this->writer->writeAttribute('value', $length);
			$this->writer->endElement();
		}

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

	protected function getPropertyTypeName(PropertyInterface $type)
	{
		$parts = explode('\\', get_class($type));

		return 'xs:' . lcfirst(end($parts));
	}
}
