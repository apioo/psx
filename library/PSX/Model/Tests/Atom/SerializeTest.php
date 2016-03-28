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

namespace PSX\Atom;

use DateTime;
use PSX\Data\Tests\SerializeTestAbstract;
use PSX\Model\Atom\Atom;
use PSX\Model\Atom\Category;
use PSX\Model\Atom\Entry;
use PSX\Model\Atom\Generator;
use PSX\Model\Atom\Link;
use PSX\Model\Atom\Person;
use PSX\Model\Atom\Text;

/**
 * SerializeTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
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
        $atom->addEntry($entry);

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
  "updated": "2003-06-10T04:00:00Z",
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
    "published": "2003-06-10T04:00:00Z",
    "rights": "foo",
    "summary": {
      "content": "lreom ipsum"
    },
    "title": "Star City",
    "updated": "2003-06-10T04:00:00Z"
  }]
}
JSON;

        $this->assertRecordEqualsContent($atom, $content);
    }
}
