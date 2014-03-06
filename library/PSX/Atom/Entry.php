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

namespace PSX\Atom;

use DateTime;
use PSX\Atom;
use PSX\Data\RecordAbstract;
use PSX\Data\RecordInfo;

/**
 * Entry
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Entry extends RecordAbstract
{
	protected $author = array();
	protected $category = array();
	protected $content;
	protected $contributor = array();
	protected $id;
	protected $link = array();
	protected $published;
	protected $rights;
	protected $source;
	protected $summary;
	protected $title;
	protected $updated;

	public function getRecordInfo()
	{
		return new RecordInfo('entry', array(
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
		));
	}

	/**
	 * @param PSX\Atom\Person $author
	 */
	public function addAuthor(Person $author)
	{
		$this->author[] = $author;
	}

	/**
	 * @param array<PSX\Atom\Person> $author
	 */
	public function setAuthor(array $author)
	{
		$this->author = $author;
	}

	public function getAuthor()
	{
		return $this->author;
	}

	/**
	 * @param PSX\Atom\Category $author
	 */
	public function addCategory(Category $category)
	{
		$this->category[] = $category;
	}

	/**
	 * @param array<PSX\Atom\Category> $category
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
	 * @param PSX\Atom\Text $content
	 */
	public function setContent(Text $content)
	{
		$this->content = $content;
	}
	
	public function getContent()
	{
		return $this->content;
	}

	/**
	 * @param PSX\Atom\Person $contributor
	 */
	public function addContributor(Person $contributor)
	{
		$this->contributor[] = $contributor;
	}

	/**
	 * @param array<PSX\Atom\Person> $contributor
	 */
	public function setContributor($contributor)
	{
		$this->contributor = $contributor;
	}

	public function getContributor()
	{
		return $this->contributor;
	}

	public function setId($id)
	{
		$this->id = $id;
	}
	
	public function getId()
	{
		return $this->id;
	}

	public function setRights($rights)
	{
		$this->rights = $rights;
	}
	
	public function getRights()
	{
		return $this->rights;
	}

	public function setTitle($title)
	{
		$this->title = $title;
	}
	
	public function getTitle()
	{
		return $this->title;
	}

	public function setPublished(DateTime $published)
	{
		$this->published = $published;
	}
	
	public function getPublished()
	{
		return $this->published;
	}

	public function setUpdated(DateTime $updated)
	{
		$this->updated = $updated;
	}
	
	public function getUpdated()
	{
		return $this->updated;
	}

	public function addLink(Link $link)
	{
		$this->link[] = $link;
	}

	public function getLink()
	{
		return $this->link;
	}

	public function setSource(Atom $source)
	{
		$this->source = $source;
	}

	public function getSource()
	{
		return $this->source;
	}

	public function setSummary(Text $summary)
	{
		$this->summary = $summary;
	}
	
	public function getSummary()
	{
		return $this->summary;
	}
}

