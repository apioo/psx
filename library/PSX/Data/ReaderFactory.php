<?php
/*
 *  $Id: ReaderFactory.php 512 2012-06-07 15:03:09Z k42b3.x@googlemail.com $
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
 * PSX_Data_ReaderFactory
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Data
 * @version    $Revision: 512 $
 */
class PSX_Data_ReaderFactory
{
	public static function getReaderTypeByContentType($contentType)
	{
		$readerType = null;

		switch(true)
		{
			case (stripos($contentType, PSX_Data_Reader_Form::$mime) !== false):

				$readerType = PSX_Data_ReaderInterface::FORM;
				break;

			case (stripos($contentType, PSX_Data_Reader_Json::$mime) !== false):

				$readerType = PSX_Data_ReaderInterface::JSON;
				break;

			case (stripos($contentType, PSX_Data_Reader_Multipart::$mime) !== false):

				$readerType = PSX_Data_ReaderInterface::MULTIPART;
				break;

			case (stripos($contentType, PSX_Data_Reader_Xml::$mime) !== false):

				$readerType = PSX_Data_ReaderInterface::XML;
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
			case PSX_Data_ReaderInterface::DOM:

				$reader = new PSX_Data_Reader_Dom();
				break;

			case PSX_Data_ReaderInterface::FORM:

				$reader = new PSX_Data_Reader_Form();
				break;

			case PSX_Data_ReaderInterface::GPC:

				$reader = new PSX_Data_Reader_Gpc();
				break;

			case PSX_Data_ReaderInterface::JSON:

				$reader = new PSX_Data_Reader_Json();
				break;

			case PSX_Data_ReaderInterface::MULTIPART:

				$reader = new PSX_Data_Reader_Multipart();
				break;

			case PSX_Data_ReaderInterface::RAW:

				$reader = new PSX_Data_Reader_Raw();
				break;

			case PSX_Data_ReaderInterface::XML:

				$reader = new PSX_Data_Reader_Xml();
				break;
		}

		return $reader;
	}
}