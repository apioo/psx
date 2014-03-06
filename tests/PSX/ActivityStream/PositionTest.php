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

namespace PSX\ActivityStream;

use PSX\Data\SerializeTestAbstract;

/**
 * PositionTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class PositionTest extends SerializeTestAbstract
{
	public function testPosition()
	{
		$position = new Position();
		$position->setLatitude(34.34);
		$position->setLongitude(-127.23);
		$position->setAltitude(100.05);

		$content = <<<JSON
{
  "latitude": 34.34,
  "longitude": -127.23,
  "altitude": 100.05
}
JSON;

		$this->assertRecordEqualsContent($position, $content);

		$this->assertEquals(34.34, $position->getLatitude());
		$this->assertEquals(-127.23, $position->getLongitude());
		$this->assertEquals(100.05, $position->getAltitude());
	}
}
