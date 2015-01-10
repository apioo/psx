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

use PSX\DateTime;
use PSX\Data\SerializeTestAbstract;

/**
 * IssueTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class IssueTest extends SerializeTestAbstract
{
	public function testIssue()
	{
		$issue = new Issue();
		$issue->setDisplayName('Terms of Use Violation');
		$issue->setUrl('http://.../terms-of-use');
		$issue->setTypes(array('http://example.org/codes/inappropriateMaterial', 'http://example.org/codes/copyrightViolation'));

		$content = <<<JSON
  {
    "objectType": "issue",
    "displayName": "Terms of Use Violation",
    "url": "http://.../terms-of-use",
    "types": [
      "http://example.org/codes/inappropriateMaterial",
      "http://example.org/codes/copyrightViolation"
    ]
  }
JSON;

		$this->assertRecordEqualsContent($issue, $content);

		$this->assertEquals(array('http://example.org/codes/inappropriateMaterial', 'http://example.org/codes/copyrightViolation'), $issue->getTypes());
	}
}
