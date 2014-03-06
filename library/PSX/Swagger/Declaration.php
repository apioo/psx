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

namespace PSX\Swagger;

use ReflectionClass;
use PSX\Data\RecordAbstract;
use PSX\Data\RecordInterface;
use PSX\Data\Record;
use PSX\Util\Annotation;

/**
 * Declaration
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Declaration extends RecordAbstract
{
	const VERSION = '1.1';

	protected $apiVersion;
	protected $basePath;
	protected $resourcePath;

	protected $apis = array();
	protected $models = array();

	public function __construct($apiVersion, $basePath, $resourcePath)
	{
		$this->apiVersion   = $apiVersion;
		$this->basePath     = $basePath;
		$this->resourcePath = $resourcePath;
	}

	public function setResourcePath($resourcePath)
	{
		$this->resourcePath = $resourcePath;
	}

	public function addApi(Api $api)
	{
		$this->apis[] = $api;
	}

	public function addModel(RecordInterface $record)
	{
		$this->models[] = self::getComplexDatatypeByRecord($record);
	}

	public function getName()
	{
		return 'declaration';
	}

	public function getFields()
	{
		return array(
			'apiVersion'     => $this->apiVersion,
			'swaggerVersion' => self::VERSION,
			'basePath'       => $this->basePath,
			'resourcePath'   => $this->resourcePath,
			'apis'           => $this->apis,
			'models'         => $this->models,
		);
	}

	/**
	 * Generates an complex swagger datatype record from the given $record class 
	 *
	 * @param PSX\Data\RecordInterface $record
	 * @return PSX\Data\RecordInterface
	 */
	public static function getComplexDatatypeByRecord(RecordInterface $record)
	{
		$class = new ReflectionClass($record);
		$props = array();

		// now we have the fields we check wich setter method exists for each 
		// field. If a setter method exists we have an value wich can be set
		// from outside. We get the doc comment from the reflection class wich 
		// will be used as description etc.
		$fields  = $record->getFields();
		$methods = $class->getMethods();

		foreach($fields as $k => $v)
		{
			// convert to camelcase if underscore is in name
			if(strpos($k, '_') !== false)
			{
				$k = implode('', array_map('ucfirst', explode('_', $k)));
			}

			$methodName = 'set' . ucfirst($k);

			foreach($methods as $method)
			{
				if($method->getName() == $methodName)
				{
					$doc    = Annotation::parse($method->getDocComment());
					$params = $doc->getAnnotation('param');

					foreach($params as $param)
					{
						$parts = explode(' ', $param, 2);
						$type  = strtolower($parts[0]);
						$desc  = $doc->getText();

						switch($type)
						{
							case 'byte':
							case 'boolean':
							case 'int':
							case 'long':
							case 'float':
							case 'double':
							case 'string':
								// we have an valid data type
								break;

							case 'bool':
								$type = 'boolean';
								break;

							case 'integer':
								$type = 'int';
								break;

							case 'datetime':
								$type = 'Date';
								break;

							default:
								$type = null;
								break;
						}

						if($type !== null)
						{
							$props[$k] = array(
								'type'        => $type,
								'description' => $desc,
							);
						}
					}
				}
			}
		}

		return new Record($record->getName(), array(
			'id'         => $record->getName(),
			'properties' => $props,
		));
	}
}
