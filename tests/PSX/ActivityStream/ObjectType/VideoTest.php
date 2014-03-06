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
use PSX\DateTime;
use PSX\Data\SerializeTestAbstract;

/**
 * VideoTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class VideoTest extends SerializeTestAbstract
{
	public function testVideo()
	{
		$stream = new LinkObject();
		$stream->setUrl('http://example.org/my_video.mpg');

		$video = new Video();
		$video->setDisplayName('Cute little kittens');
		$video->setEmbedCode('<video width=\'320\' height=\'240\' controls=\'controls\'>...</video>');
		$video->setStream($stream);

		$content = <<<JSON
  {
    "objectType": "video",
    "displayName": "Cute little kittens",
    "embedCode": "<video width='320' height='240' controls='controls'>...</video>",
    "stream": {
      "url": "http://example.org/my_video.mpg"
    }
  }
JSON;

		$this->assertRecordEqualsContent($video, $content);
	}
}
