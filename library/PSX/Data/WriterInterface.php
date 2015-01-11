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

namespace PSX\Data;

use PSX\Http\MediaType;

/**
 * WriterInterface
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
interface WriterInterface
{
	const ATOM  = 'PSX\Data\Writer\Atom';
	const FORM  = 'PSX\Data\Writer\Form';
	const HTML  = 'PSX\Data\Writer\Html';
	const JSON  = 'PSX\Data\Writer\Json';
	const JSONP = 'PSX\Data\Writer\Jsonp';
	const RSS   = 'PSX\Data\Writer\Rss';
	const SOAP  = 'PSX\Data\Writer\Soap';
	const XML   = 'PSX\Data\Writer\Xml';

	/**
	 * Returns the string representation of this record from the writer
	 *
	 * @param PSX\Data\RecordInterface
	 * @return string
	 */
	public function write(RecordInterface $record);

	/**
	 * Returns whether the content type is supported by this writer
	 *
	 * @param PSX\Http\MediaType $contentType
	 * @return boolean
	 */
	public function isContentTypeSupported(MediaType $contentType);

	/**
	 * Returns the content type of this writer
	 *
	 * @return string
	 */
	public function getContentType();
}
