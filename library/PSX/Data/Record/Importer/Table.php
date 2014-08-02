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
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
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
			case TableInterface::TYPE_TINYINT:
			case TableInterface::TYPE_SMALLINT:
			case TableInterface::TYPE_MEDIUMINT:
			case TableInterface::TYPE_INT:
			case TableInterface::TYPE_BIGINT:
			case TableInterface::TYPE_BIT:
			case TableInterface::TYPE_SERIAL:
				return (int) $data;
				break;

			case TableInterface::TYPE_DECIMAL:
			case TableInterface::TYPE_FLOAT:
			case TableInterface::TYPE_DOUBLE:
			case TableInterface::TYPE_REAL:
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
