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

namespace PSX\Data\Transformer;

use DOMDocument;
use DOMElement;
use InvalidArgumentException;
use PSX\Data\Reader\Xml;
use PSX\Data\TransformerInterface;
use RuntimeException;

/**
 * Transforms an incomming SOAP request
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Soap extends XmlArray
{
	const ENVELOPE_NS = 'http://schemas.xmlsoap.org/soap/envelope/';

	protected $namespace;

	public function __construct($namespace)
	{
		$this->namespace = $namespace;
	}

	public function accept($contentType)
	{
		return $contentType == 'application/soap+xml';
	}

	public function transform($data)
	{
		if(!$data instanceof DOMDocument)
		{
			throw new InvalidArgumentException('Data must be an instanceof DOMDocument');
		}

		return $this->extractBody($data->documentElement);
	}

	protected function extractBody(DOMElement $element)
	{
		$body = $element->getElementsByTagNameNS(self::ENVELOPE_NS, 'Body')->item(0);

		if($body instanceof DOMElement)
		{
			$root = $this->findFirstElement($body);

			if($root instanceof DOMElement)
			{
				return $this->recToXml($root);;
			}

			return array();
		}
		else
		{
			throw new RuntimeException('Found no SOAP (' . self::ENVELOPE_NS . ') Body element');
		}
	}

	protected function findFirstElement(DOMElement $element)
	{
		foreach($element->childNodes as $childNode)
		{
			if($childNode->nodeType == XML_ELEMENT_NODE && $childNode->namespaceURI == $this->namespace)
			{
				return $childNode;
			}
		}

		return null;
	}
}
