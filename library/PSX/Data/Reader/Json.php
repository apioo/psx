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

namespace PSX\Data\Reader;

use PSX\Data\ReaderAbstract;
use PSX\Data\Record\DefaultImporter;
use PSX\Http\Message;
use PSX\Json as JsonParser;

/**
 * Json
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Json extends ReaderAbstract
{
	public static $mime = 'application/json';

	public function read(Message $message)
	{
		$json = JsonParser::decode($message->getBody());

		return $json;
	}

	public function isContentTypeSupported($contentType)
	{
		return stripos($contentType, self::$mime) !== false;
	}

	public function getDefaultImporter()
	{
		$importer = parent::getDefaultImporter();

		return $importer === null ? new DefaultImporter() : $importer;
	}
}
