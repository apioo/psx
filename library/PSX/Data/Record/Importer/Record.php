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

namespace PSX\Data\Record\Importer;

use Closure;
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
use Serializable;

/**
 * Importer which reads the annotations of the given RecordInterface and calls 
 * the fitting setter methods of the record
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
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

		if(!is_array($data))
		{
			throw new InvalidArgumentException('Data must be an array');
		}

		$data = array_intersect_key($data, $record->getRecordInfo()->getFields());

		foreach($data as $k => $v)
		{
			if(isset($v))
			{
				// convert to camelcase if underscore is in name
				if(strpos($k, '_') !== false)
				{
					$k = implode('', array_map('ucfirst', explode('_', $k)));
				}

				$methodName = 'set' . ucfirst($k);

				// if we have an PSX\Data\Record instance and no concrete 
				// RecordAbstract implementation we have an magic __call method 
				// therefore we can not look at the annotation of the methods
				if($record instanceof DataRecord)
				{
					$record->$methodName($v);
				}
				else
				{
					try
					{
						$class  = new ReflectionClass($record);
						$method = $class->getMethod($methodName);

						if($method instanceof ReflectionMethod)
						{
							$record->$methodName($this->getMethodValue($method, $v));
						}
					}
					catch(ReflectionException $e)
					{
						// method does not exist
					}
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
		if(is_object($value))
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
			return $this->factory->getFactory($type)->factory($value, $this);
		}
		else
		{
			return $class->newInstance($value);
		}
	}
}
