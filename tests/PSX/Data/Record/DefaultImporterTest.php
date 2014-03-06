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

namespace PSX\Data\Record;

use PSX\Data\Record;
use PSX\Data\RecordAbstract;
use PSX\Data\FactoryInterface;
use PSX\Data\BuilderInterface;

/**
 * DefaultImporterTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class DefaultImporterTest extends ImporterTestCase
{
	protected function getImporter()
	{
		return new DefaultImporter();
	}

	protected function getRecord()
	{
		return new News();
	}
}

class News extends RecordAbstract
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
	protected $achievment;
	protected $payment;

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
	 * @param PSX\Data\Record\Person $person
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
	 * @param array<PSX\Data\Record\Tag> $tags
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
	 * @param array<PSX\Data\Record\AchievmentFactory> $achievment
	 */
	public function setAchievment(array $achievment)
	{
		$this->achievment = $achievment;
	}

	public function getAchievment()
	{
		return $this->achievment;
	}

	/**
	 * @param PSX\Data\Record\PaymentBuilder $payment
	 */
	public function setPayment($payment)
	{
		$this->payment = $payment;
	}

	public function getPayment()
	{
		return $this->payment;
	}
}

class Person extends RecordAbstract
{
	protected $title;

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

	public function setTitle($title)
	{
		$this->title = $title;
	}

	public function getTitle()
	{
		return $this->title;
	}
}

class AchievmentFactory implements FactoryInterface
{
	public function factory($data)
	{
		if(isset($data['type']))
		{
			$class = 'PSX\Data\Record\Achievment' . ucfirst($data['type']);

			if(class_exists($class))
			{
				return new $class();
			}
		}

		return null;
	}
}

class AchievmentFoo extends RecordAbstract
{
	protected $type;
	protected $foo;

	public function setType($type)
	{
		$this->type = $type;
	}
	
	public function getType()
	{
		return $this->type;
	}

	public function setFoo($foo)
	{
		$this->foo = $foo;
	}
	
	public function getFoo()
	{
		return $this->foo;
	}
}

class AchievmentBar extends RecordAbstract
{
	protected $type;
	protected $bar;

	public function setType($type)
	{
		$this->type = $type;
	}
	
	public function getType()
	{
		return $this->type;
	}

	public function setBar($bar)
	{
		$this->bar = $bar;
	}
	
	public function getBar()
	{
		return $this->bar;
	}
}

class PaymentBuilder implements BuilderInterface
{
	public function build($data)
	{
		// this is the place to build complex records depending on the content
		// if the default importer fits not your need

		return new Record('payment', array(
			'type'   => 'paypal',
			'custom' => 'foobar'
		));
	}
}

