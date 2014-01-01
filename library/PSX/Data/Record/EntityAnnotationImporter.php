<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2013 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Data\Record;

use DateTime;
use InvalidArgumentException;
use PSX\Data\Record;
use PSX\Util\Annotation;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

/**
 * Importer wich reads the annotations from an entity and creates an record 
 * based on the defined fields
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class EntityAnnotationImporter implements ImporterInterface
{
	public function import($entity, $data)
	{
		if(!is_object($entity))
		{
			throw new InvalidArgumentException('Entity must be an object');
		}

		if(!is_array($data))
		{
			throw new InvalidArgumentException('Data must be an array');
		}

		$class  = new ReflectionClass($entity);
		$fields = $this->getEntityFields($class, $data);

		return new Record(lcfirst($class->getShortName()), $fields);
	}

	protected function getEntityFields(ReflectionClass $class, array $data)
	{
		$properties = $class->getProperties();
		$fields     = array();

		// change data keys to camelcase
		$result = array();
		foreach($data as $key => $value)
		{
			// convert to camelcase if underscore is in name
			if(strpos($key, '_') !== false)
			{
				$key = implode('', array_map('ucfirst', explode('_', $key)));
			}

			$result[$key] = $value;
		}
		$data = $result;

		foreach($properties as $property)
		{
			$doc = Annotation::parse($property->getDocComment());
			$key = $property->getName();

			if(!isset($data[$key]))
			{
				continue;
			}

			$factoryClass = $doc->getFirstAnnotation('DataFactory');
			$builderClass = $doc->getFirstAnnotation('DataBuilder');

			if(($column = $doc->getFirstAnnotation('Column')) != null)
			{
				$attributes = Annotation::parseAttributes($column);
				$value      = null;

				if(isset($attributes['type']) && isset($data[$key]))
				{
					$value = $this->getColumnTypeValue($attributes['type'], $data[$key]);
				}

				$fields[$key] = $value;
			}
			else if(($mto = $doc->getFirstAnnotation('ManyToOne')) != null)
			{
				$attributes = Annotation::parseAttributes($mto);

				if(isset($attributes['targetEntity']))
				{
					$targetEntity = $attributes['targetEntity'];

					$fields[$key] = $this->getValue($factoryClass, $builderClass, $data[$key], $attributes['targetEntity']);
				}
			}
			else if(($otm = $doc->getFirstAnnotation('OneToMany')) != null)
			{
				$attributes = Annotation::parseAttributes($otm);

				if(isset($attributes['targetEntity']))
				{
					$targetEntity = $attributes['targetEntity'];

					if(is_array($data[$key]))
					{
						$result = array();

						foreach($data[$key] as $row)
						{
							$result[] = $this->getValue($factoryClass, $builderClass, $row, $targetEntity);
						}

						$fields[$key] = $result;
					}
				}
			}
		}

		return $fields;
	}

	protected function getValue($factoryClass, $builderClass, $value, $targetEntity)
	{
		if($factoryClass != null)
		{
			$class = new ReflectionClass($factoryClass);
			if($class->implementsInterface('PSX\Data\FactoryInterface'))
			{
				$entity = $class->newInstance()->factory($value);
				$record = $this->import($entity, $value);

				return $record;
			}
		}
		else if($builderClass != null)
		{
			$class = new ReflectionClass($builderClass);
			if($class->implementsInterface('PSX\Data\BuilderInterface'))
			{
				return $class->newInstance()->build($value);
			}
		}
		else
		{
			if(class_exists($targetEntity))
			{
				$entity = new $targetEntity();

				return $this->import($entity, $value);
			}
		}

		return null;
	}

	protected function getColumnTypeValue($type, $value)
	{
		switch($type)
		{
			case 'integer':
			case 'smallint':
			case 'bigint':
				return (integer) $value;

			case 'string':
			case 'text':
			case 'array':
			case 'object':
			case 'time':
				return (string) $value;

			case 'decimal':
			case 'float':
				return (float) $value;

			case 'boolean':
				return (boolean) $value;

			case 'datetime':
			case 'date':
				return new DateTime($value);
		}

		return null;
	}
}
