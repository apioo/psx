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
use PSX\Data\RecordInterface;
use PSX\Data\ResultSet;
use PSX\Rss\Writer;
use PSX\Data\WriterInterface;
use PSX\Data\WriterResult;
use PSX\Exception;
use XMLWriter;

/**
 * Rss
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Rss implements WriterInterface
{
	public static $mime = 'application/rss+xml';

	public $writerResult;

	protected $writer;

	public function setConfig($title, $link, $description)
	{
		$this->writer = new Writer($title, $link, $description);
	}

	public function write(RecordInterface $record)
	{
		$this->writerResult = new WriterResult(WriterInterface::RSS, $this);

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

	public function createItem()
	{
		if($this->writer !== null)
		{
			return $this->writer->createItem();
		}
		else
		{
			return new Writer\Item();
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

