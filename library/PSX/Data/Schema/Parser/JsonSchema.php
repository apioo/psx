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

namespace PSX\Data\Schema\Parser;

use PSX\Data\Schema;
use PSX\Data\Schema\Property;
use PSX\Data\Schema\PropertyInterface;
use PSX\Data\Schema\PropertySimpleAbstract;
use PSX\Data\Schema\ParserInterface;
use PSX\Data\Schema\Parser\JsonSchema\UnsupportedVersionException;
use PSX\Json;
use PSX\Uri;
use PSX\Util\UriResolver;

/**
 * JsonSchema
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class JsonSchema implements ParserInterface
{
	const SCHEMA_04 = 'http://json-schema.org/draft-04/schema#';

	protected $baseUri;
	protected $definitions;

	public function parse($schema)
	{
		$data = Json::decode($schema);

		$this->assertVersion($data);

		$this->baseUri     = new Uri(isset($data['id']) ? $data['id'] : '');
		$this->definitions = array();

		if(isset($data['definitions']))
		{
			$this->parseDefinitions($data['definitions']);
		}

		return new Schema($this->getProperty($data));
	}

	protected function getProperty(array $data, $name = null)
	{
		if(isset($data['$ref']))
		{
			$data = $this->resolveRef($data['$ref']);
		}

		$type = isset($data['type']) ? $data['type'] : null;

		switch($type)
		{
			case 'object':
				return $this->parseComplexType($data, $name);
				break;

			case 'array':
				return $this->parseArrayType($data, $name);
				break;

			case 'boolean':
				return $this->parseBoolean($data, $name);
				break;

			case 'integer':
				return $this->parseInteger($data, $name);
				break;

			case 'number':
				return $this->parseFloat($data, $name);
				break;

			default:
			case 'string':
				return $this->parseString($data, $name);
				break;
		}
	}

	protected function parseComplexType(array $data, $name = null)
	{
		$complexType = new Property\ComplexType($name);
		$properties  = isset($data['properties']) ? $data['properties'] : array();

		foreach($properties as $name => $property)
		{
			$complexType->add($this->getProperty($property, $name));
		}

		if(isset($data['description']))
		{
			$complexType->setDescription($data['description']);
		}

		if(isset($data['required']) && is_array($data['required']))
		{
			foreach($data['required'] as $propertyName)
			{
				$property = $complexType->get($propertyName);

				if($property instanceof PropertyInterface)
				{
					$property->setRequired(true);
				}
			}
		}

		return $complexType;
	}

	protected function parseArrayType(array $data, $name = null)
	{
		$arrayType = new Property\ArrayType($name);

		if(isset($data['items']) && is_array($data['items']))
		{
			$arrayType->setPrototype($this->getProperty($data['items']));
		}

		return $arrayType;
	}

	protected function parseBoolean(array $data, $name = null)
	{
		$property = new Property\Boolean($name);

		$this->parseScalar($property, $data);

		return $property;
	}

	protected function parseInteger(array $data, $name = null)
	{
		$property = new Property\Integer($name);

		$this->parseScalar($property, $data);

		return $property;
	}

	protected function parseFloat(array $data, $name = null)
	{
		$property = new Property\Float($name);

		$this->parseScalar($property, $data);

		return $property;
	}

	protected function parseString(array $data, $name = null)
	{
		$property = new Property\String($name);

		$this->parseScalar($property, $data);

		return $property;
	}

	protected function parseScalar(PropertySimpleAbstract $property, array $data)
	{
		if(isset($data['pattern']))
		{
			$property->setPattern($data['pattern']);
		}

		if(isset($data['enum']))
		{
			$property->setEnumeration($data['enum']);
		}

		if(isset($data['minimum']))
		{
			$property->setMin($data['minimum']);
		}

		if(isset($data['maximum']))
		{
			$property->setMax($data['maximum']);
		}

		if(isset($data['minLength']))
		{
			$property->setMinLength($data['minLength']);
		}

		if(isset($data['maxLength']))
		{
			$property->setMaxLength($data['maxLength']);
		}
	}

	protected function resolveRef($ref)
	{
		$resolver = new UriResolver();
		$uri      = $resolver->resolve($this->baseUri, new Uri($ref))->toString();

		if(isset($this->definitions[$uri]))
		{
			return $this->definitions[$uri];
		}

		return array();
	}

	protected function parseDefinitions(array $definitions)
	{
		$resolver = new UriResolver();

		foreach($definitions as $name => $definition)
		{
			$uri = $resolver->resolve($this->baseUri, new Uri('#/definitions/' . $name))->toString();

			$this->definitions[$uri] = $definition;
		}
	}

	protected function assertVersion(array $data)
	{
		if(isset($data['$schema']) && $data['$schema'] != self::SCHEMA_04)
		{
			throw new UnsupportedVersionException('Invalid version requires ' . self::SCHEMA_04);
		}
	}
}
