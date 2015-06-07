<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
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
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Html implements GeneratorInterface
{
	protected $_types;

	public function generate(SchemaInterface $schema)
	{
		$this->_types = array();

		$html = $this->generateType($schema->getDefinition());

		// this makes sure that we only reference objects which are actually 
		// rendered
		foreach($this->_types as $typeId => $typeName)
		{
			$name = '<a href="#psx-type-' . $typeId . '">' . $typeName . '</a>';
			$html = preg_replace('/<a href=\"#psx-type-' . $typeId . '\">(\w+)<\/a>/ims', $name, $html);
		}

		return $html;
	}

	protected function generateType(PropertyInterface $type)
	{
		if(isset($this->_types[$type->getId()]))
		{
			return;
		}

		$this->_types[$type->getId()] = $type->getName();

		if($type instanceof Property\ComplexType)
		{
			$properties = $type->getProperties();

			if(empty($properties))
			{
				return;
			}

			$response = '<div id="psx-type-' . $type->getId() . '" class="psx-complex-type">';
			$response.= '<h1>' . $type->getName() . '</h1>';
			$response.= '<div class="psx-type-description">' . $type->getDescription() . '</div>';
			$response.= '<table class="table psx-type-properties">';
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

			foreach($properties as $property)
			{
				list($type, $constraints) = $this->getValueDescription($property);

				$description = '';
				if(!$property instanceof Property\ComplexType)
				{
					$description.= $property->getDescription();
				}

				$response.= '<tr>';
				$response.= '<td><span class="psx-property-name ' . ($property->isRequired() ? 'psx-property-required' : 'psx-property-optional') . '">' . $property->getName() . '</span></td>';
				$response.= '<td>' . $type . '</td>';
				$response.= '<td><span class="psx-property-description">' . $description . '</span></td>';
				$response.= '<td>' . $constraints . '</td>';
				$response.= '</tr>';
			}

			$response.= '</tbody>';
			$response.= '</table>';
			$response.= '</div>';

			foreach($properties as $property)
			{
				if($property instanceof Property\CompositeTypeAbstract)
				{
					foreach($property as $childProperty)
					{
						$response.= $this->generateType($childProperty);
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
			$span = '<span class="psx-property-type psx-property-type-complex"><a href="#psx-type-' . $type->getId() . '">' . $type->getName() . '</a></span>';

			return [$span, null];
		}
		else if($type instanceof Property\ArrayType)
		{
			$constraints = array();

			$min = $type->getMinLength();
			if($min !== null)
			{
				$constraints['minimum'] = '<span class="psx-constraint-minimum">' . $min . '</span>';
			}

			$max = $type->getMaxLength();
			if($max !== null)
			{
				$constraints['maximum'] = '<span class="psx-constraint-maximum">' . $max . '</span>';
			}

			$constraint = $this->constraintToString($constraints);

			$property = $this->getValueDescription($type->getPrototype());
			$span     = '<span class="psx-property-type psx-property-type-array">Array&lt;' . $property[0] . '&gt;</span>';

			return [$span, $constraint];
		}
		else if($type instanceof Property\ChoiceType)
		{
			$choice     = array();
			$properties = $type->getProperties();

			foreach($properties as $prop)
			{
				$property = $this->getValueDescription($prop);
				$choice[] = $property[0];
			}

			$span = '<span class="psx-property-type psx-property-type-choice">' . implode('|', $choice) . '</span>';

			return [$span, null];
		}
		else if($type instanceof PropertySimpleAbstract)
		{
			$typeName    = ucfirst($type->getTypeName());
			$constraints = array();

			if($type->getPattern() !== null)
			{
				$constraints['pattern'] = '<span class="psx-constraint-pattern">' . $type->getPattern() .'</span>';
			}

			if($type->getEnumeration() !== null)
			{
				$enumeration = '<ul class="psx-property-enumeration">';
				foreach($type->getEnumeration() as $enum)
				{
					$enumeration.= '<li><span class="psx-constraint-enumeration-value">' . $enum . '</span></li>';
				}
				$enumeration.= '</ul>';

				$constraints['enumeration'] = '<span class="psx-constraint-enumeration">' . $enumeration .'</span>';
			}

			if($type instanceof Property\DecimalType)
			{
				$min = $type->getMin();
				if($min !== null)
				{
					$constraints['minimum'] = '<span class="psx-constraint-minimum">' . $min . '</span>';
				}

				$max = $type->getMax();
				if($max !== null)
				{
					$constraints['maximum'] = '<span class="psx-constraint-maximum">' . $max . '</span>';
				}
			}
			else if($type instanceof Property\StringType)
			{
				$min = $type->getMinLength();
				if($min !== null)
				{
					$constraints['minimum'] = '<span class="psx-constraint-minimum">' . $min . '</span>';
				}

				$max = $type->getMaxLength();
				if($max !== null)
				{
					$constraints['maximum'] = '<span class="psx-constraint-maximum">' . $max . '</span>';
				}
			}

			$constraint = $this->constraintToString($constraints);
			$cssClass   = 'psx-property-type-' . strtolower($typeName);

			if($type instanceof Property\DateType)
			{
				$typeName = '<a href="http://tools.ietf.org/html/rfc3339#section-5.6" title="RFC3339">Date</a>';
			}
			else if($type instanceof Property\DateTimeType)
			{
				$typeName = '<a href="http://tools.ietf.org/html/rfc3339#section-5.6" title="RFC3339">DateTime</a>';
			}
			else if($type instanceof Property\TimeType)
			{
				$typeName = '<a href="http://tools.ietf.org/html/rfc3339#section-5.6" title="RFC3339">Time</a>';
			}
			else if($type instanceof Property\DurationType)
			{
				$typeName = '<span title="ISO 8601">Duration</span>';
			}

			$span = '<span class="psx-property-type ' . $cssClass . '">' . $typeName . '</span>';

			return [$span, $constraint];
		}
	}

	protected function constraintToString(array $constraints)
	{
		$constraint = '';
		if(!empty($constraints))
		{
			$constraint.= '<dl class="psx-property-constraint">';
			foreach($constraints as $name => $con)
			{
				$constraint.= '<dt>' . ucfirst($name) . '</dt>';
				$constraint.= '<dd>' . $con . '</dd>';
			}
			$constraint.= '</dl>';
		}

		return $constraint;
	}
}
