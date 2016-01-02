<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2016 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Data;

use PSX\Atom;
use PSX\Http\Message;
use PSX\Http\Stream\StringStream;
use PSX\Test\Environment;

/**
 * ImporterTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
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

        $importer = new Importer(Environment::getService('extractor'), Environment::getService('importer_manager'));

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

        $importer = new Importer(Environment::getService('extractor'), Environment::getService('importer_manager'));

        $record = $importer->import(function ($data) {

            // based on the data we can return an different source for the
            // importer. This is useful if you want create different records
            // based on i.e. an type value
            switch ($data->type) {
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
     * @expectedException \PSX\Data\NotFoundException
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

        $importer = new Importer(Environment::getService('extractor'), Environment::getService('importer_manager'));
        $importer->import('foobar', $message);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testImportEmptyBody()
    {
        $message = new Message();
        $message->addHeader('Content-Type', 'application/json');
        $message->setBody(new StringStream(''));

        $importer = new Importer(Environment::getService('extractor'), Environment::getService('importer_manager'));
        $importer->import(new Page(), $message);
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
