<?php
/*
 *  $Id: MediaItem.php 480 2012-05-01 18:13:54Z k42b3.x@googlemail.com $
 *
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2012 Christoph Kappestein <k42b3.x@gmail.com>
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

/**
 * PSX_OpenSocial_Type_MediaItem
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_OpenSocial
 * @version    $Revision: 480 $
 */
class PSX_OpenSocial_Type_MediaItem extends PSX_OpenSocial_TypeAbstract
{
	public $albumId;
	public $created;
	public $description;
	public $duration;
	public $fileSize;
	public $id;
	public $language;
	public $lastUpdated;
	public $location;
	public $mimeType;
	public $numComments;
	public $numViews;
	public $numVotes;
	public $rating;
	public $startTime;
	public $taggedPeople;
	public $tags;
	public $thumbnailUrl;
	public $title;
	public $type;
	public $url;

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
			'tagged_people' => $this->taggedPeople,
			'tags'          => $this->tags,
			'thumbnail_url' => $this->thumbnailUrl,
			'title'         => $this->title,
			'type'          => $this->type,
			'url'           => $this->url,

		);
	}
}

