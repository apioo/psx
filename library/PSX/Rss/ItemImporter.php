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
class ItemImporter implements ImporterInterface
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

		$this->parseItemElement($data, $record);
	}

	protected function parseItemElement(DOMElement $element, RecordInterface $record)
	{
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
					$record->setTitle($item->nodeValue);
					break;

				case 'link':
					$record->setLink($item->nodeValue);
					break;

				case 'description':
					$record->setDescription($item->nodeValue);
					break;

				case 'author':
					$record->setAuthor($item->nodeValue);
					break;

				case 'comments':
					$record->setComments($item->nodeValue);
					break;

				case 'guid':
					$record->setGuid($item->nodeValue);
					break;

				case 'category':
					$record->addCategory(Importer::categoryConstruct($item));
					break;

				case 'enclosure':
					$record->setEnclosure(new Enclosure($item->getAttribute('url'), 
						$item->getAttribute('length'), 
						$item->getAttribute('type')));
					break;

				case 'pubdate':
					$record->setPubDate(new DateTime($item->nodeValue));
					break;

				case 'source':
					$record->setSource(new Source($item->nodeValues, $item->getAttribute('url')));
					break;
			}
		}
	}
}
