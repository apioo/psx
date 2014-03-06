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
use PSX\ActivityStream\Address;
use PSX\ActivityStream\Position;
use PSX\DateTime;
use PSX\Data\SerializeTestAbstract;

/**
 * PlaceTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class PlaceTest extends SerializeTestAbstract
{
	public function testPlacePosition()
	{
		$position = new Position();
		$position->setLatitude(34.34);
		$position->setLongitude(-127.23);
		$position->setAltitude(100.05);

		$place = new Place();
		$place->setDisplayName('Some Random Location on Earth');
		$place->setPosition($position);

		$content = <<<JSON
  {
    "objectType": "place",
    "displayName": "Some Random Location on Earth",
    "position": {
      "latitude": 34.34,
      "longitude": -127.23,
      "altitude": 100.05
    }
  }
JSON;

		$this->assertRecordEqualsContent($place, $content);

		$this->assertEquals($position, $place->getPosition());
	}

	public function testPlaceAddress()
	{
		$address = new Address();
		$address->setFormatted('111 North First Street, New York, NY 11111');
		$address->setStreetAddress('111 North First Street');
		$address->setLocality('New York');
		$address->setRegion('NY');
		$address->setPostalCode('11111');
		$address->setCountry('US');

		$place = new Place();
		$place->setDisplayName('This is not really my address');
		$place->setAddress($address);

		$content = <<<JSON
  {
    "objectType": "place",
    "displayName": "This is not really my address",
    "address": {
      "formatted": "111 North First Street, New York, NY 11111",
      "streetAddress": "111 North First Street",
      "locality": "New York",
      "region": "NY",
      "postalCode": "11111",
      "country": "US"
    }
  }
JSON;

		$this->assertRecordEqualsContent($place, $content);

		$this->assertEquals($address, $place->getAddress());
	}
}
