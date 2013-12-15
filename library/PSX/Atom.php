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

namespace PSX;

use DOMElement;
use PSX\Atom\Category;
use PSX\Atom\Entry;
use PSX\Atom\Generator;
use PSX\Atom\Link;
use PSX\Atom\Person;
use PSX\Atom\Text;
use PSX\Data\CollectionAbstract;
use PSX\Data\RecordAbstract;

/**
 * This record represents an atom feed. It is possible to import an existing 
 * feed into the record through the AtomImporter. In the same way you can 
 * produce a feed with the AtomExporter.
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 * @see     http://www.ietf.org/rfc/rfc4287.txt
 */
class Atom extends CollectionAbstract
{
	public static $xmlns = 'http://www.w3.org/2005/Atom';

	protected $author = array();
	protected $category = array();
	protected $contributor = array();
	protected $generator;
	protected $icon;
	protected $logo;
	protected $id;
	protected $link = array();
	protected $rights;
	protected $subTitle;
	protected $title;
	protected $updated;

	public function getRecordInfo()
	{
		return new RecordInfo('feed', array(
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
			'entry'       => $this->collection,
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
	 * @param PSX\Atom\Category $category
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
	 * @param PSX\Atom\Person $contributor
	 */
	public function addContributor(Person $contributor)
	{
		$this->contributor[] = $contributor;
	}

	/**
	 * @param array<PSX\Atom\Person> $contributor
	 */
	public function setContributor(array $contributor)
	{
		$this->contributor = $contributor;
	}

	public function getContributor()
	{
		return $this->contributor;
	}

	/**
	 * @param PSX\Atom\Generator $generator
	 */
	public function setGenerator(Generator $generator)
	{
		$this->generator = $generator;
	}
	
	public function getGenerator()
	{
		return $this->generator;
	}

	public function setIcon($icon)
	{
		$this->icon = $icon;
	}
	
	public function getIcon()
	{
		return $this->icon;
	}

	public function setLogo($logo)
	{
		$this->logo = $logo;
	}
	
	public function getLogo()
	{
		return $this->logo;
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

	/**
	 * @param DateTime $updated
	 */
	public function setUpdated(\DateTime $updated)
	{
		$this->updated = $updated;
	}
	
	public function getUpdated()
	{
		return $this->updated;
	}

	/**
	 * @param PSX\Atom\Link $link
	 */
	public function addLink(Link $link)
	{
		$this->link[] = $link;
	}

	/**
	 * @param array<PSX\Atom\Link> $link
	 */
	public function setLink(array $link)
	{
		$this->link = $link;
	}

	public function getLink()
	{
		return $this->link;
	}

	/**
	 * @param PSX\Atom\Text $subTitle
	 */
	public function setSubTitle(Text $subTitle)
	{
		$this->subTitle = $subTitle;
	}
	
	public function getSubTitle()
	{
		return $this->subTitle;
	}
}
