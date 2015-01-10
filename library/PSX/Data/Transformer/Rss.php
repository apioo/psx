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

namespace PSX\Data\Transformer;

use DOMDocument;
use DOMElement;
use InvalidArgumentException;
use PSX\Data\TransformerInterface;

/**
 * Rss
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Rss implements TransformerInterface
{
	public function accept($contentType)
	{
		return $contentType == 'application/rss+xml';
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
