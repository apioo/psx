<?php
/*
 *  $Id: Rss.php 663 2012-10-07 16:45:52Z k42b3.x@googlemail.com $
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

/**
 * PSX_Rss
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Rss
 * @version    $Revision: 663 $
 * @see        http://www.ietf.org/rfc/rfc4287.txt
 */
class PSX_Rss extends PSX_Data_RecordAbstract implements Iterator, Countable
{
	public $title;
	public $link;
	public $description;
	public $language;
	public $copyright;
	public $managingeditor;
	public $webmaster;
	public $generator;
	public $docs;
	public $ttl;
	public $image;
	public $rating;
	public $textinput;
	public $skiphours;
	public $skipdays;
	public $category  = array();
	public $pubdate;
	public $lastbuilddate;
	public $cloud;
	public $item      = array();

	private $dom;
	private $nextItem;

	public function getName()
	{
		return 'channel';
	}

	public function getFields()
	{
		return array(

			'title'          => $this->title,
			'link'           => $this->link,
			'description'    => $this->description,
			'language'       => $this->language,
			'copyright'      => $this->copyright,
			'managingeditor' => $this->managingeditor,
			'webmaster'      => $this->webmaster,
			'generator'      => $this->generator,
			'docs'           => $this->docs,
			'image'          => $this->image,
			'rating'         => $this->rating,
			'textinput'      => $this->textinput,
			'skiphours'      => $this->skiphours,
			'skipdays'       => $this->skipdays,
			'category'       => $this->category,
			'pubDate'        => $this->pubDate,
			'lastbuilddate'  => $this->lastbuilddate,
			'cloud'          => $this->cloud,
			'item'           => $this->item,

		);
	}

	public function getItem()
	{
		return $this->item;
	}

	public function getDom()
	{
		return $this->dom;
	}

	// Iterator
	public function current()
	{
		return current($this->item);
	}

	public function key()
	{
		return key($this->item);
	}

	public function next()
	{
		$this->nextItem = next($this->item);
	}

	public function rewind()
	{
		reset($this->item);
	}

	public function valid()
	{
		return $this->nextItem !== false;
	}

	// Countable
	public function count()
	{
		return count($this->item);
	}

	public function import(PSX_Data_ReaderResult $result)
	{
		$this->dom = $result->getData();

		switch($result->getType())
		{
			case PSX_Data_ReaderInterface::DOM:

				$elementList = $this->dom->getElementsByTagName('rss');

				if($elementList->length == 0)
				{
					throw new PSX_Data_Exception('Could not find rss element');
				}

				/*
				$rss = $elementList->item(0);

				if($rss->getAttribute('version') != '2.0')
				{
					throw new PSX_Data_Exception('Invalid RSS version must be 2.0');
				}
				*/

				$elementList = $this->dom->getElementsByTagName('channel');

				if($elementList->length > 0)
				{
					$this->parseChannelElement($elementList->item(0));
				}
				else
				{
					throw new PSX_Data_Exception('No channel element found');
				}

				break;

			default:

				throw new PSX_Data_Exception('Can only import result of DOM reader');
		}
	}

	private function parseChannelElement(DomElement $channel)
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
				case 'link':
				case 'description':
				case 'language':
				case 'copyright':
				case 'managingeditor':
				case 'webmaster':
				case 'generator':
				case 'docs':
				case 'ttl':
				case 'image':
				case 'rating':
				case 'textinput':
				case 'skiphours':
				case 'skipdays':

					$this->$name = $item->nodeValue;

					break;

				case 'category':

					$this->category[] = self::categoryConstruct($item);

					break;

				case 'pubdate':
				case 'lastbuilddate':

					$this->$name = new DateTime($item->nodeValue);

					break;

				case 'cloud':

					$this->cloud = self::cloudConstruct($item);

					break;

				case 'item':

					$result = new PSX_Data_ReaderResult(PSX_Data_ReaderInterface::DOM, $item);
					$item   = new PSX_Rss_Item();

					$item->import($result);

					$this->item[] = $item;

					break;
			}
		}
	}

	public static function categoryConstruct(DomElement $category)
	{
		return array(

			'text'   => $category->nodeValue,
			'domain' => $category->getAttribute('domain'),

		);
	}

	public static function cloudConstruct(DomElement $cloud)
	{
		return array(

			'domain'            => $cloud->getAttribute('domain'),
			'port'              => $cloud->getAttribute('port'),
			'path'              => $cloud->getAttribute('path'),
			'registerProcedure' => $cloud->getAttribute('registerProcedure'),
			'protocol'          => $cloud->getAttribute('protocol'),

		);
	}

	public static function findTag($content)
	{
		$parse   = new PSX_Html_Parse($content);
		$element = new PSX_Html_Parse_Element('link', array(

			'rel'  => 'alternate',
			'type' => PSX_Data_Writer_Rss::$mime,

		));

		$href = $parse->fetchAttrFromHead($element, 'href');

		return $href;
	}

	public static function request($url)
	{
		$http     = new PSX_Http();
		$request  = new PSX_Http_GetRequest($url, array(
			'User-Agent' => __CLASS__ . ' ' . PSX_Base::VERSION
		));
		$response = $http->request($request);
		$reader   = new PSX_Data_Reader_Dom();

		$rss = new self();
		$rss->import($reader->read($response));

		return $rss;
	}
}
