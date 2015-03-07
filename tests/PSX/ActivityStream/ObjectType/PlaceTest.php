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

use PSX\ActivityStream\Address;
use PSX\ActivityStream\Position;
use PSX\DateTime;
use PSX\Data\SerializeTestAbstract;

/**
 * PlaceTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
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
