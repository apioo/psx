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

namespace PSX\Model\Tests\ActivityStream\ObjectType;

use PSX\Data\Tests\SerializeTestAbstract;
use PSX\Model\ActivityStream\Collection;
use PSX\Model\ActivityStream\ObjectType;

/**
 * RoleTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class RoleTest extends SerializeTestAbstract
{
    public function testRole()
    {
        $laura = new ObjectType();
        $laura->setObjectType('person');
        $laura->setDisplayName('Laura');

        $mark = new ObjectType();
        $mark->setObjectType('person');
        $mark->setDisplayName('Mark');

        $members = new Collection();
        $members->setItems([$laura, $mark]);

        $role = new ObjectType\Role();
        $role->setDisplayName('My Work Group');
        $role->setMembers($members);

        $content = <<<JSON
  {
    "objectType": "role",
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

        $this->assertRecordEqualsContent($role, $content);

        $this->assertEquals($members, $role->getMembers());
    }
}
