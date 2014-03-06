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

namespace PSX\OpenSocial\Data;

use PSX\Data\RecordAbstract;
use PSX\Data\RecordInfo;

/**
 * Album
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Album extends RecordAbstract
{
	protected $description;
	protected $id;
	protected $location;
	protected $mediaItemCount;
	protected $mediaMimeType;
	protected $mediaType;
	protected $ownerId;
	protected $thumbnailUrl;
	protected $title;

	public function getRecordInfo()
	{
		return new RecordInfo('album', array(
			'description'    => $this->description,
			'id'             => $this->id,
			'location'       => $this->location,
			'mediaItemCount' => $this->mediaItemCount,
			'mediaMimeType'  => $this->mediaMimeType,
			'mediaType'      => $this->mediaType,
			'ownerId'        => $this->ownerId,
			'thumbnailUrl'   => $this->thumbnailUrl,
			'title'          => $this->title,
		));
	}

	/**
	 * @param string
	 */
	public function setDescription($description)
	{
		$this->description = $description;
	}
	
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * @param string
	 */
	public function setId($id)
	{
		$this->id = $id;
	}
	
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param PSX\OpenSocial\Data\Address
	 */
	public function setLocation(Address $location)
	{
		$this->location = $location;
	}
	
	public function getLocation()
	{
		return $this->location;
	}

	/**
	 * @param integer
	 */
	public function setMediaItemCount($mediaItemCount)
	{
		$this->mediaItemCount = $mediaItemCount;
	}

	public function getMediaItemCount()
	{
		return $this->mediaItemCount;
	}

	/**
	 * @param array
	 */
	public function setMediaMimeType(array $mediaMimeType)
	{
		$this->mediaMimeType = $mediaMimeType;
	}

	public function getMediaMimeType()
	{
		return $this->mediaMimeType;
	}

	/**
	 * @param array
	 */
	public function setMediaType(array $mediaType)
	{
		$this->mediaType = $mediaType;
	}

	public function getMediaType()
	{
		return $this->mediaType;
	}

	/**
	 * @param string
	 */
	public function setOwnerId($ownerId)
	{
		$this->ownerId = $ownerId;
	}
	
	public function getOwnerId()
	{
		return $this->ownerId;
	}

	/**
	 * @param string
	 */
	public function setThumbnailUrl($thumbnailUrl)
	{
		$this->thumbnailUrl = $thumbnailUrl;
	}
	
	public function getThumbnailUrl()
	{
		return $this->thumbnailUrl;
	}

	/**
	 * @param string
	 */
	public function setTitle($title)
	{
		$this->title = $title;
	}
	
	public function getTitle()
	{
		return $this->title;
	}
}

