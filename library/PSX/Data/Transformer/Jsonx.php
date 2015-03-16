<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace PSX\Data\Transformer;

use DOMDocument;
use DOMElement;
use PSX\Http\MediaType;
use PSX\Data\Reader\Xml;
use PSX\Data\TransformerInterface;
use InvalidArgumentException;

/**
 * Jsonx
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 * @see     https://tools.ietf.org/html/draft-rsalz-jsonx-00
 */
class Jsonx implements TransformerInterface
{
	public function accept(MediaType $contentType)
	{
		return $contentType->getName() == 'application/jsonx+xml';
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
		if($element->localName != 'object')
		{
			throw new InvalidArgumentException('Root element must be an object');
		}

		return $this->getValue($element);
	}

	protected function getValue(DOMElement $node)
	{
		switch($node->localName)
		{
			case 'object':
				return $this->getObject($node);

			case 'array':
				return $this->getArray($node);

			case 'boolean':
				return $node->textContent == 'false' ? false : (boolean) $node->textContent;

			case 'string':
				return $node->textContent;

			case 'number':
				return strpos($node->textContent, '.') !== false ? (float) $node->textContent : (int) $node->textContent;

			case 'null':
				return null;
		}
	}

	protected function getObject(DOMElement $element)
	{
		$result = new \stdClass();

		foreach($element->childNodes as $node)
		{
			if($node->nodeType !== XML_ELEMENT_NODE)
			{
				continue;
			}

			$name = $node->getAttribute('name');

			if(!empty($name))
			{
				$result->$name = $this->getValue($node);
			}
		}

		return $result;
	}

	protected function getArray(DOMElement $element)
	{
		$result = array();

		foreach($element->childNodes as $node)
		{
			if($node->nodeType !== XML_ELEMENT_NODE)
			{
				continue;
			}

			$result[] = $this->getValue($node);
		}

		return $result;
	}
}
