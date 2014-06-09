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

namespace PSX\Dependency;

use PSX\Data\Reader;
use PSX\Data\ReaderFactory;
use PSX\Data\Writer;
use PSX\Data\WriterFactory;

/**
 * Data
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
trait Data
{
	/**
	 * @return PSX\Data\ReaderFactory
	 */
	public function getReaderFactory()
	{
		$reader = new ReaderFactory();
		$reader->addReader(new Reader\Json());
		$reader->addReader(new Reader\Dom());
		$reader->addReader(new Reader\Form());
		$reader->addReader(new Reader\Gpc());
		$reader->addReader(new Reader\Multipart());
		$reader->addReader(new Reader\Raw());
		$reader->addReader(new Reader\Xml());

		return $reader;
	}

	/**
	 * @return PSX\Data\WriterFactory
	 */
	public function getWriterFactory()
	{
		$writer = new WriterFactory();
		$writer->addWriter(new Writer\Json());
		$writer->addWriter(new Writer\Html($this->get('template'), $this->get('reverse_router')));
		$writer->addWriter(new Writer\Atom());
		$writer->addWriter(new Writer\Form());
		$writer->addWriter(new Writer\Jsonp());
		$writer->addWriter(new Writer\Rss());
		$writer->addWriter(new Writer\Xml());

		return $writer;
	}
}
