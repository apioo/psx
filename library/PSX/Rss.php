<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
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

use PSX\Data\CollectionAbstract;
use PSX\Data\RecordInfo;
use PSX\Rss\Item;
use PSX\Rss\Category;
use PSX\Rss\Cloud;

/**
 * Rss
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 * @see     http://cyber.law.harvard.edu/rss/rss.html
 */
class Rss extends CollectionAbstract
{
	protected $title;
	protected $link;
	protected $description;
	protected $language;
	protected $copyright;
	protected $managingEditor;
	protected $webMaster;
	protected $generator;
	protected $docs;
	protected $ttl;
	protected $image;
	protected $rating;
	protected $skipHours;
	protected $skipDays;
	protected $category;
	protected $pubDate;
	protected $lastBuildDate;
	protected $cloud;

	public function getRecordInfo()
	{
		return new RecordInfo('channel', array(
			'title'          => $this->title,
			'link'           => $this->link,
			'description'    => $this->description,
			'language'       => $this->language,
			'copyright'      => $this->copyright,
			'managingEditor' => $this->managingEditor,
			'webMaster'      => $this->webMaster,
			'generator'      => $this->generator,
			'docs'           => $this->docs,
			'ttl'            => $this->ttl,
			'image'          => $this->image,
			'rating'         => $this->rating,
			'skipHours'      => $this->skipHours,
			'skipDays'       => $this->skipDays,
			'category'       => $this->category,
			'pubDate'        => $this->pubDate,
			'lastBuildDate'  => $this->lastBuildDate,
			'cloud'          => $this->cloud,
			'item'           => $this->collection,
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

	public function setLanguage($language)
	{
		$this->language = $language;
	}
	
	public function getLanguage()
	{
		return $this->language;
	}

	public function setCopyright($copyright)
	{
		$this->copyright = $copyright;
	}
	
	public function getCopyright()
	{
		return $this->copyright;
	}

	public function setManagingEditor($managingEditor)
	{
		$this->managingEditor = $managingEditor;
	}
	
	public function getManagingEditor()
	{
		return $this->managingEditor;
	}

	public function setWebMaster($webMaster)
	{
		$this->webMaster = $webMaster;
	}
	
	public function getWebMaster()
	{
		return $this->webMaster;
	}

	public function setGenerator($generator)
	{
		$this->generator = $generator;
	}
	
	public function getGenerator()
	{
		return $this->generator;
	}

	public function setDocs($docs)
	{
		$this->docs = $docs;
	}
	
	public function getDocs()
	{
		return $this->docs;
	}

	public function setTtl($ttl)
	{
		$this->ttl = $ttl;
	}

	public function getTtl()
	{
		return $this->ttl;
	}

	public function setImage($image)
	{
		$this->image = $image;
	}
	
	public function getImage()
	{
		return $this->image;
	}

	public function setRating($rating)
	{
		$this->rating = $rating;
	}
	
	public function getRating()
	{
		return $this->rating;
	}

	public function setSkipHours($skipHours)
	{
		$this->skipHours = $skipHours;
	}

	public function getSkipHours()
	{
		return $this->skipHours;
	}

	public function setSkipDays($skipDays)
	{
		$this->skipDays = $skipDays;
	}
	
	public function getSkipDays()
	{
		return $this->skipDays;
	}

	/**
	 * @param PSX\Rss\Category $category
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

	/**
	 * @param DateTime $pubDate
	 */
	public function setPubDate(\DateTime $pubDate)
	{
		$this->pubDate = $pubDate;
	}
	
	public function getPubDate()
	{
		return $this->pubDate;
	}

	/**
	 * @param DateTime $lastBuildDate
	 */
	public function setLastBuildDate(\DateTime $lastBuildDate)
	{
		$this->lastBuildDate = $lastBuildDate;
	}
	
	public function getLastBuildDate()
	{
		return $this->lastBuildDate;
	}

	/**
	 * @param PSX\Rss\Cloud $cloud
	 */
	public function setCloud(Cloud $cloud)
	{
		$this->cloud = $cloud;
	}
	
	public function getCloud()
	{
		return $this->cloud;
	}

	/**
	 * @param array<PSX\Rss\Item>
	 */
	public function setItem($item)
	{
		$this->collection = $item;
	}
	
	public function getItem()
	{
		return $this->collection;
	}
}
