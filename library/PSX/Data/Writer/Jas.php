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

namespace PSX\Data\Writer;

use DateTime;
use PSX\ActivityStream;
use PSX\Atom\Writer;
use PSX\Data\RecordInterface;
use PSX\Data\ResultSet;
use PSX\Data\WriterInterface;
use PSX\Data\WriterResult;
use PSX\Exception;
use XMLWriter;

/**
 * Json Activity Stream writer
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Jas extends Json
{
	public static $mime = 'application/stream+json';

	public $writerResult;

	public function write(RecordInterface $record)
	{
		$this->writerResult = new WriterResult(WriterInterface::JAS, $this);

		if($record instanceof ResultSet)
		{
			$items = array();

			foreach($record->entry as $entry)
			{
				$items[] = $entry->export($this->writerResult);
			}

			$collection = new ActivityStream\Collection($items);

			if(isset($record->totalItems))
			{
				$collection->setTotalItems($record->totalItems);
			}

			if(isset($record->itemsPerPage))
			{
				$collection->setItemsPerPage($record->itemsPerPage);
			}

			if(isset($record->startIndex))
			{
				$collection->setStartIndex($record->startIndex);
			}

			$collection->setItems($items);

			parent::write($collection);
		}
		else
		{
			$object = $record->export($this->writerResult);

			parent::write($object);
		}
	}
}
