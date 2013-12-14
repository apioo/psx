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

namespace PSX\Data\Record;

use PSX\Data\Record;
use PSX\Data\RecordAbstract;
use PSX\Data\FactoryInterface;
use PSX\Data\BuilderInterface;
use PSX\Data\Reader;
use PSX\Data\Writer;
use PSX\Exception;
use PSX\Http\Message;

/**
 * DefaultImporterTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class DefaultImporterTest extends \PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
	}

	protected function tearDown()
	{
	}

	public function testImportJson()
	{
		$body = <<<DATA
{
	"id": 1,
	"title": "foobar",
	"active": 1,
	"count": 12,
	"rating": 12.45,
	"person": {
		"title": "Foo"
	},
	"tags": [{
		"title": "bar"
	},{
		"title": "foo"
	},{
		"title": "test"
	}],
	"achievment": {
		"type": "foo"
	},
	"payment": {
		"type": "paypal"
	}
}
DATA;

		// read json
		$news     = new News();
		$reader   = new Reader\Json();
		$importer = new DefaultImporter();
		$importer->import($news, $reader->read(new Message(array(), $body)));

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
		$this->assertInstanceOf('PSX\Data\Record\Person', $news->getPerson());
		$this->assertEquals(true, is_array($news->getTags()));
		$this->assertInstanceOf('PSX\Data\Record\Achievment', $news->getAchievment());

		foreach($news->getTags() as $tag)
		{
			$this->assertInstanceOf('PSX\Data\Record\Tag', $tag);
		}
	}

	public function testExportJson()
	{
		$body = <<<DATA
{
	"id": 1,
	"title": "foobar",
	"active": 1,
	"count": 12,
	"rating": 12.45,
	"person": {
		"title": "Foo"
	},
	"tags": [{
		"title": "bar"
	},{
		"title": "foo"
	},{
		"title": "test"
	}],
	"achievment": {
		"type": "foo"
	},
	"payment": {
		"type": "paypal"
	}
}
DATA;

		// read json
		$news     = new News();
		$reader   = new Reader\Json();
		$importer = new DefaultImporter();
		$importer->import($news, $reader->read(new Message(array(), $body)));

		$writer = new Writer\Json();
		$resp   = $writer->write($news);

		$this->assertJsonStringEqualsJsonString($body, $resp);
	}

	public function testImportXml()
	{
		$body = <<<DATA
<?xml version="1.0" encoding="UTF-8"?>
<news>
	<id>1</id>
	<title>foobar</title>
	<active>1</active>
	<count>12</count>
	<rating>12.45</rating>
	<person>
		<title>Foo</title>
	</person>
	<tags>
		<title>bar</title>
	</tags>
	<tags>
		<title>foo</title>
	</tags>
	<tags>
		<title>test</title>
	</tags>
	<achievment>
		<type>foo</type>
	</achievment>
	<payment>
		<type>paypal</type>
	</payment>
</news>
DATA;

		// read xml
		$news     = new News();
		$reader   = new Reader\Xml();
		$importer = new DefaultImporter();
		$importer->import($news, $reader->read(new Message(array(), $body)));

		$this->assertEquals(1, $news->getId());
		$this->assertEquals('foobar', $news->getTitle());
		$this->assertEquals(true, $news->getActive());
		$this->assertEquals(12, $news->getCount());
		$this->assertEquals(12.45, $news->getRating());
		$this->assertInstanceOf('PSX\Data\Record\Person', $news->getPerson());
		$this->assertEquals(true, is_array($news->getTags()));
		$this->assertInstanceOf('PSX\Data\Record\Achievment', $news->getAchievment());

		foreach($news->getTags() as $tag)
		{
			$this->assertInstanceOf('PSX\Data\Record\Tag', $tag);
		}
	}

	public function testExportXml()
	{
		$body = <<<DATA
<?xml version="1.0" encoding="UTF-8"?>
<news>
	<id>1</id>
	<title>foobar</title>
	<active>true</active>
	<count>12</count>
	<rating>12.45</rating>
	<person>
		<title>Foo</title>
	</person>
	<tags>
		<title>bar</title>
	</tags>
	<tags>
		<title>foo</title>
	</tags>
	<tags>
		<title>test</title>
	</tags>
	<achievment>
		<type>foo</type>
	</achievment>
	<payment>
		<type>paypal</type>
	</payment>
</news>
DATA;

		// read json
		$news     = new News();
		$reader   = new Reader\Xml();
		$importer = new DefaultImporter();
		$importer->import($news, $reader->read(new Message(array(), $body)));

		$writer = new Writer\Xml();
		$resp   = $writer->write($news);

		$this->assertXmlStringEqualsXmlString($body, $resp);
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
	protected $achievment;
	protected $payment;

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
	 * @param PSX\Data\Record\AchievmentFactory $achievment
	 */
	public function setAchievment(Achievment $achievment)
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

interface Achievment
{
}

class AchievmentFoo extends RecordAbstract implements Achievment
{
	protected $type;

	public function setType($type)
	{
		$this->type = $type;
	}
	
	public function getType()
	{
		return $this->type;
	}
}

class PaymentBuilder implements BuilderInterface
{
	public function build($data)
	{
		// this is the place to build complex records depending on the content
		// if the default importer fits not your need

		return new Record('payment', array(
			'type' => 'paypal',
		));
	}
}

