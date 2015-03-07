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

namespace PSX\Rss;

use DateTime;
use PSX\Xml\WriterInterface;
use XMLWriter;

/**
 * Writer
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Writer implements WriterInterface
{
	public static $mime = 'application/rss+xml';

	protected $writer;

	public function __construct($title, $link, $description, XMLWriter $writer = null)
	{
		$this->writer = $writer === null ? new XMLWriter() : $writer;

		if($writer === null)
		{
			$this->writer->openMemory();
			$this->writer->setIndent(true);
			$this->writer->startDocument('1.0', 'UTF-8');
		}

		$this->writer->startElement('rss');
		$this->writer->writeAttribute('version', '2.0');
		$this->writer->startElement('channel');

		$this->setTitle($title);
		$this->setLink($link);
		$this->setDescription($description);
	}

	public function setTitle($title)
	{
		$this->writer->writeElement('title', $title);
	}

	public function setLink($link)
	{
		$this->writer->writeElement('link', $link);
	}

	public function setDescription($description)
	{
		$this->writer->startElement('description');
		$this->writer->text($description);
		$this->writer->endElement();
	}

	public function setLanguage($language)
	{
		$this->writer->writeElement('language', $language);
	}

	public function setCopyright($copyright)
	{
		$this->writer->writeElement('copyright', $copyright);
	}

	public function setManagingEditor($managingEditor)
	{
		$this->writer->writeElement('managingEditor', $managingEditor);
	}

	public function setWebMaster($webMaster)
	{
		$this->writer->writeElement('webMaster', $webMaster);
	}

	public function setPubDate(DateTime $pubDate)
	{
		$this->writer->writeElement('pubDate', $pubDate->format(DateTime::RSS));
	}

	public function setLastBuildDate(DateTime $lastBuildDate)
	{
		$this->writer->writeElement('lastBuildDate', $lastBuildDate->format(DateTime::RSS));
	}

	public function addCategory($category, $domain = false)
	{
		self::categoryConstruct($this->writer, $category, $domain);
	}

	public function setGenerator($generator)
	{
		$this->writer->writeElement('generator', $generator);
	}

	public function setDocs($docs = 'http://www.rssboard.org/rss-specification')
	{
		$this->writer->writeElement('docs', $docs);
	}

	public function setCloud($domain, $port, $path, $registerProcedure, $protocol)
	{
		$this->writer->startElement('cloud');
		$this->writer->writeAttribute('domain', $domain);
		$this->writer->writeAttribute('port', $port);
		$this->writer->writeAttribute('path', $path);
		$this->writer->writeAttribute('registerProcedure', $registerProcedure);
		$this->writer->writeAttribute('protocol', $protocol);
		$this->writer->endElement();
	}

	public function setTtl($ttl)
	{
		$this->writer->writeElement('ttl', intval($ttl));
	}

	public function setImage($url, $title, $link, $width = false, $height = false)
	{
		$this->writer->startElement('image');
		$this->writer->writeElement('url', $url);
		$this->writer->writeElement('title', $title);
		$this->writer->writeElement('link', $link);

		if($width !== false && $height !== false)
		{
			$width  = intval($width);
			$height = intval($height);

			$width  = $width  <= 144 && $width  >= 0 ? $width  : 88;
			$height = $height <= 400 && $height >= 0 ? $height : 31;

			$this->writer->writeElement('width', $width);
			$this->writer->writeElement('height', $height);
		}

		$this->writer->endElement();
	}

	/**
	 * @see http://www.w3.org/PICS/
	 */
	public function setRating($rating)
	{
		$this->writer->writeElement('rating', $rating);
	}

	public function setTextInput($title, $description, $name, $link)
	{
		$this->writer->startElement('textInput');
		$this->writer->writeElement('title', $title);
		$this->writer->writeElement('description', $description);
		$this->writer->writeElement('name', $name);
		$this->writer->writeElement('link', $link);
		$this->writer->endElement();
	}

	public function createItem()
	{
		return new Writer\Item($this->writer);
	}

	public function close()
	{
		$this->writer->endElement();
		$this->writer->endElement();
	}

	public function toString()
	{
		$this->close();
		$this->writer->endDocument();

		return $this->writer->outputMemory();		
	}

	public function getWriter()
	{
		return $this->writer;
	}

	public static function categoryConstruct(XMLWriter $writer, $category, $domain = false)
	{
		$writer->startElement('category');

		if(!empty($domain))
		{
			$writer->writeAttribute('domain', $domain);
		}

		$writer->text($category);
		$writer->endElement();
	}

	public static function link($title, $href)
	{
		return '<link rel="alternate" type="' . self::$mime . '" title="' . htmlspecialchars($title) . '" href="' . htmlspecialchars($href) . '" />';
	}
}
