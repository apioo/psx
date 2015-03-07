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

namespace PSX\Data\Transformer;

use PSX\Http\MediaType;

/**
 * AtomTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class AtomTest extends \PHPUnit_Framework_TestCase
{
	public function testTransform()
	{
		$body = <<<INPUT
<?xml version="1.0" encoding="UTF-8"?>
<feed xmlns="http://www.w3.org/2005/Atom">
	<title>Example Feed</title>
	<id>urn:uuid:60a76c80-d399-11d9-b93C-0003939e0af6</id>
	<updated>2003-12-13T18:30:02+00:00</updated>
	<link href="http://example.org/"/>
	<author>
		<name>John Doe</name>
	</author>
	<author>
		<name>John Doe</name>
	</author>
	<contributor>
		<name>John Doe</name>
	</contributor>
	<contributor>
		<name>John Doe</name>
	</contributor>
	<category term="news" scheme="urn:foo:bar" label="News" />
	<entry>
		<title>Atom-Powered Robots Run Amok</title>
		<id>urn:uuid:1225c695-cfb8-4ebb-aaaa-80da344efa6a</id>
		<updated>2003-12-13T18:30:02+00:00</updated>
		<link href="http://example.org/2003/12/13/atom03" title="Website" />
		<category term="news" scheme="urn:foo:bar" label="News" />
		<summary>Some text.</summary>
	</entry>
</feed>
INPUT;

		$dom = new \DOMDocument();
		$dom->loadXML($body);

		$transformer = new Atom();

		$expect = array(
			'type' => 'feed',
			'title' => 'Example Feed',
			'id' => 'urn:uuid:60a76c80-d399-11d9-b93C-0003939e0af6',
			'updated' => '2003-12-13T18:30:02+00:00',
			'link' => array(
				array(
					'href' => 'http://example.org/',
				)
			),
			'author' => array(
				array(
					'name' => 'John Doe',
				),
				array(
					'name' => 'John Doe',
				)
			),
			'contributor' => array(
				array(
					'name' => 'John Doe',
				),
				array(
					'name' => 'John Doe',
				)
			),
			'category' => array(
				array(
					'term' => 'news',
					'scheme' => 'urn:foo:bar',
					'label' => 'News',
				),
			),
			'entry' => array(
				array(
					'type' => 'entry',
					'title' => 'Atom-Powered Robots Run Amok',
					'id' => 'urn:uuid:1225c695-cfb8-4ebb-aaaa-80da344efa6a',
					'updated' => '2003-12-13T18:30:02+00:00',
					'link' => array(
						array(
							'href' => 'http://example.org/2003/12/13/atom03',
							'title' => 'Website',
						)
					),
					'category' => array(
						array(
							'term' => 'news',
							'scheme' => 'urn:foo:bar',
							'label' => 'News',
						),
					),
					'summary' => array(
						'content' => 'Some text.'
					),
				)
			),
		);

		$data = $transformer->transform($dom);

		$this->assertTrue(is_array($data));
		$this->assertEquals($expect, $data);
	}

	public function testBase64TextConstruct()
	{
		$body = <<<INPUT
<?xml version="1.0" encoding="UTF-8"?>
<feed xmlns="http://www.w3.org/2005/Atom">
	<entry>
		<title>Atom-Powered Robots Run Amok</title>
		<id>urn:uuid:1225c695-cfb8-4ebb-aaaa-80da344efa6a</id>
		<updated>2003-12-13T18:30:02+00:00</updated>
		<content type="application/octet-stream">Zm9vYmFy</content>
	</entry>
</feed>
INPUT;

		$dom = new \DOMDocument();
		$dom->loadXML($body);

		$transformer = new Atom();

		$expect = array(
			'type' => 'feed',
			'entry' => array(
				array(
					'type' => 'entry',
					'title' => 'Atom-Powered Robots Run Amok',
					'id' => 'urn:uuid:1225c695-cfb8-4ebb-aaaa-80da344efa6a',
					'updated' => '2003-12-13T18:30:02+00:00',
					'content' => array(
						'type' => 'application/octet-stream',
						'content' => 'foobar',
					),
				)
			),
		);

		$data = $transformer->transform($dom);

		$this->assertTrue(is_array($data));
		$this->assertEquals($expect, $data);
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testNoFeedElement()
	{
		$body = <<<INPUT
<?xml version="1.0" encoding="UTF-8"?>
<foo />
INPUT;

		$dom = new \DOMDocument();
		$dom->loadXML($body);

		$transformer = new Atom();
		$transformer->transform($dom);
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testInvalidData()
	{
		$transformer = new Atom();
		$transformer->transform(array());
	}

	public function testAccept()
	{
		$transformer = new Atom();

		$this->assertTrue($transformer->accept(MediaType::parse('application/atom+xml')));
	}

	public function testAcceptInvalid()
	{
		$transformer = new Atom();

		$this->assertFalse($transformer->accept(MediaType::parse('text/plain')));
	}
}
