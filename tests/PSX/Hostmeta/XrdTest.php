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

namespace PSX\Hostmeta;

/**
 * XrdTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class XrdTest extends \PHPUnit_Framework_TestCase
{
	public function testImport()
	{
		$xml = <<<XML
<?xml version='1.0' encoding='UTF-8'?>
    <XRD xmlns='http://docs.oasis-open.org/ns/xri/xrd-1.0'
         xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance'>

      <Subject>http://blog.example.com/article/id/314</Subject>
      <Expires>2010-01-30T09:30:00+00:00</Expires>

      <Alias>http://blog.example.com/cool_new_thing</Alias>
      <Alias>http://blog.example.com/steve/article/7</Alias>

      <Property type='http://blgx.example.net/ns/version'>1.3</Property>
      <Property type='http://blgx.example.net/ns/ext' xsi:nil='true' />

      <Link rel='author' type='text/html'
            href='http://blog.example.com/author/steve'>
        <Title>About the Author</Title>
        <Title xml:lang='en-us'>Author Information</Title>
        <Property type='http://example.com/role'>editor</Property>
      </Link>

      <Link rel='author' href='http://example.com/author/john'>
        <Title>The other author</Title>
      </Link>
      <Link rel='copyright'
            template='http://example.com/copyright?id={uri}' />
    </XRD>
XML;

		$xrd = new Xrd();
		$xrd->import(simplexml_load_string($xml));

		$this->assertEquals('http://blog.example.com/article/id/314', $xrd->getSubject());
		$this->assertEquals('Sat, 30 Jan 2010 09:30:00 +0000', $xrd->getExpires()->format('r'));
		$this->assertEquals(array('http://blog.example.com/cool_new_thing', 'http://blog.example.com/steve/article/7'), $xrd->getAliases());
		$this->assertEquals(array('http://blgx.example.net/ns/version' => '1.3', 'http://blgx.example.net/ns/ext' => null), $xrd->getProperties());

		$links = $xrd->getLinks();

		$this->assertEquals('author', $links[0]->getRel());
		$this->assertEquals('text/html', $links[0]->getType());
		$this->assertEquals('http://blog.example.com/author/steve', $links[0]->getHref());
		$this->assertEquals(array('default' => 'About the Author', 'en-us' => 'Author Information'), $links[0]->getTitles());
		$this->assertEquals(array('http://example.com/role' => 'editor'), $links[0]->getProperties());

		$this->assertEquals('author', $links[1]->getRel());
		$this->assertEquals('http://example.com/author/john', $links[1]->getHref());
		$this->assertEquals(array('default' => 'The other author'), $links[1]->getTitles());

		$this->assertEquals('copyright', $links[2]->getRel());
		$this->assertEquals('http://example.com/copyright?id={uri}', $links[2]->getTemplate());

		$this->assertXmlStringEqualsXmlString($xml, $xrd->export());
	}	
}
