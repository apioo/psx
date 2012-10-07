<?php
/*
 *  $Id: WriterFactory.php 498 2012-06-02 19:21:00Z k42b3.x@googlemail.com $
 *
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2012 Christoph Kappestein <k42b3.x@gmail.com>
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

/**
 * PSX_Data_WriterFactory
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Data
 * @version    $Revision: 498 $
 */
class PSX_Data_WriterFactory
{
	public static function getWriterTypeByContentType($contentType)
	{
		$writerType = null;

		switch(true)
		{
			case (stripos($contentType, PSX_Data_Writer_Atom::$mime) !== false):

				$writerType = PSX_Data_WriterInterface::ATOM;
				break;

			case (stripos($contentType, PSX_Data_Writer_Form::$mime) !== false):

				$writerType = PSX_Data_WriterInterface::FORM;
				break;

			default:
			case (stripos($contentType, PSX_Data_Writer_Json::$mime) !== false):

				$writerType = PSX_Data_WriterInterface::JSON;
				break;

			case (stripos($contentType, PSX_Data_Writer_Rss::$mime) !== false):

				$writerType = PSX_Data_WriterInterface::RSS;
				break;

			case (stripos($contentType, PSX_Data_Writer_Xml::$mime) !== false):

				$writerType = PSX_Data_WriterInterface::XML;
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
			case PSX_Data_WriterInterface::ATOM:

				header('Content-type: ' . PSX_Data_Writer_Atom::$mime);

				$writer = new PSX_Data_Writer_Atom();
				break;

			case PSX_Data_WriterInterface::FORM:

				header('Content-type: ' . PSX_Data_Writer_Form::$mime);

				$writer = new PSX_Data_Writer_Form();
				break;

			case PSX_Data_WriterInterface::JSON:

				header('Content-type: ' . PSX_Data_Writer_Json::$mime);

				$writer = new PSX_Data_Writer_Json();
				break;

			case PSX_Data_WriterInterface::RSS:

				header('Content-type: ' . PSX_Data_Writer_Rss::$mime);

				$writer = new PSX_Data_Writer_Rss();
				break;

			case PSX_Data_WriterInterface::XML:

				header('Content-type: ' . PSX_Data_Writer_Xml::$mime);

				$writer = new PSX_Data_Writer_Xml();
				break;
		}

		return $writer;
	}
}
