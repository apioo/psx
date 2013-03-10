<?php
/*
 *  $Id: Atom.php 663 2012-10-07 16:45:52Z k42b3.x@googlemail.com $
 *
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2012 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX;

use Countable;
use DOMElement;
use Iterator;
use PSX\Atom\Entry;
use PSX\Data\InvalidDataException;
use PSX\Data\NotSupportedException;
use PSX\Data\ReaderInterface;
use PSX\Data\ReaderResult;
use PSX\Data\Reader\Dom;
use PSX\Data\RecordAbstract;
use PSX\Data\Writer;
use PSX\Html\Parse;
use PSX\Html\Parse\Element;
use PSX\Http\GetRequest;

/**
 * This class is for converting an raw ATOM XML feed into an readable object.
 * Where you can access all values without parsing the XML. You can import an
 * existing feed with the PSX_Data_Reader_Dom. Here an example howto read an
 * external atom feed.
 * <code>
 * $atom = PSX_Atom::request('http://test.phpsx.org/index.php/atom');
 *
 * echo $atom->title:
 * echo $atom->updated;
 *
 * foreach($atom as $entry)
 * {
 * 	echo $entry->title . "\n";
 * }
 * </code>
 *
 * If your module extends the PSX_Module_ApiAbstract class you can parse an atom
 * feed wich is send to your via an HTTP request with the getRequest() method
 * <code>
 * $atom = new PSX_Atom();
 * $atom->import($this->getRequest(PSX_Data_ReaderInterface::DOM));
 *
 * echo $atom->title:
 * echo $atom->updated;
 * </code>
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Atom
 * @version    $Revision: 663 $
 * @see        http://www.ietf.org/rfc/rfc4287.txt
 */
class Atom extends RecordAbstract implements Iterator, Countable
{
	public static $xmlns = 'http://www.w3.org/2005/Atom';

	public $author      = array();
	public $category    = array();
	public $contributor = array();
	public $generator;
	public $icon;
	public $logo;
	public $id;
	public $link        = array();
	public $rights;
	public $subtitle;
	public $title;
	public $updated;
	public $entry       = array();

	private $dom;
	private $nextEntry;

	public function getName()
	{
		return 'feed';
	}

	public function getFields()
	{
		return array(

			'author'      => $this->author,
			'category'    => $this->category,
			'contributor' => $this->contributor,
			'generator'   => $this->generator,
			'icon'        => $this->icon,
			'logo'        => $this->logo,
			'id'          => $this->id,
			'link'        => $this->link,
			'rights'      => $this->rights,
			'subtitle'    => $this->subtitle,
			'title'       => $this->title,
			'updated'     => $this->updated,
			'entry'       => $this->entry,

		);
	}

	public function getEntry()
	{
		return $this->entry;
	}

	public function getDom()
	{
		return $this->dom;
	}

	// Iterator
	public function current()
	{
		return current($this->entry);
	}

	public function key()
	{
		return key($this->entry);
	}

	public function next()
	{
		$this->nextEntry = next($this->entry);
	}

	public function rewind()
	{
		reset($this->entry);
	}

	public function valid()
	{
		return $this->nextEntry !== false;
	}

	// Countable
	public function count()
	{
		return count($this->entry);
	}

	public function import(ReaderResult $result)
	{
		$this->dom = $result->getData();

		switch($result->getType())
		{
			case ReaderInterface::DOM:

				$elementList = $this->dom->getElementsByTagNameNS(self::$xmlns, 'feed');

				if($elementList->length > 0)
				{
					$this->parseFeedElement($elementList->item(0));
				}
				else
				{
					throw new InvalidDataException('No feed element found');
				}

				break;

			default:

				throw new NotSupportedException('Can only import result of DOM reader');
		}
	}

	private function parseFeedElement(DOMElement $feed)
	{
		$childNodes = $feed->childNodes;

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

					array_push($this->$name, self::personConstruct($item));

					break;

				case 'category':

					$this->category[] = self::categoryConstruct($item);

					break;

				case 'generator':
				case 'icon':
				case 'logo':
				case 'id':
				case 'rights':
				case 'title':

					$this->$name = $item->nodeValue;

					break;

				case 'updated':

					$this->updated = self::dateConstruct($item);

					break;

				case 'link':

					$this->link[] = self::linkConstruct($item);

					break;

				case 'subtitle':

					$this->subtitle = self::textConstruct($item);

					break;

				case 'entry':

					$result = new ReaderResult(ReaderInterface::DOM, $item);
					$entry  = new Entry();

					$entry->import($result);

					$this->entry[] = $entry;

					break;
			}
		}
	}

	public static function textConstruct(DOMElement $text)
	{
		$content = null;
		$type    = $text->getAttribute('type');

		switch($type)
		{
			case 'xhtml':

				$content = '';

				break;

			default:
			case 'html':
			case 'text':

				$content = trim($text->nodeValue);
		}

		return $content;
	}

	public static function personConstruct(DOMElement $person)
	{
		$data   = $person->childNodes;
		$result = array();

		for($i = 0; $i < $data->length; $i++)
		{
			$item = $data->item($i);

			if($item->nodeType != XML_ELEMENT_NODE)
			{
				continue;
			}


			$name = $item->nodeName;

			switch($name)
			{
				case 'name':
				case 'uri':
				case 'email':

					$result[$name] = $item->nodeValue;

					break;
			}
		}

		return $result;
	}

	public static function dateConstruct(DOMElement $date)
	{
		if($date->nodeType == XML_ELEMENT_NODE)
		{
			return new DateTime($date->nodeValue);
		}
	}

	public static function categoryConstruct(DOMElement $category)
	{
		return array(

			'term'   => $category->getAttribute('term'),
			'scheme' => $category->getAttribute('scheme'),
			'label'  => $category->getAttribute('label'),

		);
	}

	public static function linkConstruct(DOMElement $link)
	{
		return array(

			'href'     => $link->getAttribute('href'),
			'rel'      => $link->getAttribute('rel'),
			'type'     => $link->getAttribute('type'),
			'hreflang' => $link->getAttribute('hreflang'),
			'title'    => $link->getAttribute('title'),
			'length'   => $link->getAttribute('length'),

		);
	}

	public static function findTag($content)
	{
		$parse   = new Parse($content);
		$element = new Element('link', array(

			'rel'  => 'alternate',
			'type' => Writer\Atom::$mime,

		));

		$href = $parse->fetchAttrFromHead($element, 'href');

		return $href;
	}

	public static function request($url)
	{
		$http     = new Http();
		$request  = new GetRequest($url, array(
			'User-Agent' => __CLASS__ . ' ' . Base::VERSION
		));
		$response = $http->request($request);
		$reader   = new Dom();

		$atom = new self();
		$atom->import($reader->read($response));

		return $atom;
	}
}
