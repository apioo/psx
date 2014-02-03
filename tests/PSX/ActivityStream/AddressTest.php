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

namespace PSX\ActivityStream;

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
		$address->setFormatted('111 North First Street, New York, NY 11111');
		$address->setStreetAddress('111 North First Street');
		$address->setLocality('New York');
		$address->setRegion('NY');
		$address->setPostalCode('11111');
		$address->setCountry('US');

		$content = <<<JSON
{
  "formatted": "111 North First Street, New York, NY 11111",
  "streetAddress": "111 North First Street",
  "locality": "New York",
  "region": "NY",
  "postalCode": "11111",
  "country": "US"
}
JSON;

		$this->assertRecordEqualsContent($address, $content);

		$this->assertEquals('111 North First Street, New York, NY 11111', $address->getFormatted());
		$this->assertEquals('111 North First Street', $address->getStreetAddress());
		$this->assertEquals('New York', $address->getLocality());
		$this->assertEquals('NY', $address->getRegion());
		$this->assertEquals('11111', $address->getPostalCode());
		$this->assertEquals('US', $address->getCountry());
	}
}
