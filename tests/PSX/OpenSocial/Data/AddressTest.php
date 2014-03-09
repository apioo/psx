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

namespace PSX\OpenSocial\Data;

use PSX\Data\Writer;
use PSX\Data\SerializeTestAbstract;

/**
 * AddressTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class AddressTest extends SerializeTestAbstract
{
	public function testAddress()
	{
		$address = new Address();
		$address->setCountry('US');
		$address->setFormatted('111 North First Street, New York, NY 11111');
		$address->setLatitude(1);
		$address->setLocality('New York');
		$address->setLongitude(1);
		$address->setPostalCode('11111');
		$address->setRegion('NY');
		$address->setStreetAddress('111 North First Street');
		$address->setType('work');

		$content = <<<JSON
{
  "country": "US",
  "formatted": "111 North First Street, New York, NY 11111",
  "latitude": 1,
  "locality": "New York",
  "longitude": 1,
  "postalCode": "11111",
  "region": "NY",
  "streetAddress": "111 North First Street",
  "type": "work"
} 
JSON;

		$this->assertRecordEqualsContent($address, $content);
	}
}
