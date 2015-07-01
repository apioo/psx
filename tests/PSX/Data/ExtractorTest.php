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

namespace PSX\Data;

use PSX\Http\Message;
use PSX\Http\Stream\StringStream;
use PSX\Test\Environment;

/**
 * ExtractorTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ExtractorTest extends \PHPUnit_Framework_TestCase
{
    public function testExtractJson()
    {
        $data = <<<TEXT
{"foo": "bar"}
TEXT;

        $message = new Message();
        $message->addHeader('Content-Type', 'application/json');
        $message->setBody(new StringStream($data));

        $extractor = new Extractor(Environment::getService('reader_factory'), Environment::getService('transformer_manager'));

        $data = $extractor->extract($message);

        $expect = new \stdClass();
        $expect->foo = 'bar';

        $this->assertEquals($expect, $data);
    }

    public function testExtractXml()
    {
        $data = <<<TEXT
<entry>
	<foo>bar</foo>
</entry>
TEXT;

        $message = new Message();
        $message->addHeader('Content-Type', 'application/xml');
        $message->setBody(new StringStream($data));

        $extractor = new Extractor(Environment::getService('reader_factory'), Environment::getService('transformer_manager'));

        $data = $extractor->extract($message);

        $expect = new \stdClass();
        $expect->foo = 'bar';

        $this->assertEquals($expect, $data);
    }

    public function testExtractXmlImplicitTransformer()
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

        $extractor = new Extractor(Environment::getService('reader_factory'), Environment::getService('transformer_manager'));

        $data = $extractor->extract($message);

        $expect = new \stdClass();
        $expect->entry = [];
        $expect->entry[0] = new \stdClass();
        $expect->entry[0]->title = 'Atom-Powered Robots Run Amok';
        $expect->entry[0]->id = 'urn:uuid:1225c695-cfb8-4ebb-aaaa-80da344efa6a';
        $expect->entry[0]->updated = '2003-12-13T18:30:02+00:00';
        $expect->entry[0]->link = [];
        $expect->entry[0]->link[0] = new \stdClass();
        $expect->entry[0]->link[0]->href = 'http://example.org/2003/12/13/atom03';
        $expect->entry[0]->summary = new \stdClass();
        $expect->entry[0]->summary->content = 'Some text.';
        $expect->entry[0]->type = 'entry';

        $this->assertEquals($expect, $data);
    }

    public function testExtractXmlExplicitTransformer()
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

        $extractor = new Extractor(Environment::getService('reader_factory'), Environment::getService('transformer_manager'));
        $testCase  = $this;

        $data = $extractor->extract($message, new Transformer\Callback(function ($data) use ($testCase) {

            $testCase->assertInstanceOf('DOMDocument', $data);

            $result = $data->documentElement->getElementsByTagName('title');
            $titles = array();

            foreach ($result as $titleElement) {
                $titles[] = (string) $titleElement->nodeValue;
            }

            $result = new \stdClass();
            $result->titles = $titles;

            return $result;

        }));

        $expect = new \stdClass();
        $expect->titles = array('Atom-Powered Robots Run Amok');

        $this->assertEquals($expect, $data);
    }

    public function testExtractJsonExplicitReader()
    {
        $data = <<<TEXT
{"foo": "bar"}
TEXT;

        $message = new Message();
        $message->addHeader('Content-Type', 'text/plain');
        $message->setBody(new StringStream($data));

        $extractor = new Extractor(Environment::getService('reader_factory'), Environment::getService('transformer_manager'));

        $data = $extractor->extract($message, null, ReaderInterface::JSON);

        $expect = new \stdClass();
        $expect->foo = 'bar';

        $this->assertEquals($expect, $data);
    }
}
