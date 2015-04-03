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

namespace PSX\Data\Schema\Parser\JsonSchema;

use PSX\Data\Schema;
use PSX\Data\Schema\Property;
use PSX\Data\Schema\PropertyInterface;
use PSX\Data\Schema\PropertySimpleAbstract;
use PSX\Data\Schema\ParserInterface;
use PSX\Data\Schema\Parser\JsonSchema\UnsupportedVersionException;
use PSX\Json;
use PSX\Uri;

/**
 * Document
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Document
{
	protected $data;
	protected $resolver;
	protected $basePath;
	protected $baseUri;

	public function __construct(array $data, RefResolver $resolver, $basePath = null)
	{
		$this->data     = $data;
		$this->resolver = $resolver;
		$this->basePath = $basePath;
		$this->baseUri  = new Uri(isset($data['id']) ? $data['id'] : '');
	}

	/**
	 * The base path if the schema was fetched from a file
	 *
	 * @return string
	 */
	public function getBasePath()
	{
		return $this->basePath;
	}

	/**
	 * Tells whether the resource was fetched from a remote source
	 *
	 * @return boolean
	 */
	public function isRemote()
	{
		return $this->basePath === null;
	}

	/**
	 * @return PSX\Uri
	 */
	public function getBaseUri()
	{
		return $this->baseUri;
	}

	/**
	 * @return PSX\Data\Schema\PropertyInterface
	 */
	public function getProperty()
	{
		return $this->getRecProperty($this->data, null, 0);
	}

	/**
	 * Resolves an json pointer on the document
	 *
	 * @param string $pointer
	 * @param string $name
	 * @param integer $depth
	 * @return PSX\Data\Schema\PropertyInterface
	 */
	public function pointer($pointer, $name = null, $depth = 0)
	{
		$pointer = ltrim($pointer, '/');
		$pointer = str_replace('~0', '~', $pointer);
		$pointer = str_replace('~1', '/', $pointer);
		$parts   = !empty($pointer) ? explode('/', $pointer) : array();
		$data    = $this->data;

		foreach($parts as $key)
		{
			if(isset($data[$key]))
			{
				$data = $data[$key];
			}
			else
			{
				return array();
			}
		}

		return $this->getRecProperty($data, $name, $depth);
	}

	/**
	 * @param PSX\Uri $ref
	 * @return boolean
	 */
	public function canResolve(Uri $ref)
	{
		return $this->baseUri->getHost() == $ref->getHost() && $this->baseUri->getPath() == $ref->getPath();
	}

	protected function getRecProperty(array $data, $name, $depth)
	{
		if(isset($data['$ref']))
		{
			return $this->resolver->resolve($this, new Uri($data['$ref']), $name, $depth);
		}

		if(isset($data['title']))
		{
			$name = $data['title'];
		}

		$type = isset($data['type']) ? $data['type'] : null;

		switch($type)
		{
			case 'object':
				return $this->parseComplexType($data, $name, $depth);
				break;

			case 'array':
				return $this->parseArrayType($data, $name, $depth);
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

	protected function parseComplexType(array $data, $name, $depth)
	{
		$complexType = new Property\ComplexType($name);
		$properties  = isset($data['properties']) ? $data['properties'] : array();

		foreach($properties as $name => $row)
		{
			if(is_array($row))
			{
				$property = $this->getRecProperty($row, $name, $depth + 1);

				if($property !== null)
				{
					$complexType->add($property);
				}
			}
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

	protected function parseArrayType(array $data, $name, $depth)
	{
		$arrayType = new Property\ArrayType($name);

		if(isset($data['minItems']))
		{
			$arrayType->setMinLength($data['minItems']);
		}

		if(isset($data['maxItems']))
		{
			$arrayType->setMaxLength($data['maxItems']);
		}

		if(isset($data['items']) && is_array($data['items']))
		{
			$property = $this->getRecProperty($data['items'], null, $depth + 1);

			if($property !== null)
			{
				$arrayType->setPrototype($property);
			}
		}

		return $arrayType;
	}

	protected function parseBoolean(array $data, $name)
	{
		$property = new Property\Boolean($name);

		$this->parseScalar($property, $data);

		return $property;
	}

	protected function parseInteger(array $data, $name)
	{
		$property = new Property\Integer($name);

		$this->parseScalar($property, $data);

		return $property;
	}

	protected function parseFloat(array $data, $name)
	{
		$property = new Property\Float($name);

		$this->parseScalar($property, $data);

		return $property;
	}

	protected function parseString(array $data, $name)
	{
		$property = null;
		if(isset($data['format']))
		{
			if($data['format'] == 'date')
			{
				$property = new Property\Date($name);
			}
			else if($data['format'] == 'date-time')
			{
				$property = new Property\DateTime($name);
			}
			else if($data['format'] == 'duration')
			{
				$property = new Property\Duration($name);
			}
			else if($data['format'] == 'time')
			{
				$property = new Property\Time($name);
			}
		}

		if($property === null)
		{
			$property = new Property\String($name);
		}

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

	protected function assertVersion(array $data)
	{
		if(isset($data['$schema']) && $data['$schema'] != JsonSchema::SCHEMA_04)
		{
			throw new UnsupportedVersionException('Invalid version requires ' . JsonSchema::SCHEMA_04);
		}
	}
}
