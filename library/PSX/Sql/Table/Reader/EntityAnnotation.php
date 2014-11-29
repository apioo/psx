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

namespace PSX\Sql\Table\Reader;

use Doctrine\ORM\EntityManager;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use ReflectionClass;
use PSX\Sql\TableInterface;
use PSX\Sql\Table\Definition;
use PSX\Sql\Table\ReaderInterface;

/**
 * EntityAnnotation
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class EntityAnnotation implements ReaderInterface
{
	protected $em;

	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}

	public function getTableDefinition($class)
	{
		$metaData = $this->em->getMetadataFactory()->getMetadataFor($class);
		$columns  = $this->getEntityColumns($metaData);

		return new Definition($metaData->getTableName(), $columns);
	}

	protected function getEntityColumns(ClassMetadata $metaData)
	{
		$columns = $metaData->getColumnNames();
		$result  = array();

		foreach($columns as $columnName)
		{
			$type = $this->getColumnTypeValue($metaData->getTypeOfField($columnName));

			if($metaData->isIdentifier($metaData->getFieldName($columnName)))
			{
				$type|= TableInterface::PRIMARY_KEY;

				if($metaData->isIdGeneratorIdentity() || $metaData->isIdGeneratorSequence())
				{
					$type|= TableInterface::AUTO_INCREMENT;
				}
			}

			$result[$columnName] = $type;
		}

		return $result;
	}

	protected function getColumnTypeValue($type)
	{
		switch($type)
		{
			case 'integer':
				return TableInterface::TYPE_INT;

			case 'smallint':
				return TableInterface::TYPE_SMALLINT;

			case 'bigint':
				return TableInterface::TYPE_BIGINT;

			case 'text':
			case 'array':
			case 'object':
				return TableInterface::TYPE_TEXT;

			case 'decimal':
				return TableInterface::TYPE_DECIMAL;

			case 'boolean':
				return TableInterface::TYPE_BOOLEAN;

			case 'datetime':
				return TableInterface::TYPE_DATETIME;

			case 'date':
				return TableInterface::TYPE_DATE;

			case 'time':
				return TableInterface::TYPE_TIME;

			case 'float':
				return TableInterface::TYPE_FLOAT;

			default:
			case 'string':
				return TableInterface::TYPE_VARCHAR;
		}
	}
}
