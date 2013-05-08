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

namespace PSX\OpenSocial\Data;

use PSX\OpenSocial\DataAbstract;

/**
 * MediaItem
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class MediaItem extends DataAbstract
{
	protected $albumId;
	protected $created;
	protected $description;
	protected $duration;
	protected $fileSize;
	protected $id;
	protected $language;
	protected $lastUpdated;
	protected $location;
	protected $mimeType;
	protected $numComments;
	protected $numViews;
	protected $numVotes;
	protected $rating;
	protected $startTime;
	protected $taggedPeople;
	protected $tags;
	protected $thumbnailUrl;
	protected $title;
	protected $type;
	protected $url;

	public function getName()
	{
		return 'mediaItem';
	}

	public function getFields()
	{
		return array(

			'album_id'      => $this->albumId,
			'created'       => $this->created,
			'description'   => $this->description,
			'duration'      => $this->duration,
			'file_size'     => $this->fileSize,
			'id'            => $this->id,
			'language'      => $this->language,
			'last_updated'  => $this->lastUpdated,
			'location'      => $this->location,
			'mime_type'     => $this->mimeType,
			'num_comments'  => $this->numComments,
			'num_views'     => $this->numViews,
			'num_votes'     => $this->numVotes,
			'rating'        => $this->rating,
			'start_time'    => $this->startTime,
			'tagged_people' => $this->taggedPeople,
			'tags'          => $this->tags,
			'thumbnail_url' => $this->thumbnailUrl,
			'title'         => $this->title,
			'type'          => $this->type,
			'url'           => $this->url,

		);
	}

	/**
	 * @param string
	 */
	public function setAlbumId($albumId)
	{
		$this->albumId = $albumId;
	}
	
	public function getAlbumId()
	{
		return $this->albumId;
	}

	/**
	 * @param string
	 */
	public function setCreated($created)
	{
		$this->created = $created;
	}
	
	public function getCreated()
	{
		return $this->created;
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
	 * @param integer
	 */
	public function setDuration($duration)
	{
		$this->duration = $duration;
	}
	
	public function getDuration()
	{
		return $this->duration;
	}

	/**
	 * @param integer
	 */
	public function setFileSize($fileSize)
	{
		$this->fileSize = $fileSize;
	}
	
	public function getFileSize()
	{
		return $this->fileSize;
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
	 * @param string
	 */
	public function setLanguage($language)
	{
		$this->language = $language;
	}
	
	public function getLanguage()
	{
		return $this->language;
	}

	/**
	 * @param string
	 */
	public function setLastUpdated($lastUpdated)
	{
		$this->lastUpdated = $lastUpdated;
	}
	
	public function getLastUpdated()
	{
		return $this->lastUpdated;
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
	 * @param string
	 */
	public function setMimeType($mimeType)
	{
		$this->mimeType = $mimeType;
	}
	
	public function getMimeType()
	{
		return $this->mimeType;
	}

	/**
	 * @param integer
	 */
	public function setNumComments($numComments)
	{
		$this->numComments = $numComments;
	}
	
	public function getNumComments()
	{
		return $this->numComments;
	}

	/**
	 * @param integer
	 */
	public function setNumViews($numViews)
	{
		$this->numViews = $numViews;
	}
	
	public function getNumViews()
	{
		return $this->numViews;
	}

	/**
	 * @param integer
	 */
	public function setNumVotes($numVotes)
	{
		$this->numVotes = $numVotes;
	}
	
	public function getNumVotes()
	{
		return $this->numVotes;
	}

	/**
	 * @param integer
	 */
	public function setRatinge($rating)
	{
		$this->rating = $rating;
	}
	
	public function getRatinge()
	{
		return $this->rating;
	}

	/**
	 * @param string
	 */
	public function setStartTime($startTime)
	{
		$this->startTime = $startTime;
	}
	
	public function getStartTime()
	{
		return $this->startTime;
	}

	/**
	 * @param array
	 */
	public function setTaggedPeople(array $taggedPeople)
	{
		$this->taggedPeople = $taggedPeople;
	}
	
	public function getTaggedPeople()
	{
		return $this->taggedPeople;
	}

	/**
	 * @param array
	 */
	public function setTags(array $tags)
	{
		$this->tags = $tags;
	}
	
	public function getTags()
	{
		return $this->tags;
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

	/**
	 * @param string
	 */
	public function setType($type)
	{
		$this->type = $type;
	}
	
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @param string
	 */
	public function setUrl($url)
	{
		$this->url = $url;
	}
	
	public function getUrl()
	{
		return $this->url;
	}
}

