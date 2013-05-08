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

use DOMDocument;
use DOMElement;
use PSX\Exception;
use PSX\Atom;
use PSX\Data\InvalidDataException;
use PSX\Data\NotSupportedException;
use PSX\Data\ReaderInterface;
use PSX\Data\ReaderResult;
use PSX\Data\RecordAbstract;
use PSX\Http;
use PSX\Http\GetRequest;
use PSX\Url;

/**
 * Entry
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Entry extends RecordAbstract
{
	public $author      = array();
	public $category    = array();
	public $content;
	public $contributor = array();
	public $id;
	public $link        = array();
	public $published;
	public $rights;
	public $source;
	public $summary;
	public $title;
	public $updated;

	private $dom;
	private $element;
	private $fetchRemoteContent = false;
	private $http;
	private $xmlMediaTypes = array(

		'text/xml',
		'application/xml',
		'text/xml-external-parsed-entity',
		'application/xml-external-parsed-entity',
		'application/xml-dtd'

	);

	public function getName()
	{
		return 'entry';
	}

	public function getFields()
	{
		return array(

			'author'      => $this->author,
			'category'    => $this->category,
			'content'     => $this->content,
			'contributor' => $this->contributor,
			'id'          => $this->id,
			'link'        => $this->link,
			'published'   => $this->published,
			'rights'      => $this->rights,
			'source'      => $this->source,
			'summary'     => $this->summary,
			'title'       => $this->title,
			'updated'     => $this->updated,

		);
	}

	public function import(ReaderResult $result)
	{
		switch($result->getType())
		{
			case ReaderInterface::DOM:

				$entry = $result->getData();

				if($entry instanceof DOMDocument)
				{
					$this->dom = $entry;

					$root = $entry->documentElement;
				}
				else if($entry instanceof DOMElement)
				{
					$this->dom = $entry->ownerDocument;

					$root = $entry;
				}
				else
				{
					throw new InvalidDataException('Data must be an instance of DOMDocument or DOMElement');
				}

				if(strcasecmp($root->localName, 'entry') == 0)
				{
					$this->parseEntryElement($root);
				}
				else
				{
					throw new InvalidDataException('No entry element found');
				}

				break;

			default:

				throw new NotSupportedException('Reader is not supported');
		}
	}

	private function parseEntryElement(DOMElement $entry)
	{
		$this->element = $entry;

		$childNodes = $entry->childNodes;

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
				case 'author':
				case 'contributor':

					array_push($this->$name, Atom::personConstruct($item));

					break;

				case 'category':

					$this->category[] = Atom::categoryConstruct($item);

					break;

				case 'content':

					$content = null;
					$type    = strtolower($item->getAttribute('type'));
					$src     = $item->getAttribute('src');

					if($this->fetchRemoteContent && !empty($src))
					{
						$this->fetchRemoteContent($item, new Url($src));
					}

					if(empty($type) || $type == 'text' || $type == 'html' || substr($type, 0, 5) == 'text/')
					{
						$content = $item->nodeValue;
					}
					else if($type == 'xhtml' || in_array($type, $this->xmlMediaTypes) || substr($type, -4) == '+xml' || substr($type, -4) == '/xml')
					{
						$child = $this->getFirstChild($item);

						if($child !== false)
						{
							$content = $this->dom->saveXML($child);
						}
					}
					else
					{
						$content = base64_decode($item->nodeValue);
					}

					$this->content = $content;

					break;

				case 'id':
				case 'rights':
				case 'title':

					$this->$name = $item->nodeValue;

					break;

				case 'published':
				case 'updated':

					$this->$name = Atom::dateConstruct($item);

					break;

				case 'link':

					array_push($this->$name, Atom::linkConstruct($item));

					break;

				case 'source':

					$dom = new DOMDocument();

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

					$result = new ReaderResult(ReaderInterface::DOM, $dom);

					$atom = new Atom();
					$atom->import($result);

					$this->source = $atom;

					break;

				case 'summary':

					$this->summary = Atom::textConstruct($item);

					break;
			}
		}
	}

	public function getDom()
	{
		return $this->dom;
	}

	public function getElement()
	{
		return $this->element;
	}

	public function setFetchRemoteContent($remoteContent)
	{
		$this->fetchRemoteContent = (boolean) $remoteContent;

		if($this->fetchRemoteContent)
		{
			$this->http = new Http();
		}
	}

	public function fetchRemoteContent(DOMElement $parent, Url $url)
	{
		if($url->getScheme() == 'http' || $url->getScheme() == 'https')
		{
			$request  = new GetRequest($url);
			$response = $this->http->request($request);

			if($response->getCode() == 200)
			{
				$content = $this->dom->createDocumentFragment();
				$content->appendXML($response->getBody());

				$parent->append($content);
			}
		}
		else
		{
			throw new Exception('Can only fetch http or https sources');
		}
	}

	private function getFirstChild(DOMElement $element)
	{
		foreach($element->childNodes as $child)
		{
			if($child->nodeType == XML_ELEMENT_NODE)
			{
				return $child;
			}
		}

		return false;
	}
}

