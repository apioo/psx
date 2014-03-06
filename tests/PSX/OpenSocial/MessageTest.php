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

namespace PSX\OpenSocial;

use PSX\Data\Writer;
use PSX\Data\SerializeTestAbstract;

/**
 * MessageTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class MessageTest extends SerializeTestAbstract
{
	public function testMessage()
	{
		$message = new Data\Message();
		$message->setId('http://example.org/inbox/message/{msgid}');
		$message->setRecipients(array('example.org:AD38B3886625AAF', 'example.org:997638BAA6F25AD'));
		$message->setTitle('You have a new messge from Joe');
		$message->setTitleId('541141091700');
		$message->setBody('Short message from Joe to some friends');
		$message->setBodyId('5491155811231');
		$message->setType('privateMessage');
		$message->setStatus('unread');

		$content = <<<JSON
{
  "id": "http://example.org/inbox/message/{msgid}",
  "recipients": ["example.org:AD38B3886625AAF", "example.org:997638BAA6F25AD"],
  "title": "You have a new messge from Joe",
  "titleId": "541141091700",
  "body": "Short message from Joe to some friends",
  "bodyId": "5491155811231",  
  "type": "privateMessage",
  "status": "unread"
}
JSON;

		$this->assertRecordEqualsContent($message, $content);
	}
}
