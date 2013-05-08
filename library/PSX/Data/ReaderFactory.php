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

namespace PSX\Data;

use PSX\Data\Reader;

/**
 * ReaderFactory
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ReaderFactory
{
	public static function getReaderTypeByContentType($contentType)
	{
		$readerType = null;

		switch(true)
		{
			case (stripos($contentType, Reader\Form::$mime) !== false):

				$readerType = ReaderInterface::FORM;
				break;

			case (stripos($contentType, Reader\Multipart::$mime) !== false):

				$readerType = ReaderInterface::MULTIPART;
				break;

			case (stripos($contentType, Reader\Xml::$mime) !== false):

				$readerType = ReaderInterface::XML;
				break;

			default:
			case (stripos($contentType, Reader\Json::$mime) !== false):

				$readerType = ReaderInterface::JSON;
				break;
		}

		return $readerType;
	}

	public static function getReaderByContentType($contentType, $fallbackReaderType = null)
	{
		$reader = self::getReader(self::getReaderTypeByContentType($contentType));

		return $reader !== null ? $reader : self::getReader($fallbackReaderType);
	}

	public static function getReader($readerType)
	{
		$reader = null;

		switch($readerType)
		{
			case ReaderInterface::DOM:

				$reader = new Reader\Dom();
				break;

			case ReaderInterface::FORM:

				$reader = new Reader\Form();
				break;

			case ReaderInterface::GPC:

				$reader = new Reader\Gpc();
				break;

			case ReaderInterface::JSON:

				$reader = new Reader\Json();
				break;

			case ReaderInterface::MULTIPART:

				$reader = new Reader\Multipart();
				break;

			case ReaderInterface::RAW:

				$reader = new Reader\Raw();
				break;

			case ReaderInterface::XML:

				$reader = new Reader\Xml();
				break;
		}

		return $reader;
	}
}