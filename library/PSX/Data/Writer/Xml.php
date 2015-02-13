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

use PSX\Data\RecordInterface;
use PSX\Data\WriterInterface;
use PSX\Http\MediaType;
use PSX\Xml\Writer;
use XMLWriter;

/**
 * Xml
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Xml extends ArrayAbstract
{
	public static $mime = 'application/xml';

	protected $writer;

	/**
	 *
	 * If an writer is given the result gets written to the XMLWriter and the
	 * write method returns null. Otherwise the write method returns the xml as
	 * string
	 *
	 * @param XMLWriter $writer
	 */
	public function __construct(XMLWriter $writer = null)
	{
		$this->writer = $writer;
	}

	public function write(RecordInterface $record)
	{
		$writer = new Writer($this->writer);
		$writer->setRecord($record->getRecordInfo()->getName(), $this->export($record));

		return $this->writer === null ? $writer->toString() : null;
	}

	public function isContentTypeSupported(MediaType $contentType)
	{
		return $contentType->getName() == self::$mime;
	}

	public function getContentType()
	{
		return self::$mime;
	}
}
