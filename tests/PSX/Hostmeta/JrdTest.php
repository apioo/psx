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

namespace PSX\Hostmeta;

use PSX\Json;

/**
 * XrdTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class JrdTest extends \PHPUnit_Framework_TestCase
{
	public function testImport()
	{
		$json = <<<JSON
{
      "subject":"http://blog.example.com/article/id/314",
      "expires":"2010-01-30T09:30:00+00:00",

      "aliases":[
        "http://blog.example.com/cool_new_thing",
        "http://blog.example.com/steve/article/7"],

      "properties":{
        "http://blgx.example.net/ns/version":"1.3",
        "http://blgx.example.net/ns/ext":null
      },

      "links":[
        {
          "rel":"author",
          "type":"text/html",
          "href":"http://blog.example.com/author/steve",
          "titles":{
            "default":"About the Author",
            "en-us":"Author Information"
          },
          "properties":{
            "http://example.com/role":"editor"
          }
        },
        {
          "rel":"author",
          "href":"http://example.com/author/john",
          "titles":{
            "default":"The other author"
          }
        },
        {
          "rel":"copyright",
          "template":"http://example.com/copyright?id={uri}"
        }
      ]
    }
JSON;

		$jrd = new Jrd();
		$jrd->import(Json::decode($json));

		$this->assertEquals('http://blog.example.com/article/id/314', $jrd->getSubject());
		$this->assertEquals('Sat, 30 Jan 2010 09:30:00 +0000', $jrd->getExpires()->format('r'));
		$this->assertEquals(array('http://blog.example.com/cool_new_thing', 'http://blog.example.com/steve/article/7'), $jrd->getAliases());
		$this->assertEquals(array('http://blgx.example.net/ns/version' => '1.3', 'http://blgx.example.net/ns/ext' => null), $jrd->getProperties());

		$links = $jrd->getLinks();

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

		$this->assertJsonStringEqualsJsonString($json, $jrd->export());
	}	
}
