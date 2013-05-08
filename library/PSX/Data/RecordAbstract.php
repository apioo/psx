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

namespace PSX\Data;

use PSX\Exception;
use PSX\Util\Annotation;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use Serializable;

/**
 * RecordAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class RecordAbstract implements RecordInterface, Serializable
{
	public function hasFields()
	{
		$fields  = $this->getFields();
		$columns = func_get_args();

		foreach($columns as $column)
		{
			if(!isset($fields[$column]))
			{
				return false;
			}
		}

		return true;
	}

	public function getData()
	{
		return $this->getRecData($this->getFields());
	}

	public function import(ReaderResult $result)
	{
		$class = new ReflectionClass($this);

		switch($result->getType())
		{
			case ReaderInterface::FORM:
			case ReaderInterface::GPC:
			case ReaderInterface::JSON:
			case ReaderInterface::MULTIPART:
			case ReaderInterface::XML:

				$data = (array) $result->getData();
				$data = array_intersect_key($data, $this->getFields());

				foreach($data as $k => $v)
				{
					if(isset($v))
					{
						// convert to camelcase if underscore is in name
						if(strpos($k, '_') !== false)
						{
							$k = implode('', array_map('ucfirst', explode('_', $k)));
						}

						try
						{
							$methodName = 'set' . ucfirst($k);
							$method = $class->getMethod($methodName);

							if($method instanceof ReflectionMethod)
							{
								$this->$methodName($this->getMethodValue($method, $v, $result->getType()));
							}
						}
						catch(ReflectionException $e)
						{
							// method does not exist
						}
					}
				}

				break;

			default:

				throw new NotSupportedException('Reader is not supported');
				break;
		}
	}

	public function export(WriterResult $result)
	{
		switch($result->getType())
		{
			case WriterInterface::FORM:
			case WriterInterface::JSON:
			case WriterInterface::XML:

				return $this->getData();
				break;

			default:

				throw new NotSupportedException('Writer is not supported');
				break;
		}
	}

	public function serialize()
	{
		$vars = get_class_vars(get_class($this));
		$data = array();

		foreach($vars as $k => $v)
		{
			if($k[0] != '_')
			{
				$data[$k] = $this->$k;
			}
		}

		return json_encode($data);
	}

	public function unserialize($data)
	{
		$data = json_decode($data, true);

		if(is_array($data))
		{
			foreach($data as $k => $v)
			{
				$this->$k = $v;
			}
		}
	}

	protected function getMethodValue(ReflectionMethod $method, $value, $resultType)
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
						$value[] = $this->getMethodType($type, $row, $resultType);
					}
				}
				else
				{
					$value = $this->getMethodType($type, $value, $resultType);
				}
			}
		}

		return $value;
	}

	protected function getMethodType($type, $value, $resultType)
	{
		switch($type)
		{
			case 'integer':
				$value = (integer) $value;
				break;

			case 'float':
				$value = (float) $value;
				break;

			case 'boolean':
				$value = (boolean) $value;
				break;

			case 'string':
				$value = (string) $value;
				break;

			case 'array':
				$value = (array) $value;
				break;

			default:
				$class = new ReflectionClass($type);
				if($class->implementsInterface('PSX\Data\RecordInterface'))
				{
					$result = new ReaderResult($resultType, $value);

					$value = $class->newInstance();
					$value->import($result);
				}
				else if($class->implementsInterface('PSX\Data\FactoryInterface'))
				{
					$result = new ReaderResult($resultType, $value);

					$value = $class->newInstance()->factory($result);
					$value->import($result);
				}
				else
				{
					$value = $class->newInstance($value);
				}
				break;
		}

		return $value;
	}

	protected function getRecData(array $fields)
	{
		$data = array();

		foreach($fields as $k => $v)
		{
			if(isset($v))
			{
				if(is_array($v))
				{
					$data[$k] = $this->getRecData($v);
				}
				else if($v instanceof RecordInterface)
				{
					$data[$k] = $v->getData();
				}
				else if(is_object($v))
				{
					$data[$k] = (string) $v;
				}
				else
				{
					$data[$k] = $v;
				}
			}
		}

		return $data;
	}
}

