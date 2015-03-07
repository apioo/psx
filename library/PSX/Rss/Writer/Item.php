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

namespace PSX\Rss\Writer;

use DateTime;
use PSX\Rss\Writer as Rss;
use PSX\Xml\WriterInterface;
use XMLWriter;

/**
 * Item
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Item implements WriterInterface
{
	public static $mime = 'application/rss+xml';

	protected $writer;

	public function __construct(XMLWriter $writer = null)
	{
		$this->writer = $writer === null ? new XMLWriter() : $writer;

		if($writer === null)
		{
			$this->writer->openMemory();
			$this->writer->setIndent(true);
			$this->writer->startDocument('1.0', 'UTF-8');
		}

		$this->writer->startElement('item');
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

	public function setAuthor($author)
	{
		$this->writer->writeElement('author', $author);
	}

	public function addCategory($category, $domain = false)
	{
		Rss::categoryConstruct($this->writer, $category, $domain);
	}

	public function setComments($comment)
	{
		$this->writer->writeElement('comments', $comment);
	}

	public function setEnclosure($url, $length, $type)
	{
		$this->writer->startElement('enclosure');
		$this->writer->writeAttribute('url', $url);
		$this->writer->writeAttribute('length', intval($length));
		$this->writer->writeAttribute('type', $type);
		$this->writer->endElement();
	}

	public function setGuid($guid, $isPermaLink = false)
	{
		$this->writer->startElement('guid');

		if(!empty($isPermaLink))
		{
			$this->writer->writeAttribute('isPermaLink', $isPermaLink);
		}

		$this->writer->text($guid);
		$this->writer->endElement();
	}

	public function setPubDate(DateTime $pubDate)
	{
		$this->writer->writeElement('pubDate', $pubDate->format(DateTime::RSS));
	}

	public function setSource($url, $name)
	{
		$this->writer->startElement('source');
		$this->writer->writeAttribute('url', $url);
		$this->writer->text($name);
		$this->writer->endElement();
	}

	public function close()
	{
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
}
