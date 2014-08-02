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

use DateTime;
use Doctrine\Common\Persistence\Proxy;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use InvalidArgumentException;
use PSX\Data\Record as DataRecord;
use PSX\Data\Record\ImporterInterface;
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
class Entity implements ImporterInterface
{
	protected $em;

	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}

	public function accept($entity)
	{
		return $this->isEntity($entity);
	}

	public function import($entity, $data)
	{
		if(!$this->isEntity($entity))
		{
			throw new InvalidArgumentException('Entity must be an entity');
		}

		if(!is_array($data))
		{
			throw new InvalidArgumentException('Data must be an array');
		}

		$metaData = $this->em->getMetadataFactory()->getMetadataFor(get_class($entity));
		$fields   = $this->getEntityFields($entity, $data, $metaData);

		return new DataRecord($metaData->getTableName(), $fields);
	}

	protected function getEntityFields($entity, array $data, ClassMetadata $metaData)
	{
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

		// get all fields
		$fieldNames = $metaData->getFieldNames();
		$fields     = array();

		foreach($fieldNames as $fieldName)
		{
			if(!isset($data[$fieldName]))
			{
				continue;
			}

			$type  = $metaData->getTypeOfField($fieldName);
			$value = $this->getColumnTypeValue($type, $data[$fieldName]);

			$fields[$fieldName] = $value;
		}

		return $fields;
	}

	protected function getColumnTypeValue($type, $value)
	{
		switch($type)
		{
			case 'integer':
			case 'smallint':
			case 'bigint':
				return (int) $value;

			case 'decimal':
			case 'float':
				return (float) $value;

			case 'boolean':
				return $value === 'false' ? false : (bool) $value;

			case 'datetime':
			case 'date':
				return new DateTime($value);

			default:
				return $value;
		}
	}

	protected function isEntity($class)
	{
		if(is_object($class))
		{
			$class = ($class instanceof Proxy) ? get_parent_class($class) : get_class($class);
		}

		return !$this->em->getMetadataFactory()->isTransient($class);
	}
}
