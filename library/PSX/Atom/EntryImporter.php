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

namespace PSX\Atom;

use DateTime;
use DOMDocument;
use DOMElement;
use DOMCdataSection;
use PSX\Atom;
use PSX\Data\Reader;
use PSX\Data\RecordInterface;
use PSX\Data\Record\ImporterInterface;
use PSX\Http;
use PSX\Http\GetRequest;
use PSX\Url;

/**
 * EntryImporter
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class EntryImporter implements ImporterInterface
{
	protected $fetchRemoteContent = false;
	protected $http;
	protected $dom;

	public function import($record, $data)
	{
		if($data instanceof DOMDocument)
		{
			$this->dom = $data;

			$data = $data->documentElement;
		}
		else if($data instanceof DOMElement)
		{
			$this->dom = $data->ownerDocument;
		}
		else
		{
			throw new InvalidArgumentException('Data must be an instanceof DOMDocument or DOMElement');
		}

		$this->parseEntryElement($data, $record);

		return $record;
	}

	public function setFetchRemoteContent($remoteContent)
	{
		if($remoteContent)
		{
			$this->fetchRemoteContent = true;

			if($remoteContent instanceof Http)
			{
				$this->http = $remoteContent;
			}
			else
			{
				$this->http = new Http();
			}
		}
		else
		{
			$this->fetchRemoteContent = false;
		}
	}

	protected function parseEntryElement(DOMElement $entry, RecordInterface $record)
	{
		for($i = 0; $i < $entry->childNodes->length; $i++)
		{
			$item = $entry->childNodes->item($i);

			if($item->nodeType != XML_ELEMENT_NODE)
			{
				continue;
			}

			$name = strtolower($item->localName);

			switch($name)
			{
				case 'author':
					$record->addAuthor(Importer::personConstruct($item));
					break;

				case 'contributor':
					$record->addContributor(Importer::personConstruct($item));
					break;

				case 'category':
					$record->addCategory(Importer::categoryConstruct($item));
					break;

				case 'content':
					$content = null;
					$type    = strtolower($item->getAttribute('type'));
					$src     = $item->getAttribute('src');

					if($this->fetchRemoteContent && !empty($src))
					{
						$this->fetchRemoteContent($item, new Url($src), $type);
					}

					$record->setContent(Importer::textConstruct($item));
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

				case 'published':
					$record->setPublished(Importer::dateConstruct($item));
					break;

				case 'updated':
					$record->setUpdated(Importer::dateConstruct($item));
					break;

				case 'link':
					$record->addLink(Importer::linkConstruct($item));
					break;

				case 'source':
					$dom  = new DOMDocument();
					$feed = $dom->createElementNS(Atom::$xmlns, 'feed');

					foreach($item->childNodes as $node)
					{
						// the source node must not contain entry elements
						if($node->nodeType == XML_ELEMENT_NODE && $node->nodeName != 'entry')
						{
							$feed->appendChild($dom->importNode($node, true));
						}
					}

					$dom->appendChild($feed);

					$atom     = new Atom();
					$importer = new Importer();
					$importer->import($atom, $dom);

					$record->setSource($atom);
					break;

				case 'summary':
					$record->setSummary(Importer::textConstruct($item));
					break;
			}
		}
	}

	protected function fetchRemoteContent(DOMElement $parent, Url $url, $type)
	{
		if($url->getScheme() == 'http' || $url->getScheme() == 'https')
		{
			$request  = new GetRequest($url);
			$response = $this->http->request($request);

			if($response->getCode() == 200)
			{
				if($type == 'xhtml' || in_array($type, Importer::$xmlMediaTypes) || substr($type, -4) == '+xml' || substr($type, -4) == '/xml')
				{
					$reader = new Reader\Dom();
					$dom    = $reader->read($response);
					$node   = $parent->ownerDocument->importNode($dom->documentElement, true);

					$parent->appendChild($node);
				}
				else
				{
					$parent->appendChild(new DOMCdataSection($response->getBody()));
				}
			}
		}
		else
		{
			throw new Exception('Can only fetch http or https sources');
		}
	}
}
