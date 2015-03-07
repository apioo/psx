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

namespace PSX\Data\Record\Importer\Test;

use PSX\Data\RecordAbstract;
use PSX\Url;

/**
 * Record
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Record extends RecordAbstract
{
	protected $id;
	protected $title;
	protected $active;
	protected $disabled;
	protected $count;
	protected $rating;
	protected $date;
	protected $person;
	protected $tags;
	protected $entry;
	protected $token;
	protected $url;

	/**
	 * @param integer $id
	 */
	public function setId($id)
	{
		$this->id = $id;
	}

	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param string $title
	 */
	public function setTitle($title)
	{
		$this->title = $title;
	}

	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * @param boolean $active
	 */
	public function setActive($active)
	{
		$this->active = $active;
	}

	public function getActive()
	{
		return $this->active;
	}

	/**
	 * @param boolean $disabled
	 */
	public function setDisabled($disabled)
	{
		$this->disabled = $disabled;
	}

	public function getDisabled()
	{
		return $this->disabled;
	}

	/**
	 * @param integer $count
	 */
	public function setCount($count)
	{
		$this->count = $count;
	}

	public function getCount()
	{
		return $this->count;
	}

	/**
	 * @param float $rating
	 */
	public function setRating($rating)
	{
		$this->rating = $rating;
	}

	public function getRating()
	{
		return $this->rating;
	}

	/**
	 * @param DateTime $date
	 */
	public function setDate($date)
	{
		$this->date = $date;
	}

	public function getDate()
	{
		return $this->date;
	}

	/**
	 * @param PSX\Data\Record\Importer\Test\Person $person
	 */
	public function setPerson(Person $person)
	{
		$this->person = $person;
	}

	public function getPerson()
	{
		return $this->person;
	}

	/**
	 * @param array<string> $tags
	 */
	public function setTags(array $tags)
	{
		$this->tags = $tags;
	}

	public function getTags()
	{
		return $this->tags;
	}

	/**
	 * @param array<PSX\Data\Record\Importer\Test\Entry> $entry
	 */
	public function setEntry(array $entry)
	{
		$this->entry = $entry;
	}

	public function getEntry()
	{
		return $this->entry;
	}

	/**
	 * @param PSX\Data\Record\Importer\Test\Factory $token
	 */
	public function setToken(\stdClass $token)
	{
		$this->token = $token;
	}
	
	public function getToken()
	{
		return $this->token;
	}

	/**
	 * @param PSX\Url $url
	 */
	public function setUrl(Url $url)
	{
		$this->url = $url;
	}
	
	public function getUrl()
	{
		return $this->url;
	}
}

