<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\ActivityStream\ObjectType;

use PSX\ActivityStream\Object;
use PSX\Data\SerializeTestAbstract;
use PSX\DateTime;

/**
 * VideoTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class VideoTest extends SerializeTestAbstract
{
	public function testVideo()
	{
		$stream = new Object();
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

		$this->assertEquals('video', $video->getObjectType());
		$this->assertEquals('Cute little kittens', $video->getDisplayName());
		$this->assertEquals('<video width=\'320\' height=\'240\' controls=\'controls\'>...</video>', $video->getEmbedCode());
		$this->assertEquals('http://example.org/my_video.mpg', $video->getStream()->getUrl());
	}
}
