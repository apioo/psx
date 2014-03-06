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
 * MediaItemTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class MediaItemTest extends SerializeTestAbstract
{
	public function testMediaItem()
	{
		$mediaItem = new Data\MediaItem();
		$mediaItem->setId('11223344');
		$mediaItem->setThumbnailUrl('http://pages.example.org/images/11223344-tn.png');
		$mediaItem->setMimeType('image/png');
		$mediaItem->setType('image');
		$mediaItem->setUrl('http://pages.example.org/images/11223344.png');
		$mediaItem->setAlbumId('44332211');

		$content = <<<JSON
{
  "id": "11223344",
  "thumbnail_url": "http://pages.example.org/images/11223344-tn.png",
  "mime_type": "image/png",
  "type": "image",
  "url": "http://pages.example.org/images/11223344.png",
  "album_id": "44332211"
}
JSON;

		$this->assertRecordEqualsContent($mediaItem, $content);
	}
}
