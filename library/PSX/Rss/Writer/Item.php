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

namespace PSX\Rss\Writer;

use DateTime;
use PSX\Rss\Writer as Rss;
use PSX\Xml\WriterInterface;
use XMLWriter;

/**
 * Item
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
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

	public function output()
	{
		header('Content-Type: ' . self::$mime);

		echo $this->toString();
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
