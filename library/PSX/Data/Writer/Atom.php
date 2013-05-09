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
use PSX\Atom\Writer;
use PSX\Data\RecordInterface;
use PSX\Data\ResultSet;
use PSX\Data\WriterInterface;
use PSX\Data\WriterResult;
use PSX\Exception;
use XMLWriter;

/**
 * Atom
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Atom implements WriterInterface
{
	public static $mime = 'application/atom+xml';

	public $writerResult;

	protected $writer;

	public function setConfig($title, $id, DateTime $updated)
	{
		$this->writer = new Writer($title, $id, $updated);
	}

	public function write(RecordInterface $record)
	{
		$this->writerResult = new WriterResult(WriterInterface::ATOM, $this);

		if($record instanceof ResultSet)
		{
			foreach($record->entry as $entry)
			{
				$entry = $entry->export($this->writerResult);
				$entry->close();
			}

			echo $this->writer->toString();
		}
		else
		{
			$entry = $record->export($this->writerResult);

			echo $entry->toString();
		}
	}

	public function createEntry()
	{
		if($this->writer !== null)
		{
			return $this->writer->createEntry();
		}
		else
		{
			return new Writer\Entry();
		}
	}

	public function __call($name, $args)
	{
		if($this->writer !== null)
		{
			return call_user_func_array(array($this->writer, $name), $args);
		}
		else
		{
			throw new Exception('Writer is not initialized');
		}
	}
}
