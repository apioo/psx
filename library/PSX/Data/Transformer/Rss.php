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
use InvalidArgumentException;
use PSX\Http\MediaType;
use PSX\Data\TransformerInterface;

/**
 * Rss
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Rss implements TransformerInterface
{
	public function accept(MediaType $contentType)
	{
		return $contentType->getName() == 'application/rss+xml';
	}

	public function transform($data)
	{
		if(!$data instanceof DOMDocument)
		{
			throw new InvalidArgumentException('Data must be an instanceof DOMDocument');
		}

		$name = strtolower($data->documentElement->localName);

		if($name == 'rss')
		{
			$channel = $data->documentElement->getElementsByTagName('channel');

			if($channel->item(0) instanceof DOMElement)
			{
				return $this->parseChannelElement($channel->item(0));
			}
			else
			{
				throw new InvalidArgumentException('Found no channel element');
			}
		}
		else if($name == 'item')
		{
			return array(
				'item' => array($this->parseItemElement($data->documentElement))
			);
		}
		else
		{
			throw new InvalidArgumentException('Found no rss or item element');
		}
	}

	protected function parseChannelElement(DOMElement $channel)
	{
		$result     = array('type' => 'rss');
		$childNodes = $channel->childNodes;

		for($i = 0; $i < $childNodes->length; $i++)
		{
			$item = $childNodes->item($i);

			if($item->nodeType != XML_ELEMENT_NODE)
			{
				continue;
			}

			$name = strtolower($item->localName);

			switch($name)
			{
				case 'title':
				case 'link':
				case 'description':
				case 'language':
				case 'copyright':
				case 'generator':
				case 'docs':
				case 'ttl':
				case 'image':
				case 'rating':
					$result[$name] = $item->nodeValue;
					break;

				case 'managingeditor':
					$result['managingEditor'] = $item->nodeValue;
					break;

				case 'webmaster':
					$result['webMaster'] = $item->nodeValue;
					break;

				case 'category':
					$result['category'] = self::categoryConstruct($item);
					break;

				case 'pubdate':
					$result['pubDate'] = $item->nodeValue;
					break;

				case 'lastbuilddate':
					$result['lastBuildDate'] = $item->nodeValue;
					break;

				case 'item':
					$result['item'][] = $this->parseItemElement($item);
					break;
			}
		}

		return $result;
	}

	protected function parseItemElement(DOMElement $element)
	{
		$result = array('type' => 'item');

		for($i = 0; $i < $element->childNodes->length; $i++)
		{
			$item = $element->childNodes->item($i);

			if($item->nodeType != XML_ELEMENT_NODE)
			{
				continue;
			}

			$name = strtolower($item->localName);

			switch($name)
			{
				case 'title':
				case 'link':
				case 'description':
				case 'author':
				case 'comments':
				case 'guid':
					$result[$name] = $item->nodeValue;
					break;

				case 'category':
					$result[$name] = self::categoryConstruct($item);
					break;

				case 'enclosure':
					$result['enclosure'] = array(
						'url'    => $item->getAttribute('url'),
						'length' => $item->getAttribute('length'),
						'type'   => $item->getAttribute('type'),
					);
					break;

				case 'pubdate':
					$result['pubDate'] = $item->nodeValue;
					break;

				case 'source':
					$result['source'] = array(
						'text' => $item->nodeValue,
						'url'  => $item->getAttribute('url'),
					);
					break;
			}
		}

		return $result;
	}

	public static function categoryConstruct(DOMElement $category)
	{
		return array(
			'text'   => $category->nodeValue,
			'domain' => $category->getAttribute('domain'),
		);
	}
}
