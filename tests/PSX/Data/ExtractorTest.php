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

use PSX\Http\Message;
use PSX\Http\Stream\StringStream;

/**
 * ExtractorTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
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

		$extractor = new Extractor(getContainer()->get('reader_factory'), getContainer()->get('transformer_manager'));

		$data = $extractor->extract($message);

		$this->assertEquals(array('foo' => 'bar'), $data);
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

		$extractor = new Extractor(getContainer()->get('reader_factory'), getContainer()->get('transformer_manager'));

		$data = $extractor->extract($message);

		$this->assertEquals(array('foo' => 'bar'), $data);
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

		$extractor = new Extractor(getContainer()->get('reader_factory'), getContainer()->get('transformer_manager'));

		$data = $extractor->extract($message);

		$this->assertEquals(array(
			'entry' => array(
				array(
					'title' => 'Atom-Powered Robots Run Amok',
					'id' => 'urn:uuid:1225c695-cfb8-4ebb-aaaa-80da344efa6a',
					'updated' => '2003-12-13T18:30:02+00:00',
					'link' => array(
						array(
							'href' => 'http://example.org/2003/12/13/atom03',
						)
					),
					'summary' => array(
						'content' => 'Some text.',
					),
					'type' => 'entry',
				)
			)
		), $data);
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

		$extractor = new Extractor(getContainer()->get('reader_factory'), getContainer()->get('transformer_manager'));
		$testCase  = $this;

		$data = $extractor->extract($message, new Transformer\Callback(function($data) use ($testCase){

			$testCase->assertInstanceOf('DOMDocument', $data);

			$result = $data->documentElement->getElementsByTagName('title');
			$titles = array();

			foreach($result as $titleElement)
			{
				$titles[] = (string) $titleElement->nodeValue;
			}

			return array(
				'titles' => $titles
			);

		}));

		$this->assertEquals(array(
			'titles' => array('Atom-Powered Robots Run Amok')
		), $data);
	}

	public function testExtractJsonExplicitReader()
	{
		$data = <<<TEXT
{"foo": "bar"}
TEXT;

		$message = new Message();
		$message->addHeader('Content-Type', 'text/plain');
		$message->setBody(new StringStream($data));

		$extractor = new Extractor(getContainer()->get('reader_factory'), getContainer()->get('transformer_manager'));

		$data = $extractor->extract($message, null, ReaderInterface::JSON);

		$this->assertEquals(array('foo' => 'bar'), $data);
	}
}
