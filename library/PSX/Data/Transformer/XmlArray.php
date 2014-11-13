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
use InvalidArgumentException;

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
	protected $namespace;

	public function setNamespace($namespace)
	{
		$this->namespace = $namespace;
	}

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

			if($this->namespace !== null && $node->namespaceURI != $this->namespace)
			{
				continue;
			}

			if(isset($result[$node->localName]) && (!isset($result[$node->localName][0]) || !is_array($result[$node->localName][0])))
			{
				$result[$node->localName] = array($result[$node->localName]);
			}

			if($this->hasChildElements($node, $this->namespace))
			{
				$value = $this->recToXml($node);
			}
			else
			{
				if($this->namespace !== null && $this->hasChildElements($node, null))
				{
					// if we need an specific namespace and the node has child
					// elements add the complete node as value since we have no
					// idea howto handle the data
					$value = $node;
				}
				else
				{
					$value = $node->textContent;
				}
			}

			if(isset($result[$node->localName]) && is_array($result[$node->localName]))
			{
				$result[$node->localName][] = $value;
			}
			else
			{
				$result[$node->localName] = $value;
			}
		}

		return $result;
	}

	protected function hasChildElements(DOMElement $element, $namespace)
	{
		foreach($element->childNodes as $node)
		{
			if($node->nodeType === XML_ELEMENT_NODE && ($namespace === null || $node->namespaceURI == $namespace))
			{
				return true;
			}
		}

		return false;
	}
}
