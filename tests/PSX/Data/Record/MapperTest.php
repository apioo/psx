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

namespace PSX\Data\Record;

use DateTime;
use PSX\Data\RecordAbstract;
use PSX\Data\Record\Mapper\Rule;
use PSX\Data\WriterTestRecord;

/**
 * MapperTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class MapperTest extends \PHPUnit_Framework_TestCase
{
	public function testMap()
	{
		$source = new Source();
		$source->setId(1);
		// userId is not available in the source
		$source->setUserId(12);
		// underscore names are converted to camelcase
		$source->setRight_level('bar');
		$source->setTitle('foo');
		$source->setContent('bar');
		$source->setRating('a-rating');
		$source->setDate('2014-09-06');

		$destination = new Destination();
		$testCase    = $this;

		$mapper = new Mapper();
		$mapper->setRule(array(
			'title'   => 'description',
			'content' => new Rule('content'),
			'rating'  => new Rule('level', 'no-rating'),
			'date'    => new Rule('date', function($value) use ($testCase){
				$testCase->assertEquals('2014-09-06', $value);

				return strtotime($value);
			}),
		));

		$mapper->map($source, $destination);

		$this->assertEquals($source->getId(), $destination->getId());
		$this->assertEquals($source->getRight_level(), $destination->getRightLevel());
		$this->assertEquals($source->getTitle(), $destination->getDescription());
		$this->assertEquals($source->getContent(), $destination->getContent());
		$this->assertEquals('no-rating', $destination->getLevel());
		$this->assertEquals('1409961600', $destination->getDate());
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testMapInvalidDestination()
	{
		$mapper = new Mapper();
		$mapper->map(new Source(), 'foo');
	}
}

class Source extends RecordAbstract
{
	protected $id;
	protected $userId;
	protected $right_level;
	protected $title;
	protected $content;
	protected $rating;
	protected $date;

	public function setId($id)
	{
		$this->id = $id;
	}
	
	public function getId()
	{
		return $this->id;
	}

	public function setUserId($userId)
	{
		$this->userId = $userId;
	}
	
	public function getUserId()
	{
		return $this->userId;
	}

	public function setRight_level($right_level)
	{
		$this->right_level = $right_level;
	}
	
	public function getRight_level()
	{
		return $this->right_level;
	}

	public function setTitle($title)
	{
		$this->title = $title;
	}
	
	public function getTitle()
	{
		return $this->title;
	}

	public function setContent($content)
	{
		$this->content = $content;
	}
	
	public function getContent()
	{
		return $this->content;
	}

	public function setRating($rating)
	{
		$this->rating = $rating;
	}
	
	public function getRating()
	{
		return $this->rating;
	}

	public function setDate($date)
	{
		$this->date = $date;
	}
	
	public function getDate()
	{
		return $this->date;
	}
}

class Destination
{
	protected $id;
	protected $rightLevel;
	protected $description;
	protected $content;
	protected $level;
	protected $date;

	public function setId($id)
	{
		$this->id = $id;
	}
	
	public function getId()
	{
		return $this->id;
	}

	public function setRightLevel($rightLevel)
	{
		$this->rightLevel = $rightLevel;
	}
	
	public function getRightLevel()
	{
		return $this->rightLevel;
	}

	public function setDescription($description)
	{
		$this->description = $description;
	}
	
	public function getDescription()
	{
		return $this->description;
	}

	public function setContent($content)
	{
		$this->content = $content;
	}
	
	public function getContent()
	{
		return $this->content;
	}

	public function setLevel($level)
	{
		$this->level = $level;
	}
	
	public function getLevel()
	{
		return $this->level;
	}

	public function setDate($date)
	{
		$this->date = $date;
	}
	
	public function getDate()
	{
		return $this->date;
	}
}
