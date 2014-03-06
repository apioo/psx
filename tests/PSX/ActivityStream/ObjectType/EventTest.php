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

namespace PSX\ActivityStream\ObjectType;

use PSX\ActivityStream\LinkObject;
use PSX\ActivityStream\Object;
use PSX\DateTime;
use PSX\Data\SerializeTestAbstract;

/**
 * EventTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class EventTest extends SerializeTestAbstract
{
	public function testEvent()
	{
		$person = new Object();
		$person->setObjectType('person');
		$person->setDisplayName('Joe');

		$persons = new Collection();
		$persons->add($person);

		$event = new Event();
		$event->setDisplayName('Meeting with Joe');
		$event->setStartTime(new DateTime('2012-12-12T12:00:00Z'));
		$event->setEndTime(new DateTime('2012-12-12T13:00:00Z'));
		$event->setAttendedBy($persons);
		$event->setAttending($persons);
		$event->setInvited($persons);
		$event->setMaybeAttending($persons);
		$event->setNotAttendedBy($persons);

		$content = <<<JSON
{
  "attendedBy": {"items": [{
        "objectType": "person",
        "displayName": "Joe"
      }]},
  "attending": {"items": [{
        "objectType": "person",
        "displayName": "Joe"
      }]},
  "endTime": "2012-12-12T13:00:00+00:00",
  "invited": {"items": [{
        "objectType": "person",
        "displayName": "Joe"
      }]},
  "maybeAttending": {"items": [{
        "objectType": "person",
        "displayName": "Joe"
      }]},
  "notAttendedBy": {"items": [{
        "objectType": "person",
        "displayName": "Joe"
      }]},
  "startTime": "2012-12-12T12:00:00+00:00",
  "objectType": "event",
  "displayName": "Meeting with Joe"
}
JSON;

		$this->assertRecordEqualsContent($event, $content);

		$this->assertEquals('Meeting with Joe', $event->getDisplayName());
		$this->assertEquals(new DateTime('2012-12-12T12:00:00Z'), $event->getStartTime());
		$this->assertEquals(new DateTime('2012-12-12T13:00:00Z'), $event->getEndTime());
		$this->assertEquals($persons, $event->getAttendedBy());
		$this->assertEquals($persons, $event->getAttending());
		$this->assertEquals($persons, $event->getInvited());
		$this->assertEquals($persons, $event->getMaybeAttending());
		$this->assertEquals($persons, $event->getNotAttendedBy());
	}
}
