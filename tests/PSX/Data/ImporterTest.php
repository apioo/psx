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

namespace PSX\Data;

use PSX\Atom;
use PSX\Data\RecordAbstract;
use PSX\Http\Message;
use PSX\Http\Stream\StringStream;

/**
 * ImporterTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ImporterTest extends \PHPUnit_Framework_TestCase
{
	public function testImport()
	{
		$data = <<<TEXT
<entry>
	<title>Atom-Powered Robots Run Amok</title>
	<id>urn:uuid:1225c695-cfb8-4ebb-aaaa-80da344efa6a</id>
	<updated>2003-12-13T18:30:02+00:00</updated>
	<link href="http://example.org/2003/12/13/atom03"/>
	<summary>Some text.</summary>
</entry>
TEXT;

		$message = new Message();
		$message->addHeader('Content-Type', 'application/atom+xml');
		$message->setBody(new StringStream($data));

		$importer = new Importer(getContainer()->get('extractor'), getContainer()->get('importer_manager'));

		$atom  = $importer->import(new Atom(), $message);
		$entry = $atom->current();
		$link  = current($entry->getLink());

		$this->assertEquals('Atom-Powered Robots Run Amok', $entry->getTitle());
		$this->assertEquals('urn:uuid:1225c695-cfb8-4ebb-aaaa-80da344efa6a', $entry->getId());
		$this->assertEquals('http://example.org/2003/12/13/atom03', $link->getHref());
		$this->assertEquals('Some text.', $entry->getSummary()->getContent());
	}

	public function testImportCallback()
	{
		$data = <<<TEXT
{
	"type": "page",
	"title": "foo",
	"content": "lorem ipsum"
}
TEXT;

		$message = new Message();
		$message->addHeader('Content-Type', 'application/json');
		$message->setBody(new StringStream($data));

		$importer = new Importer(getContainer()->get('extractor'), getContainer()->get('importer_manager'));

		$record = $importer->import(function($data){

			// based on the data we can return an different source for the 
			// importer. This is useful if you want create different records
			// based on i.e. an type value
			switch($data['type'])
			{
				case 'page':
					return new Page();
					break;

				default:
					throw new \RuntimeException('Invalid type');
					return;
			}

		}, $message);

		$this->assertInstanceOf('PSX\Data\Page', $record);
		$this->assertEquals('foo', $record->getTitle());
		$this->assertEquals('lorem ipsum', $record->getContent());
	}

	/**
	 * @expectedException PSX\Data\NotFoundException
	 */
	public function testNoImporter()
	{
		$data = <<<TEXT
<entry>
	<title>Atom-Powered Robots Run Amok</title>
	<id>urn:uuid:1225c695-cfb8-4ebb-aaaa-80da344efa6a</id>
	<updated>2003-12-13T18:30:02+00:00</updated>
	<link href="http://example.org/2003/12/13/atom03"/>
	<summary>Some text.</summary>
</entry>
TEXT;

		$message = new Message();
		$message->addHeader('Content-Type', 'application/atom+xml');
		$message->setBody(new StringStream($data));

		$importer = new Importer(getContainer()->get('extractor'), getContainer()->get('importer_manager'));
		$importer->import('foobar', $message);
	}
}

class Page extends RecordAbstract
{
	protected $title;
	protected $content;

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
}
