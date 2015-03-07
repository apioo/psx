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
use PSX\Data\Record as DataRecord;
use PSX\Data\Record\ImporterInterface;
use PSX\Sql\TableInterface;

/**
 * Importer wich imports data into an record based on an sql table class. Note 
 * this importer does not handle relations it simply uses all available columns
 * on the table
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Table implements ImporterInterface
{
	public function accept($table)
	{
		return $table instanceof TableInterface;
	}

	public function import($table, $data)
	{
		if(!$table instanceof TableInterface)
		{
			throw new InvalidArgumentException('Table must be an instanceof PSX\Sql\TableInterface');
		}

		if(!is_array($data))
		{
			throw new InvalidArgumentException('Data must be an array');
		}

		$columns = $table->getColumns();
		$data    = array_intersect_key($data, $columns);
		$fields  = array();

		foreach($data as $key => $value)
		{
			$fields[$key] = $this->convertType($value, $columns[$key]);
		}

		return new DataRecord(lcfirst($table->getDisplayName()), $fields);
	}

	protected function convertType($data, $type)
	{
		$length = $type & 0xFFFFF;
		$type   = (($type >> 20) & 0xFF) << 20;

		if($length > 0 && strlen($data) > $length)
		{
			$data = substr($data, 0, $length);
		}

		switch($type)
		{
			case TableInterface::TYPE_SMALLINT:
			case TableInterface::TYPE_INT:
			case TableInterface::TYPE_BIGINT:
				return (int) $data;
				break;

			case TableInterface::TYPE_DECIMAL:
			case TableInterface::TYPE_FLOAT:
				return (float) $data;
				break;

			case TableInterface::TYPE_BOOLEAN:
				return $data === 'false' ? false : (bool) $data;
				break;

			case TableInterface::TYPE_DATE:
			case TableInterface::TYPE_DATETIME:
				return new \DateTime($data);
				break;

			default:
				return $data;
				break;
		}
	}
}
