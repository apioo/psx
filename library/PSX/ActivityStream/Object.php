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

namespace PSX\ActivityStream;

use DateTime;
use PSX\Data\RecordAbstract;

/**
 * Object
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Object extends RecordAbstract
{
	protected $attachments;
	protected $author;
	protected $content;
	protected $displayName;
	protected $downstreamDuplicates;
	protected $id;
	protected $image;
	protected $objectType;
	protected $published;
	protected $summary;
	protected $updated;
	protected $upstreamDuplicates;
	protected $url;

	public function getName()
	{
		return 'object';
	}

	public function getFields()
	{
		return array(

			'attachments' => $this->attachments,
			'author'      => $this->author,
			'content'     => $this->content,
			'displayName' => $this->displayName,
			'downstreamDuplicates' => $this->downstreamDuplicates,
			'id'          => $this->id,
			'image'       => $this->image,
			'objectType'  => $this->objectType,
			'published'   => $this->published !== null ? $this->published->format(DateTime::RFC3339) : null,
			'summary'     => $this->summary,
			'updated'     => $this->updated !== null ? $this->updated->format(DateTime::RFC3339) : null,
			'upstreamDuplicates' => $this->upstreamDuplicates,
			'url'         => $this->url,

		);
	}

	/**
	 * @param array<PSX\ActivityStream\ObjectFactory>
	 */
	public function setAttachments(array $attachments)
	{
		$this->attachments = $attachments;
	}

	/**
	 * @param PSX\ActivityStream\ObjectFactory
	 */
	public function setAuthor($author)
	{
		$this->author = $author;
	}

	public function setContent($content)
	{
		$this->content = $content;
	}

	public function setDisplayName($displayName)
	{
		$this->displayName = $displayName;
	}

	/**
	 * @param array
	 */
	public function setDownstreamDuplicates($downstreamDuplicates)
	{
		$this->downstreamDuplicates = $downstreamDuplicates;
	}

	public function setId($id)
	{
		$this->id = $id;
	}

	/**
	 * @param PSX\ActivityStream\MediaLink
	 */
	public function setImage(MediaLink $image)
	{
		$this->image = $image;
	}

	public function setObjectType($objectType)
	{
		$this->objectType = $objectType;
	}

	/**
	 * @param DateTime
	 */
	public function setPublished(DateTime $published)
	{
		$this->published = $published;
	}

	public function setSummary($summary)
	{
		$this->summary = $summary;
	}

	/**
	 * @param DateTime
	 */
	public function setUpdated(DateTime $updated)
	{
		$this->updated = $updated;
	}

	/**
	 * @param array
	 */
	public function setUpstreamDuplicates($upstreamDuplicates)
	{
		$this->upstreamDuplicates = $upstreamDuplicates;
	}

	public function setUrl($url)
	{
		$this->url = $url;
	}
}

