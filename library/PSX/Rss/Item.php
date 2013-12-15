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
use PSX\Data\InvalidDataException;
use PSX\Data\NotSupportedException;
use PSX\Data\ReaderInterface;
use PSX\Data\RecordAbstract;
use PSX\Rss;

/**
 * Item
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Item extends RecordAbstract
{
	protected $title;
	protected $link;
	protected $description;
	protected $author;
	protected $category = array();
	protected $comments;
	protected $enclosure;
	protected $guid;
	protected $pubDate;
	protected $source;

	public function getRecordInfo()
	{
		return new RecordInfo('item', array(
			'title'       => $this->title,
			'link'        => $this->link,
			'description' => $this->description,
			'author'      => $this->author,
			'category'    => $this->category,
			'comments'    => $this->comments,
			'enclosure'   => $this->enclosure,
			'guid'        => $this->guid,
			'pubDate'     => $this->pubDate,
			'source'      => $this->source,
		));
	}

	public function setTitle($title)
	{
		$this->title = $title;
	}
	
	public function getTitle()
	{
		return $this->title;
	}

	public function setLink($link)
	{
		$this->link = $link;
	}
	
	public function getLink()
	{
		return $this->link;
	}

	public function setDescription($description)
	{
		$this->description = $description;
	}
	
	public function getDescription()
	{
		return $this->description;
	}

	public function setAuthor($author)
	{
		$this->author = $author;
	}
	
	public function getAuthor()
	{
		return $this->author;
	}

	/**
	 * @param PSX\Rss\Category $category
	 */
	public function addCategory(Category $category)
	{
		$this->category[] = $category;
	}

	/**
	 * @param array<PSX\Rss\Category> $category
	 */
	public function setCategory(array $category)
	{
		$this->category = $category;
	}
	
	public function getCategory()
	{
		return $this->category;
	}

	public function setComments($comments)
	{
		$this->comments = $comments;
	}
	
	public function getComments()
	{
		return $this->comments;
	}

	/**
	 * @param PSX\Rss\Enclosure $enclosure
	 */
	public function setEnclosure(Enclosure $enclosure)
	{
		$this->enclosure = $enclosure;
	}
	
	public function getEnclosure()
	{
		return $this->enclosure;
	}

	public function setGuid($guid)
	{
		$this->guid = $guid;
	}
	
	public function getGuid()
	{
		return $this->guid;
	}

	/**
	 * @param DateTime $pubDate
	 */
	public function setPubDate(DateTime $pubDate)
	{
		$this->pubDate = $pubDate;
	}
	
	public function getPubDate()
	{
		return $this->pubDate;
	}

	public function setSource($source)
	{
		$this->source = $source;
	}
	
	public function getSource()
	{
		return $this->source;
	}
}

