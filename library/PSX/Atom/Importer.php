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

namespace PSX\Atom;

use DateTime;
use DOMDocument;
use DOMElement;
use InvalidArgumentException;
use PSX\Data\RecordInterface;
use PSX\Data\Record\ImporterInterface;
use PSX\Atom\Entry;
use PSX\Atom\Text;
use PSX\Atom\Person;
use PSX\Atom\Category;
use PSX\Atom\Link;

/**
 * Importer
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Importer implements ImporterInterface
{
	public static $xmlMediaTypes = array(
		'text/xml',
		'application/xml',
		'text/xml-external-parsed-entity',
		'application/xml-external-parsed-entity',
		'application/xml-dtd'
	);

	public function import($record, $data)
	{
		if($data instanceof DOMDocument)
		{
			$data = $data->documentElement;
		}
		else if($data instanceof DOMElement)
		{
		}
		else
		{
			throw new InvalidArgumentException('Data must be an instanceof DOMDocument or DOMElement');
		}

		$name = strtolower($data->localName);

		if($name == 'feed')
		{
			$this->parseFeedElement($data, $record);
		}
		else if($name == 'entry')
		{
			$entry    = new Entry();
			$importer = new EntryImporter();
			$importer->import($entry, $data);

			$record->add($entry);
		}
		else
		{
			throw new InvalidArgumentException('Found no feed or entry element');
		}

		return $record;
	}

	protected function parseFeedElement(DOMElement $feed, RecordInterface $record)
	{
		for($i = 0; $i < $feed->childNodes->length; $i++)
		{
			$item = $feed->childNodes->item($i);

			if($item->nodeType != XML_ELEMENT_NODE)
			{
				continue;
			}

			$name = strtolower($item->localName);

			switch($name)
			{
				case 'author':
					$record->addAuthor(self::personConstruct($item));
					break;

				case 'contributor':
					$record->addContributor(self::personConstruct($item));
					break;

				case 'category':
					$record->addCategory(self::categoryConstruct($item));
					break;

				case 'generator':
					$record->setGenerator(new Generator($item->nodeValue, $item->getAttribute('uri'), $item->getAttribute('version')));
					break;

				case 'icon':
					$record->setIcon($item->nodeValue);
					break;

				case 'logo':
					$record->setLogo($item->nodeValue);
					break;

				case 'id':
					$record->setId($item->nodeValue);
					break;

				case 'rights':
					$record->setRights($item->nodeValue);
					break;

				case 'title':
					$record->setTitle($item->nodeValue);
					break;

				case 'updated':
					$record->setUpdated(self::dateConstruct($item));
					break;

				case 'link':
					$record->addLink(self::linkConstruct($item));
					break;

				case 'subtitle':
					$record->setSubTitle(self::textConstruct($item));
					break;

				case 'entry':
					$entry    = new Entry();
					$importer = new EntryImporter();
					$importer->import($entry, $item);

					$record->add($entry);
					break;
			}
		}
	}

	public static function textConstruct(DOMElement $el)
	{
		$type = strtolower($el->getAttribute('type'));

		$text = new Text();
		$text->setType($type);

		if(empty($type) || $type == 'text' || $type == 'html' || substr($type, 0, 5) == 'text/')
		{
			$content = $el->nodeValue;
		}
		else if($type == 'xhtml' || in_array($type, self::$xmlMediaTypes) || substr($type, -4) == '+xml' || substr($type, -4) == '/xml')
		{
			// get first child element
			$child = null;
			foreach($el->childNodes as $node)
			{
				if($node->nodeType == XML_ELEMENT_NODE)
				{
					$child = $node;
					break;
				}
			}

			if($child !== null)
			{
				$content = $el->ownerDocument->saveXML($child);
			}
		}
		else
		{
			$content = base64_decode($el->nodeValue);
		}

		$text->setContent($content);

		return $text;
	}

	public static function personConstruct(DOMElement $el)
	{
		$person = new Person();

		for($i = 0; $i < $el->childNodes->length; $i++)
		{
			$item = $el->childNodes->item($i);

			if($item->nodeType != XML_ELEMENT_NODE)
			{
				continue;
			}

			$name = strtolower($item->nodeName);

			switch($name)
			{
				case 'name':
					$person->setName($item->nodeValue);
					break;

				case 'uri':
					$person->setUri($item->nodeValue);
					break;

				case 'email':
					$person->setEmail($item->nodeValue);
					break;
			}
		}

		return $person;
	}

	public static function dateConstruct(DOMElement $date)
	{
		return new DateTime($date->nodeValue);
	}

	public static function categoryConstruct(DOMElement $el)
	{
		$category = new Category();
		$category->setTerm($el->getAttribute('term'));
		$category->setScheme($el->getAttribute('scheme'));
		$category->setLabel($el->getAttribute('label'));

		return $category;
	}

	public static function linkConstruct(DOMElement $el)
	{
		$link = new Link();
		$link->setHref($el->getAttribute('href'));
		$link->setRel($el->getAttribute('rel'));
		$link->setType($el->getAttribute('type'));
		$link->setHrefLang($el->getAttribute('hreflang'));
		$link->setTitle($el->getAttribute('title'));
		$link->setLength($el->getAttribute('length'));

		return $link;
	}
}
