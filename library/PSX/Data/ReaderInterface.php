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
use PSX\Http\MessageInterface;

/**
 * ReaderInterface
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
interface ReaderInterface
{
	const FORM = 'PSX\Data\Reader\Form';
	const JSON = 'PSX\Data\Reader\Json';
	const XML  = 'PSX\Data\Reader\Xml';

	/**
	 * Transforms the $request into an parseable form this can be an array
	 * or DOMDocument etc.
	 *
	 * @param PSX\Http\MessageInterface $message
	 * @return mixed
	 */
	public function read(MessageInterface $message);

	/**
	 * Returns whether the content type is supported by this reader
	 *
	 * @param PSX\Http\MediaType $contentType
	 * @return boolean
	 */
	public function isContentTypeSupported(MediaType $contentType);
}
