<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Data\Writer;

use DateTime;
use PSX\Data\RecordInterface;
use PSX\Data\WriterInterface;

/**
 * ArrayAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class ArrayAbstract implements WriterInterface
{
	public function export(RecordInterface $record)
	{
		return $this->getRecData($record->getRecordInfo()->getFields());
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
					$data[$k] = $this->export($v);
				}
				else if($v instanceof DateTime)
				{
					$data[$k] = $v->format(DateTime::RFC3339);
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
