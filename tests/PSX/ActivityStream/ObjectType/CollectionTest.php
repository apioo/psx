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

namespace PSX\ActivityStream\ObjectType;

use PSX\ActivityStream\Object;
use PSX\Data\SerializeTestAbstract;
use PSX\DateTime;

/**
 * CollectionTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class CollectionTest extends SerializeTestAbstract
{
    public function testCollection()
    {
        $items = array();

        $item = new Object();
        $item->setContent('This was my first comment');
        $item->setUpdated(new DateTime('2011-11-21T15:13:59+00:00'));
        $item->setId('f8f0e93f-e462-4ede-92cc-f6e8a1b7eb36');

        $items[] = $item;

        $item = new Object();
        $item->setContent('This was another comment');
        $item->setUpdated(new DateTime('2011-11-21T15:14:06+00:00'));
        $item->setId('5369ea82-d791-46cb-a87a-3696ff90d8f3');

        $items[] = $item;

        $collection = new Collection();
        $collection->setItems($items);
        $collection->setTotalItems(4);
        $collection->setItemsAfter(new DateTime('2011-11-21T15:13:59+00:00'));
        $collection->setItemsBefore(new DateTime('2011-11-21T15:13:59+00:00'));
        $collection->setItemsPerPage(2);
        $collection->setStartIndex(0);
        $collection->setFirst('urn:foo:page:1');
        $collection->setLast('urn:foo:page:4');
        $collection->setPrev('urn:foo:page:2');
        $collection->setNext('urn:foo:page:4');
        $collection->setCurrent('urn:foo:page:3');
        $collection->setSelf('urn:foo:page:3');

        $content = <<<JSON
  {
    "totalItems": 4,
    "items": [{
    	"id": "f8f0e93f-e462-4ede-92cc-f6e8a1b7eb36",
    	"content": "This was my first comment",
    	"updated": "2011-11-21T15:13:59Z"
    },{
    	"id": "5369ea82-d791-46cb-a87a-3696ff90d8f3",
    	"content": "This was another comment",
    	"updated": "2011-11-21T15:14:06Z"
    }],
    "itemsAfter": "2011-11-21T15:13:59Z",
    "itemsBefore": "2011-11-21T15:13:59Z",
    "itemsPerPage": 2,
    "startIndex": 0,
    "first": "urn:foo:page:1",
    "last": "urn:foo:page:4",
    "prev": "urn:foo:page:2",
    "next": "urn:foo:page:4",
    "current": "urn:foo:page:3",
    "self": "urn:foo:page:3"
  }
JSON;

        $this->assertRecordEqualsContent($collection, $content);

        $this->assertEquals($items, $collection->getItems());
        $this->assertEquals(4, $collection->getTotalItems());
        $this->assertEquals(new DateTime('2011-11-21T15:13:59+00:00'), $collection->getItemsAfter());
        $this->assertEquals(new DateTime('2011-11-21T15:13:59+00:00'), $collection->getItemsBefore());
        $this->assertEquals(2, $collection->getItemsPerPage());
        $this->assertEquals(0, $collection->getStartIndex());
        $this->assertEquals('urn:foo:page:1', $collection->getFirst());
        $this->assertEquals('urn:foo:page:4', $collection->getLast());
        $this->assertEquals('urn:foo:page:2', $collection->getPrev());
        $this->assertEquals('urn:foo:page:4', $collection->getNext());
        $this->assertEquals('urn:foo:page:3', $collection->getCurrent());
        $this->assertEquals('urn:foo:page:3', $collection->getSelf());
    }

    public function testCollectionIterator()
    {
        $items = range(1, 4);

        $collection = new Collection();
        $collection->setItems($items);

        $i = 1;
        foreach ($collection as $value) {
            $this->assertEquals($i, $value);

            $i++;
        }

        $this->assertEquals(4, count($collection));
        $this->assertEquals($items, $collection->toArray());
        $this->assertEquals(3, $collection->get(2));

        $collection->set(2, new Object());

        $this->assertInstanceOf('PSX\ActivityStream\Object', $collection->get(2));
    }
}
