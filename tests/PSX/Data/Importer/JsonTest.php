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

namespace PSX\Data\Importer;

use DateTime;
use PSX\Data\RecordAbstract;
use PSX\Http\Message;

/**
 * JsonTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class JsonTest extends \PHPUnit_Framework_TestCase
{
	public function testJson()
	{
		$body = <<<JSON
{
	"id": "1",
	"title": "foo",
	"date": "2014-07-29T23:37:00"
}
JSON;

		$request = new Message(array('Content-Type' => 'application/json'), $body);
		$record  = getContainer()->get('importer')->import(new JsonRecord(), $request);

		$this->assertEquals(1, $record->getId());
		$this->assertEquals('foo', $record->getTitle());
		$this->assertInstanceOf('DateTime', $record->getDate());
		$this->assertEquals('Tue, 29 Jul 2014 23:37:00 +0000', $record->getDate()->format('r'));
	}

	/**
	 * As default we use the json reader so even if we have no content type 
	 * this should work
	 */
	public function testUnknownContentType()
	{
		$body = <<<JSON
{
	"id": "1",
	"title": "foo",
	"date": "2014-07-29T23:37:00"
}
JSON;

		$request = new Message(array('Content-Type' => ''), $body);
		$record  = getContainer()->get('importer')->import(new JsonRecord(), $request);

		$this->assertEquals(1, $record->getId());
		$this->assertEquals('foo', $record->getTitle());
		$this->assertInstanceOf('DateTime', $record->getDate());
		$this->assertEquals('Tue, 29 Jul 2014 23:37:00 +0000', $record->getDate()->format('r'));
	}
}

class JsonRecord extends RecordAbstract
{
	protected $id;
	protected $title;
	protected $date;

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
	 * @param DateTime $date
	 */
	public function setDate(DateTime $date)
	{
		$this->date = $date;
	}
	
	public function getDate()
	{
		return $this->date;
	}
}
