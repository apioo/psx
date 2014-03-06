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
 * PermissionTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class PermissionTest extends SerializeTestAbstract
{
	public function testPermission()
	{
		$file = new Object();
		$file->setObjectType('file');
		$file->setDisplayName('2Q2014 Sales Forecast.xls');

		$html = new Object();
		$html->setObjectType('inline-html');
		$html->setContent('<video ... />');

		$share = new LinkObject();
		$share->setObjectType('service');
		$share->setDisplayName('My Sharing Service');
		$share->setUrl('http://example.net/share');

		$permission = new Permission();
		$permission->setDisplayName('Permission to Edit File: 2Q2014 Sales Forecast.xls');
		$permission->setScope($file);
		$permission->addAction('watch', 'movie://example.org/cats.mpg');
		$permission->addAction('share', $share);
		$permission->addAction('embed', array('http://example.org/gadgets/video.xml?v=cats.mpg', $html));

		$content = <<<JSON
  {
    "objectType": "permission",
    "displayName": "Permission to Edit File: 2Q2014 Sales Forecast.xls",
    "scope": {
      "objectType": "file",
      "displayName": "2Q2014 Sales Forecast.xls"
    },
    "actions": {
      "watch": "movie://example.org/cats.mpg",
      "share": {
        "objectType": "service",
        "displayName": "My Sharing Service",
        "url": "http://example.net/share"
      },
      "embed": [
        "http://example.org/gadgets/video.xml?v=cats.mpg",
        {
          "objectType": "inline-html",
          "content": "<video ... />"
        }
      ]
    }
  }
JSON;

		$this->assertRecordEqualsContent($permission, $content);

    $this->assertEquals($file, $permission->getScope());
	}
}
