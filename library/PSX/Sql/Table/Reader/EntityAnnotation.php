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

use ReflectionClass;
use PSX\Sql\TableInterface;
use PSX\Sql\Table\Definition;
use PSX\Sql\Table\ReaderInterface;
use PSX\Util\Annotation;

/**
 * EntityAnnotation
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class EntityAnnotation implements ReaderInterface
{
	public function getTableDefinition($class)
	{
		$class   = new ReflectionClass($class);
		$name    = $this->getNameFromEntity($class);
		$columns = $this->getColumnsFromEntity($class);

		// @todo try to read connections

		return new Definition($name, $columns);
	}

	protected function getNameFromEntity(ReflectionClass $class)
	{
		$doc   = Annotation::parse($class->getDocComment());
		$table = $doc->getFirstAnnotation('Table');
		$name  = null;

		if(!empty($table))
		{
			$attributes = Annotation::parseAttributes($table);

			if(isset($attributes['name']))
			{
				$name = $attributes['name'];
			}
		}

		if(empty($name))
		{
			$name = $class->getShortName();
		}

		return $name;
	}

	protected function getColumnsFromEntity(ReflectionClass $class)
	{
		$properties = $class->getProperties();
		$columns    = array();

		foreach($properties as $property)
		{
			$doc    = Annotation::parse($property->getDocComment());
			$column = $doc->getFirstAnnotation('Column');

			if(!empty($column))
			{
				$columnName  = null;
				$columnValue = null;
				$attributes  = Annotation::parseAttributes($column);

				if(isset($attributes['type']))
				{
					$columnValue = $this->getColumnTypeValue($attributes['type']);
				}

				if(isset($attributes['name']))
				{
					$columnName = $attributes['name'];
				}

				if($columnValue !== null)
				{
					if($columnName === null)
					{
						$columnName = $property->getName();
					}

					if($doc->hasAnnotation('Id'))
					{
						$columnValue|= TableInterface::PRIMARY_KEY;
					}

					if($doc->hasAnnotation('GeneratedValue'))
					{
						$columnValue|= TableInterface::AUTO_INCREMENT;
					}

					$columns[$columnName] = $columnValue;
				}
			}
		}

		return $columns;
	}

	protected function getColumnTypeValue($value)
	{
		switch($value)
		{
			case 'integer':
				return TableInterface::TYPE_INT;

			case 'smallint':
				return TableInterface::TYPE_SMALLINT;

			case 'bigint':
				return TableInterface::TYPE_BIGINT;

			case 'string':
				return TableInterface::TYPE_VARCHAR;

			case 'text':
			case 'array':
			case 'object':
				return TableInterface::TYPE_TEXT;

			case 'decimal':
				return TableInterface::TYPE_DECIMAL;

			case 'boolean':
				return TableInterface::TYPE_TINYINT | 1;

			case 'datetime':
				return TableInterface::TYPE_DATETIME;

			case 'date':
				return TableInterface::TYPE_DATE;

			case 'time':
				return TableInterface::TYPE_TIME;

			case 'float':
				return TableInterface::TYPE_FLOAT;
		}

		return null;
	}
}
