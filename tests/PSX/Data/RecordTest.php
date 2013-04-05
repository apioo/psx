<?php
/*
 *  $Id: ResultSetTest.php 480 2012-05-01 18:13:54Z k42b3.x@googlemail.com $
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

namespace PSX\Data;

use PSX\Exception;
use PSX\Data\Reader\Json;
use PSX\Http\Message;

/**
 * PSX_Data_ResultSetTest
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   tests
 * @version    $Revision: 480 $
 */
class RecordTest extends \PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
	}

	protected function tearDown()
	{
	}

	public function testImport()
	{
		$body = <<<DATA
{
	"id": 1,
	"title": "foobar",
	"active": 1,
	"count": 12,
	"rating": 12.45,
	"person": {
		"name": "Foo"
	},
	"tags": [{
		"name": "bar"
	},{
		"name": "foo"
	},{
		"name": "test"
	}]
}
DATA;

		// read json
		$reader  = new Json();
		$message = new Message(array(), $body);

		$result = $reader->read($message);

		// create news object
		$news = new News();
		$news->import($result);

		$this->assertEquals(1, $news->getId());
		$this->assertInternalType('integer', $news->getId());
		$this->assertEquals('foobar', $news->getTitle());
		$this->assertInternalType('string', $news->getTitle());
		$this->assertEquals(true, $news->getActive());
		$this->assertInternalType('boolean', $news->getActive());
		$this->assertEquals(12, $news->getCount());
		$this->assertInternalType('integer', $news->getCount());
		$this->assertEquals(12.45, $news->getRating());
		$this->assertInternalType('float', $news->getRating());
		$this->assertInstanceOf('PSX\Data\Person', $news->getPerson());
		$this->assertEquals(true, is_array($news->getTags()));

		foreach($news->getTags() as $tag)
		{
			$this->assertInstanceOf('PSX\Data\Tag', $tag);
		}
	}
}

class News extends RecordAbstract
{
	protected $id;
	protected $title;
	protected $active;
	protected $count;
	protected $rating;
	protected $person;
	protected $tags;

	public function getName()
	{
		return 'news';
	}

	public function getFields()
	{
		return array(
			'id'     => $this->id,
			'title'  => $this->title,
			'active' => $this->active,
			'count'  => $this->count,
			'rating' => $this->rating,
			'person' => $this->person,
			'tags'   => $this->tags,
		);
	}

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
	 * @param PSX\Data\Person $person
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
	 * @param array<PSX\Data\Tag> $tags
	 */
	public function setTags(array $tags)
	{
		$this->tags = $tags;
	}

	public function getTags()
	{
		return $this->tags;
	}
}

class Person extends RecordAbstract
{
	protected $title;

	public function getName()
	{
		return 'person';
	}

	public function getFields()
	{
		return array(
			'title' => $this->title,
		);
	}

	public function setTitle($title)
	{
		$this->title = $title;
	}

	public function getTitle()
	{
		return $this->title;
	}
}

class Tag extends RecordAbstract
{
	protected $title;

	public function getName()
	{
		return 'tag';
	}

	public function getFields()
	{
		return array(
			'title' => $this->title,
		);
	}

	public function setTitle($title)
	{
		$this->title = $title;
	}

	public function getTitle()
	{
		return $this->title;
	}
}

