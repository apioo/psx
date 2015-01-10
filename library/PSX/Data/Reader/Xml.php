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

namespace PSX\Data\Reader;

use DOMDocument;
use Psr\Http\Message\MessageInterface;
use PSX\Data\ReaderAbstract;

/**
 * Xml
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Xml extends ReaderAbstract
{
	public static $mediaTypes = array(
		'text/xml',
		'application/xml',
		'text/xml-external-parsed-entity',
		'application/xml-external-parsed-entity',
		'application/xml-dtd'
	);

	public function read(MessageInterface $message)
	{
		$dom = new DOMDocument();
		$dom->encoding = 'UTF-8';
		$dom->loadXML((string) $message->getBody());

		return $dom;
	}

	public function isContentTypeSupported($contentType)
	{
		return self::isXmlMediaContentType($contentType);
	}

	public static function isXmlMediaContentType($contentType)
	{
		return in_array($contentType, self::$mediaTypes) || substr($contentType, -4) == '+xml' || substr($contentType, -4) == '/xml';
	}
}
