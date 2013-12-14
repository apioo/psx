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

namespace PSX\Rss;

use DateTime;
use DOMDocument;
use DOMElement;
use InvalidArgumentException;
use PSX\Data\RecordInterface;
use PSX\Data\Record\ImporterInterface;
use PSX\Rss\Category;
use PSX\Rss\Cloud;

/**
 * Importer
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Importer implements ImporterInterface
{
	public function import(RecordInterface $record, $data)
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

		$channel = $data->getElementsByTagName('channel');

		if($channel->length > 0)
		{
			$this->parseChannelElement($channel->item(0), $record);
		}
		else
		{
			throw new InvalidDataException('No channel element found');
		}
	}

	protected function parseChannelElement(DOMElement $channel, RecordInterface $record)
	{
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
					$record->setTitle($item->nodeValue);
					break;

				case 'link':
					$record->setLink($item->nodeValue);
					break;

				case 'description':
					$record->setDescription($item->nodeValue);
					break;

				case 'language':
					$record->setLanguage($item->nodeValue);
					break;

				case 'copyright':
					$record->setCopyright($item->nodeValue);
					break;

				case 'managingeditor':
					$record->setManagingEditor($item->nodeValue);
					break;

				case 'webmaster':
					$record->setWebMaster($item->nodeValue);
					break;

				case 'generator':
					$record->setGenerator($item->nodeValue);
					break;

				case 'docs':
					$record->setDocs($item->nodeValue);
					break;

				case 'ttl':
					$record->setTtl($item->nodeValue);
					break;

				case 'image':
					$record->setImage($item->nodeValue);
					break;

				case 'rating':
					$record->setRating($item->nodeValue);
					break;

				case 'skiphours':
					$record->setSkiphours($item->nodeValue);
					break;

				case 'skipdays':
					$record->setSkipdays($item->nodeValue);
					break;

				case 'category':
					$record->addCategory(self::categoryConstruct($item));
					break;

				case 'pubdate':
					$record->setPubDate(new DateTime($item->nodeValue));
					break;

				case 'lastbuilddate':
					$record->setLastBuildDate(new DateTime($item->nodeValue));
					break;

				case 'cloud':
					$record->setCloud(self::cloudConstruct($item));
					break;

				case 'item':
					$entry    = new Item();
					$importer = new ItemImporter();
					$importer->import($entry, $item);

					$record->add($entry);
					break;
			}
		}
	}

	public static function categoryConstruct(DOMElement $category)
	{
		return new Category($category->nodeValue, $category->getAttribute('domain'));
	}

	public static function cloudConstruct(DOMElement $cloud)
	{
		return new Cloud($cloud->getAttribute('domain'), 
			$cloud->getAttribute('port'), 
			$cloud->getAttribute('path'), 
			$cloud->getAttribute('registerProcedure'), 
			$cloud->getAttribute('protocol'));
	}
}
