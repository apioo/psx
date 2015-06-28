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

namespace PSX\Data\Record\Importer;

use InvalidArgumentException;
use PSX\Data\ReaderInterface;
use PSX\Data\Record as DataRecord;
use PSX\Data\Record\FactoryFactory;
use PSX\Data\Record\ImporterInterface;
use PSX\Data\RecordInterface;
use PSX\Util\Annotation;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

/**
 * Importer which reads the annotations of the given RecordInterface and calls 
 * the fitting setter methods of the record
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Record implements ImporterInterface
{
	protected $factory;

	public function __construct(FactoryFactory $factory)
	{
		$this->factory = $factory;
	}

	public function accept($record)
	{
		return $record instanceof RecordInterface;
	}

	public function import($record, $data)
	{
		if(!$record instanceof RecordInterface)
		{
			throw new InvalidArgumentException('Record must be an instanceof PSX\Data\RecordInterface');
		}

		if(!$data instanceof \stdClass)
		{
			throw new InvalidArgumentException('Data must be an stdClass');
		}

		// if we have an PSX\Data\Record instance and no concrete RecordAbstract 
		// we have no meta informations about the data therefore we can not 
		// look at the annotation of the methods
		if($record instanceof DataRecord)
		{
			foreach($data as $key => $value)
			{
				// convert to camelcase if underscore is in name
				if(strpos($key, '_') !== false)
				{
					$key = implode('', array_map('ucfirst', explode('_', $key)));
				}

				$record->setProperty($key, $value);
			}
		}
		else
		{
			$properties = array_intersect_key(
				(array) $data, 
				$record->getRecordInfo()->getFields()
			);

			foreach($properties as $key => $value)
			{
				// convert to camelcase if underscore is in name
				if(strpos($key, '_') !== false)
				{
					$key = implode('', array_map('ucfirst', explode('_', $key)));
				}

				$methodName = 'set' . ucfirst($key);

				try
				{
					$class  = new ReflectionClass($record);
					$method = $class->getMethod($methodName);

					if($method instanceof ReflectionMethod)
					{
						$method->invokeArgs($record, array($this->getMethodValue($method, $value)));
					}
				}
				catch(ReflectionException $e)
				{
					// method does not exist
				}
			}
		}

		return $record;
	}

	protected function getMethodValue(ReflectionMethod $method, $value)
	{
		$comment = $method->getDocComment();

		if(!empty($comment))
		{
			$doc   = Annotation::parse($comment);
			$param = $doc->getFirstAnnotation('param');

			if(!empty($param))
			{
				$param = explode(' ', $param);
				$type  = isset($param[0]) ? $param[0] : null;

				if(substr($type, 0, 6) == 'array<')
				{
					$type   = substr($type, 6, -1);
					$values = (array) $value;
					$value  = array();

					foreach($values as $row)
					{
						$value[] = $this->getMethodType($type, $row);
					}
				}
				else
				{
					$value = $this->getMethodType($type, $value);
				}
			}
		}

		return $value;
	}

	protected function getMethodType($type, $value)
	{
		if(!$value instanceof \stdClass && is_object($value))
		{
			return $value;
		}

		switch($type)
		{
			case 'integer':
				$value = (int) $value;
				break;

			case 'float':
				$value = (float) $value;
				break;

			case 'boolean':
				$value = $value === 'false' ? false : (bool) $value;
				break;

			case 'string':
				$value = (string) $value;
				break;

			case 'array':
				$value = (array) $value;
				break;

			default:
				$value = $this->getClassValue($type, $value);
				break;
		}

		return $value;
	}

	protected function getClassValue($type, $value)
	{
		$class = new ReflectionClass($type);

		if($class->implementsInterface('PSX\Data\RecordInterface'))
		{
			return $this->import($class->newInstance(), $value);
		}
		else if($class->implementsInterface('PSX\Data\Record\FactoryInterface'))
		{
			return $this->factory->getFactory($type)->factory($value);
		}
		else
		{
			return $class->newInstance($value);
		}
	}
}
