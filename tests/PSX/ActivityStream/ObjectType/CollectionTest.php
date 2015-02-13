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

namespace PSX\ActivityStream\ObjectType;

use PSX\ActivityStream\Object;
use PSX\DateTime;
use PSX\Data\SerializeTestAbstract;

/**
 * CollectionTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
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
    	"updated": "2011-11-21T15:13:59+00:00"
    },{
    	"id": "5369ea82-d791-46cb-a87a-3696ff90d8f3",
    	"content": "This was another comment",
    	"updated": "2011-11-21T15:14:06+00:00"
    }],
    "itemsAfter": "2011-11-21T15:13:59+00:00",
    "itemsBefore": "2011-11-21T15:13:59+00:00",
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
		foreach($collection as $value)
		{
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
