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

namespace PSX\Swagger\ModelGenerator;

use InvalidArgumentException;
use PSX\Data\Record;
use PSX\Data\RecordAbstract;
use PSX\Data\RecordInterface;
use PSX\Swagger\Model;
use PSX\Swagger\ModelGeneratorInterface;
use PSX\Swagger\Property;
use PSX\Swagger\PropertyReference;
use PSX\Util\Annotation;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

/**
 * RecordGenerator
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class RecordGenerator implements ModelGeneratorInterface
{
	public function getComplexType($record)
	{
		if(!$record instanceof RecordInterface)
		{
			throw new InvalidArgumentException('Must be an RecordInterface');
		}

		$info  = $record->getRecordInfo();
		$model = new Model($info->getName());

		// now we have the fields we check wich setter method exists for each 
		// field. If a setter method exists we have an value wich can be set
		// from outside. We get the doc comment from the reflection class wich 
		// will be used as description etc.
		$fields  = $info->getFields();
		$class   = new ReflectionClass($record);
		$methods = $class->getMethods();

		foreach($fields as $k => $v)
		{
			// convert to camelcase if underscore is in name
			if(strpos($k, '_') !== false)
			{
				$k = implode('', array_map('ucfirst', explode('_', $k)));
			}

			$methodName = 'set' . ucfirst($k);

			try
			{
				$method   = $class->getMethod($methodName);
				$property = $this->getPropertyByMethod($k, $method);

				if($property instanceof Property)
				{
					$model->addProperty($property);
				}
			}
			catch(ReflectionException $e)
			{
				// method does not exist
			}
		}

		return $model;
	}

	protected function getPropertyByMethod($name, ReflectionMethod $method)
	{
		$doc   = Annotation::parse($method->getDocComment());
		$param = $doc->getFirstAnnotation('param');

		if(!empty($param))
		{
			$parts  = explode(' ', $param, 2);
			$type   = $parts[0];
			$scalar = $this->getNormalizedType(strtolower($type));

			if($scalar !== null)
			{
				return new Property($name, $scalar);
			}
			else
			{
				if(class_exists($type))
				{
					$class = new ReflectionClass($type);

					if($class->isSubclassOf('PSX\Data\RecordInterface'))
					{
						return new PropertyReference($name, $class->newInstance()->getRecordInfo()->getName());
					}
					else
					{
						// the class is no record interface so it ca be passed 
						// as string i.e. DateTime
						return new Property($name, Property::TYPE_STRING);
					}
				}
				else
				{
					// no class so probably an unknown type so we assume an 
					// string
					return new Property($name, Property::TYPE_STRING);
				}
			}
		}

		return null;
	}

	protected function getNormalizedType($type)
	{
		$type = ltrim($type, '\\');

		switch($type)
		{
			case 'int':
			case 'integer':
			case 'long':
				return Property::TYPE_INTEGER;
				break;

			case 'float':
			case 'double':
				return Property::TYPE_NUMBER;
				break;

			case 'bool':
			case 'boolean':
				return Property::TYPE_BOOLEAN;
				break;

			case 'string':
				return Property::TYPE_STRING;
				break;
		}

		return null;
	}
}
