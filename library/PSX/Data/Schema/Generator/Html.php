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
use PSX\Data\Schema\PropertySimpleAbstract;

/**
 * Can generate sample html request data from an schema which can be used for 
 * documentation purpose
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Html implements GeneratorInterface
{
	protected $_types = array();

	public function generate(SchemaInterface $schema)
	{
		return $this->generateType($schema->getDefinition());
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
			$response = '<div id="type-' . $type->getName() . '" class="type">';
			$response.= '<h1>' . $type->getName() . '</h1>';
			$response.= '<div class="type-description">' . $type->getDescription() . '</div>';
			$response.= '<table class="table type-properties">';
			$response.= '<colgroup>';
			$response.= '<col width="20%" />';
			$response.= '<col width="20%" />';
			$response.= '<col width="40%" />';
			$response.= '<col width="20%" />';
			$response.= '</colgroup>';
			$response.= '<thead>';
			$response.= '<tr>';
			$response.= '<th>Property</th>';
			$response.= '<th>Type</th>';
			$response.= '<th>Description</th>';
			$response.= '<th>Constraints</th>';
			$response.= '</tr>';
			$response.= '</thead>';
			$response.= '<tbody>';

			$children = $type->getChildren();

			foreach($children as $child)
			{
				list($type, $constraints) = $this->getValueDescription($child);

				// we dont add descriptions of complex types since we refer to
				// the complex type which contains the description
				$description = '';
				if(!$child instanceof Property\ComplexType)
				{
					$description.= $child->getDescription();
				}

				$response.= '<tr>';
				$response.= '<td><span class="property-name ' . ($child->isRequired() ? 'property-required' : 'property-optional') . '">' . $child->getName() . '</span></td>';
				$response.= '<td>' . $type . '</td>';
				$response.= '<td><span class="property-description">' . $description . '</span></td>';
				$response.= '<td>' . $constraints . '</td>';
				$response.= '</tr>';
			}

			$response.= '</tbody>';
			$response.= '</table>';
			$response.= '</div>';

			foreach($children as $child)
			{
				if($child instanceof Property\ComplexType)
				{
					$response.= $this->generateType($child);
				}
				else if($child instanceof Property\ArrayType)
				{
					$prototype = $child->getPrototype();

					if($prototype instanceof Property\ComplexType)
					{
						$response.= $this->generateType($prototype);
					}
				}
			}

			return $response;
		}
	}

	protected function getValueDescription(PropertyInterface $type, $enclosed = true)
	{
		if($type instanceof Property\ComplexType)
		{
			$span = '<span class="property-type type-object"><a href="#type-' . $type->getName() . '">' . $type->getName() . '</a></span>';

			return [$span, null];
		}
		else if($type instanceof Property\ArrayType)
		{
			$child = $this->getValueDescription($type->getPrototype());
			$span  = '<span class="property-type type-array">Array&lt;' . $child[0] . '&gt;</span>';

			return [$span, $child[1]];
		}
		else if($type instanceof PropertySimpleAbstract)
		{
			$class    = explode('\\', get_class($type));
			$typeName = end($class);

			$constraints = array();

			if($type->getPattern() !== null)
			{
				$constraints['pattern'] = '<span class="constraint-pattern">' . $type->getPattern() .'</span>';
			}

			if($type->getEnumeration() !== null)
			{
				$enumeration = '<ul class="property-enumeration">';
				foreach($type->getEnumeration() as $enum)
				{
					$enumeration.= '<li><span class="constraint-enumeration-value">' . $enum . '</span></li>';
				}
				$enumeration.= '</li>';

				$constraints['enumeration'] = '<span class="constraint-enumeration">' . $enumeration .'</span>';
			}

			if($type instanceof Property\Decimal)
			{
				$min = $type->getMin();
				if($min !== null)
				{
					$constraints['minimum'] = '<span class="constraint-minimum">' . $min . '</span>';
				}

				$max = $type->getMax();
				if($max !== null)
				{
					$constraints['maximum'] = '<span class="constraint-maximum">' . $max . '</span>';
				}
			}
			else if($type instanceof Property\String)
			{
				$min = $type->getMinLength();
				if($min !== null)
				{
					$constraints['minimum'] = '<span class="constraint-minimum">' . $min . '</span>';
				}

				$max = $type->getMaxLength();
				if($max !== null)
				{
					$constraints['maximum'] = '<span class="constraint-maximum">' . $max . '</span>';
				}
			}

			$constraint = '';
			if(!empty($constraints))
			{
				$constraint.= '<dl class="property-constraint">';
				foreach($constraints as $name => $con)
				{
					$constraint.= '<dt>' . ucfirst($name) . '</dt>';
					$constraint.= '<dd>' . $con . '</dd>';
				}
				$constraint.= '</dl>';
			}

			$cssClass = 'property-type-' . strtolower($typeName);

			if($type instanceof Property\Date)
			{
				$typeName = '<a href="http://tools.ietf.org/html/rfc3339#section-5.6" title="RFC3339">Date</a>';
			}
			else if($type instanceof Property\DateTime)
			{
				$typeName = '<a href="http://tools.ietf.org/html/rfc3339#section-5.6" title="RFC3339">DateTime</a>';
			}
			else if($type instanceof Property\DateTimeStamp)
			{
				$typeName = '<a href="http://tools.ietf.org/html/rfc3339#section-5.6" title="RFC3339">DateTimeStamp</a>';
			}
			else if($type instanceof Property\Duration)
			{
				$typeName = '<span title="ISO 8601">Duration</span>';
			}

			$span = '<span class="property-type ' . $cssClass . '">' . $typeName . '</span>';

			return [$span, $constraint];
		}
	}
}
