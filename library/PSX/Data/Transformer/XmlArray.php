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
use PSX\Data\Reader\Xml;
use PSX\Data\TransformerInterface;

/**
 * Takes an DOMDocument and formats it into an array structure which can be used
 * by an importer. Note this transformer should handle "Data oriented" xml 
 * documents since it looses all text nodes which are between elements and all 
 * attributes
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class XmlArray implements TransformerInterface
{
	public function accept($contentType)
	{
		return in_array($contentType, Xml::$mediaTypes) || substr($contentType, -4) == '+xml' || substr($contentType, -4) == '/xml';
	}

	public function transform($data)
	{
		if(!$data instanceof DOMDocument)
		{
			throw new InvalidArgumentException('Data must be an instanceof DOMDocument');
		}

		return $this->recToXml($data->documentElement);
	}

	protected function recToXml(DOMElement $element)
	{
		$result = array();

		foreach($element->childNodes as $node)
		{
			if($node->nodeType !== XML_ELEMENT_NODE)
			{
				continue;
			}

			if(isset($result[$node->nodeName]) && (!isset($result[$node->nodeName][0]) || !is_array($result[$node->nodeName][0])))
			{
				$result[$node->nodeName] = array($result[$node->nodeName]);
			}

			if($this->hasChildElements($node))
			{
				$value = $this->recToXml($node);
			}
			else
			{
				$value = $node->textContent;
			}

			if(isset($result[$node->nodeName]) && is_array($result[$node->nodeName]))
			{
				$result[$node->nodeName][] = $value;
			}
			else
			{
				$result[$node->nodeName] = $value;
			}
		}

		return $result;
	}

	protected function hasChildElements(DOMElement $element)
	{
		foreach($element->childNodes as $node)
		{
			if($node->nodeType === XML_ELEMENT_NODE)
			{
				return true;
			}
		}

		return false;
	}
}
