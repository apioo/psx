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
use PSX\Data\RecordAbstract;
use PSX\Data\RecordInfo;
use PSX\Rss;

/**
 * Item
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Item extends RecordAbstract
{
	protected $title;
	protected $link;
	protected $description;
	protected $author;
	protected $category;
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
	 * @param \PSX\Rss\Category $category
	 */
	public function addCategory(Category $category)
	{
		if($this->category === null)
		{
			$this->category = array();
		}

		$this->category[] = $category;
	}

	/**
	 * @param \PSX\Rss\Category[] $category
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
	 * @param \PSX\Rss\Enclosure $enclosure
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

