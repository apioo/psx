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

namespace PSX\Data\Transformer;

/**
 * AtomTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
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
	<entry>
		<title>Atom-Powered Robots Run Amok</title>
		<id>urn:uuid:1225c695-cfb8-4ebb-aaaa-80da344efa6a</id>
		<updated>2003-12-13T18:30:02+00:00</updated>
		<link href="http://example.org/2003/12/13/atom03"/>
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
				)
			),
			'entry' => array(
				array(
					'type' => 'entry',
					'title' => 'Atom-Powered Robots Run Amok',
					'id' => 'urn:uuid:1225c695-cfb8-4ebb-aaaa-80da344efa6a',
					'updated' => '2003-12-13T18:30:02+00:00',
					'link' => array(
						array(
							'href' => 'http://example.org/2003/12/13/atom03'
						)
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
}
