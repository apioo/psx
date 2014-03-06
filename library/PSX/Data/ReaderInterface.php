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

namespace PSX\Data;

use PSX\Data\Record\ImporterInterface;
use PSX\Http\Message as HttpMessage;

/**
 * ReaderInterface
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
interface ReaderInterface
{
	const DOM       = 'PSX\Data\Reader\Dom';
	const FORM      = 'PSX\Data\Reader\Form';
	const GPC       = 'PSX\Data\Reader\Gpc';
	const JSON      = 'PSX\Data\Reader\Json';
	const MULTIPART = 'PSX\Data\Reader\Multipart';
	const RAW       = 'PSX\Data\Reader\Raw';
	const XML       = 'PSX\Data\Reader\Xml';

	/**
	 * Transforms the $request into an parseable form this can be an array
	 * or DOMDocument etc.
	 *
	 * @param PSX\Http\Request $request
	 * @return mixed
	 */
	public function read(HttpMessage $message);

	/**
	 * Returns whether the content type is supported by this reader
	 *
	 * @param string $contentType
	 * @return boolean
	 */
	public function isContentTypeSupported($contentType);

	/**
	 * Returns the default importer of this reader
	 *
	 * @return PSX\Data\Record\ImporterInterface|null
	 */
	public function getDefaultImporter();

	/**
	 * Sets the default importer of this reader
	 *
	 * @param PSX\Data\Record\ImporterInterface
	 */
	public function setDefaultImporter(ImporterInterface $importer);

	/**
	 * Imports the data from the http message into the record using the default
	 * importer
	 *
	 * @return PSX\Data\RecordInterface
	 */
	public function import(RecordInterface $record, HttpMessage $message);
}
