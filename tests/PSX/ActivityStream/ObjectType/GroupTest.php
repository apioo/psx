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

use PSX\ActivityStream\Object;
use PSX\DateTime;
use PSX\Data\SerializeTestAbstract;

/**
 * GroupTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class GroupTest extends SerializeTestAbstract
{
	public function testGroup()
	{
		$laura = new Object();
		$laura->setObjectType('person');
		$laura->setDisplayName('Laura');

		$mark = new Object();
		$mark->setObjectType('person');
		$mark->setDisplayName('Mark');

		$members = new Collection();
		$members->add($laura);
		$members->add($mark);

		$group = new Group();
		$group->setDisplayName('My Work Group');
		$group->setMembers($members);

		$content = <<<JSON
  {
    "objectType": "group",
    "displayName": "My Work Group",
    "members": {
      "items": [
        {
          "objectType": "person",
          "displayName": "Laura"
        },
        {
          "objectType": "person",
          "displayName": "Mark"
        }
      ]
    }
  }
JSON;

		$this->assertRecordEqualsContent($group, $content);

		$this->assertEquals($members, $group->getMembers());
	}
}
