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

namespace PSX\ActivityStream\ObjectType;

use PSX\ActivityStream\LinkObject;
use PSX\DateTime;
use PSX\Data\SerializeTestAbstract;

/**
 * AudioTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class AudioTest extends SerializeTestAbstract
{
	public function testAudio()
	{
		$stream = new LinkObject();
		$stream->setUrl('http://example.org/my_audio.mp3');

		$audio = new Audio();
		$audio->setDisplayName('Cute little kittens');
		$audio->setEmbedCode('<audio controls=\'controls\'>...</audio>');
		$audio->setStream($stream);

		$content = <<<JSON
  {
    "objectType": "audio",
    "displayName": "Cute little kittens",
    "embedCode": "<audio controls='controls'>...</audio>",
    "stream": {
      "url": "http://example.org/my_audio.mp3"
    }
  }
JSON;

		$this->assertRecordEqualsContent($audio, $content);
	}
}
