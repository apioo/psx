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

use PSX\Data\Writer;

/**
 * WriterFactory
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class WriterFactory
{
	public static function getWriterTypeByContentType($contentType)
	{
		$writerType = null;

		switch(true)
		{
			case (stripos($contentType, Writer\Atom::$mime) !== false):

				$writerType = WriterInterface::ATOM;
				break;

			case (stripos($contentType, Writer\Form::$mime) !== false):

				$writerType = WriterInterface::FORM;
				break;

			case (stripos($contentType, Writer\Rss::$mime) !== false):

				$writerType = WriterInterface::RSS;
				break;

			case (stripos($contentType, Writer\Xml::$mime) !== false):

				$writerType = WriterInterface::XML;
				break;

			default:
			case (stripos($contentType, Writer\Json::$mime) !== false):

				$writerType = WriterInterface::JSON;
				break;
		}

		return $writerType;
	}

	public static function getWriterByContentType($contentType, $fallbackWriterType = null)
	{
		$writer = self::getWriter(self::getWriterTypeByContentType($contentType));

		return $writer !== null ? $writer : self::getWriter($fallbackWriterType);
	}

	public static function getWriter($writerType)
	{
		$writer = null;

		switch($writerType)
		{
			case WriterInterface::ATOM:

				header('Content-type: ' . Writer\Atom::$mime);

				$writer = new Writer\Atom();
				break;

			case WriterInterface::FORM:

				header('Content-type: ' . Writer\Form::$mime);

				$writer = new Writer\Form();
				break;

			case WriterInterface::JSON:

				header('Content-type: ' . Writer\Json::$mime);

				$writer = new Writer\Json();
				break;

			case WriterInterface::RSS:

				header('Content-type: ' . Writer\Rss::$mime);

				$writer = new Writer\Rss();
				break;

			case WriterInterface::XML:

				header('Content-type: ' . Writer\Xml::$mime);

				$writer = new Writer\Xml();
				break;
		}

		return $writer;
	}
}
