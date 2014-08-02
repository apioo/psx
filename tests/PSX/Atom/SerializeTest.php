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

namespace PSX\Atom;

use DateTime;
use PSX\Data\Writer;
use PSX\Data\SerializeTestAbstract;
use PSX\Atom;

/**
 * SerializeTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class SerializeTest extends SerializeTestAbstract
{
	public function testSerialize()
	{
		$entry = new Entry();
		$entry->addAuthor(new Person('foobar', 'http://foo.com', 'foo@bar.com'));
		$entry->addCategory(new Category('foobar', 'http://foo.com', 'Foobar'));
		$entry->setContent(new Text('foobar'));
		$entry->addContributor(new Person('foobar', 'http://foo.com', 'foo@bar.com'));
		$entry->setId('http://localhost.com#1');
		$entry->setRights('foo');
		$entry->setTitle('Star City');
		$entry->setPublished(new DateTime('Tue, 10 Jun 2003 04:00:00 GMT'));
		$entry->setUpdated(new DateTime('Tue, 10 Jun 2003 04:00:00 GMT'));
		$entry->addLink(new Link('http://localhost.com', 'me', 'application/xml', 'en', 'Foobar', 1337));
		$entry->setSummary(new Text('lreom ipsum'));

		$atom = new Atom();
		$atom->addAuthor(new Person('foobar', 'http://foo.com', 'foo@bar.com'));
		$atom->addCategory(new Category('foobar', 'http://foo.com', 'Foobar'));
		$atom->addContributor(new Person('foobar', 'http://foo.com', 'foo@bar.com'));
		$atom->setGenerator(new Generator('foobar', 'http://foo.com', '1.0'));
		$atom->setIcon('http://localhost.com/icon.png');
		$atom->setLogo('http://localhost.com/logo.png');
		$atom->setId('http://localhost.com#1');
		$atom->setRights('foo');
		$atom->setTitle('Foo has bar');
		$atom->setUpdated(new DateTime('Tue, 10 Jun 2003 04:00:00 GMT'));
		$atom->addLink(new Link('http://localhost.com', 'me', 'application/xml', 'en', 'Foobar', 1337));
		$atom->setSubTitle(new Text('And some more content'));
		$atom->add($entry);

		$content = <<<JSON
{
  "author": [{
    "name": "foobar",
    "uri": "http://foo.com",
    "email": "foo@bar.com"
  }],
  "category": [{
    "term": "foobar",
    "scheme": "http://foo.com",
    "label": "Foobar"
  }],
  "contributor": [{
    "name": "foobar",
    "uri": "http://foo.com",
    "email": "foo@bar.com"
  }],
  "generator": {
    "text": "foobar",
    "uri": "http://foo.com",
    "version": "1.0"
  },
  "icon": "http://localhost.com/icon.png",
  "logo": "http://localhost.com/logo.png",
  "id": "http://localhost.com#1",
  "link": [{
    "href": "http://localhost.com",
    "rel": "me",
    "type": "application/xml",
    "hreflang": "en",
    "title": "Foobar",
    "length": 1337
  }],
  "rights": "foo",
  "subTitle": {
  	"content": "And some more content"
  },
  "title": "Foo has bar",
  "updated": "2003-06-10T04:00:00+00:00",
  "entry": [{
    "author": [{
      "name": "foobar",
      "uri": "http://foo.com",
      "email": "foo@bar.com"
    }],
    "category": [{
      "term": "foobar",
      "scheme": "http://foo.com",
      "label": "Foobar"
    }],
    "content": {
      "content": "foobar"
    },
    "contributor": [{
      "name": "foobar",
      "uri": "http://foo.com",
      "email": "foo@bar.com"
    }],
    "id": "http://localhost.com#1",
    "link": [{
      "href": "http://localhost.com",
      "rel": "me",
      "type": "application/xml",
      "hreflang": "en",
      "title": "Foobar",
      "length": 1337
    }],
    "published": "2003-06-10T04:00:00+00:00",
    "rights": "foo",
    "summary": {
      "content": "lreom ipsum"
    },
    "title": "Star City",
    "updated": "2003-06-10T04:00:00+00:00"
  }]
} 
JSON;

		$this->assertRecordEqualsContent($atom, $content);
	}
}
