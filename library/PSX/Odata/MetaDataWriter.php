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

namespace PSX\Odata;

use ReflectionClass;
use ReflectionException;
use PSX\Data\RecordInterface;
use PSX\Util\Annotation;
use XMLWriter;

/**
 * MetaData
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class MetaDataWriter
{
	protected $namespace;
	protected $writer;

	protected $complexTypes = array();
	protected $typesAdded = array();

	public function __construct($namespace)
	{
		$this->namespace = $namespace;

		$this->writer = new XMLWriter();
		$this->writer->openMemory();
		$this->writer->setIndent(true);
		$this->writer->startDocument('1.0', 'UTF-8');

		$this->writer->startElement('edmx:Edmx');
		$this->writer->writeAttribute('xmlns:edmx', 'http://schemas.microsoft.com/ado/2007/06/edmx');
		$this->writer->writeAttribute('Version', '1.0');

		$this->writer->startElement('edmx:DataServices');
		$this->writer->writeAttribute('xmlns:m', 'http://schemas.microsoft.com/ado/2007/08/dataservices/metadata');
		$this->writer->writeAttribute('m:DataServiceVersion', '3.0');
		$this->writer->writeAttribute('m:MaxDataServiceVersion', '3.0');

		$this->writer->startElement('Schema');
		$this->writer->writeAttribute('xmlns', 'http://schemas.microsoft.com/ado/2009/11/edm');
		$this->writer->writeAttribute('Namespace', $this->namespace);
	}

	/**
	 * Generates an entity definition from the given $record class 
	 *
	 * @param PSX\Data\RecordInterface $record
	 */
	public function addEntity(RecordInterface $record)
	{
		$this->writer->startElement('EntityType');
		$this->writer->writeAttribute('Name', $record->getName());

		$this->buildEntity(new ReflectionClass($record), true);

		$this->buildComplexTypes();

		$this->writer->endElement();
	}

	public function close()
	{
		$this->writer->endElement();
		$this->writer->endElement();
		$this->writer->endElement();
		$this->writer->endDocument();

		echo $this->writer->outputMemory();
	}

	protected function buildEntity(ReflectionClass $class, $key = false, $idField = 'id')
	{
		$record  = $class->newInstance();
		$fields  = $record->getFields();
		$methods = $class->getMethods();

		// add key if available
		if($key)
		{
			if(array_key_exists($idField, $fields))
			{
				$this->writer->startElement('Key');
				$this->writer->startElement('PropertyRef');
				$this->writer->writeAttribute('Name', ucfirst($idField));
				$this->writer->endElement();
				$this->writer->endElement();
			}
		}

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
					$doc   = Annotation::parse($method->getDocComment());
					$param = $doc->getFirstAnnotation('param');

					if(!empty($param))
					{
						$parts = explode(' ', $param, 2);
						$type  = strtolower($parts[0]);
						$type  = $this->getType($type);

						if($type !== null)
						{
							$this->writer->startElement('Property');
							$this->writer->writeAttribute('Name', ucfirst($k));
							$this->writer->writeAttribute('Type', $type);
							$this->writer->endElement();
						}
					}
				}
			}
		}
	}

	protected function buildComplexTypes()
	{
		do
		{
			$name  = key($this->complexTypes);
			$class = array_shift($this->complexTypes);

			if($class !== null && !in_array($name, $this->typesAdded))
			{
				$this->writer->startElement('ComplexType');
				$this->writer->writeAttribute('Name', ucfirst($name));

				$this->buildEntity($class);

				$this->writer->endElement();

				$this->typesAdded[] = $name;
			}
		}
		while($class !== null);
	}

	protected function getType($value)
	{
		$type = null;

		switch($value)
		{
			case 'byte':
				$type = 'Edm.Byte';
				break;

			case 'bool':
			case 'boolean':
				$type = 'Edm.Boolean';
				break;

			case 'int':
			case 'integer':
				$type = 'Edm.Int32';
				break;

			case 'long':
			case 'float':
			case 'double':
				$type = 'Edm.Double';
				break;

			case 'string':
				$type = 'Edm.String';
				break;

			case 'datetime':
				$type = 'Edm.Date';
				break;

			case 'array':
				$type = 'Collection(Edm.String)';
				break;

			default:
				// we have a complex type or an array
				try
				{
					if(substr($value, 0, 6) == 'array<')
					{
						$type = substr($value, 6, -1);
						$type = 'Collection(' . $this->getType($type) . ')';
					}
					else
					{
						$class = new ReflectionClass($value);
						if($class->implementsInterface('PSX\Data\RecordInterface'))
						{
							$record = $class->newInstance();
							$type   = $this->namespace . '.' . $record->getName();

							if(!isset($this->complexTypes[$record->getName()]))
							{
								$this->complexTypes[$record->getName()] = $class;
							}
						}
					}
				}
				catch(ReflectionException $e)
				{
					// class does not exist
				}
				break;
		}

		return $type;
	}
}

