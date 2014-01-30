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
	protected $id;
	protected $objectType;
	protected $language;
	protected $displayName;
	protected $url;
	protected $alias;
	protected $attachments;
	protected $author;
	protected $content;
	protected $duplicates;
	protected $icon;
	protected $image;
	protected $location;
	protected $published;
	protected $generator;
	protected $provider;
	protected $summary;
	protected $updated;
	protected $startTime;
	protected $endTime;
	protected $rating;
	protected $tags;
	protected $title;
	protected $duration;
	protected $height;
	protected $width;
	protected $inReplyTo;
	protected $actions;
	protected $scope;

	public function setId($id)
	{
		$this->id = $id;
	}

	public function getId()
	{
		return $this->id;
	}

	public function setObjectType($objectType)
	{
		$this->objectType = $objectType;
	}

	public function getObjectType()
	{
		return $this->objectType;
	}

	public function setLanguage($language)
	{
		$this->language = $language;
	}
	
	public function getLanguage()
	{
		return $this->language;
	}

	public function setDisplayName($displayName)
	{
		$this->displayName = $displayName;
	}
	
	public function getDisplayName()
	{
		return $this->displayName;
	}

	public function setUrl($url)
	{
		$this->url = $url;
	}
	
	public function getUrl()
	{
		return $this->url;
	}

	public function setAlias($alias)
	{
		$this->alias = $alias;
	}
	
	public function getAlias()
	{
		return $this->alias;
	}

	/**
	 * @param PSX\ActivityStream\LinkBuilder $attachments
	 */
	public function setAttachments($attachments)
	{
		$this->attachments = $attachments;
	}
	
	public function getAttachments()
	{
		return $this->attachments;
	}

	/**
	 * @param PSX\ActivityStream\LinkBuilder $author
	 */
	public function setAuthor($author)
	{
		$this->author = $author;
	}
	
	public function getAuthor()
	{
		return $this->author;
	}

	/**
	 * @param PSX\ActivityStream\Language $content
	 */
	public function setContent($content)
	{
		$this->content = $content;
	}
	
	public function getContent()
	{
		return $this->content;
	}

	/**
	 * @param PSX\ActivityStream\LinkBuilder $duplicates
	 */
	public function setDuplicates($duplicates)
	{
		$this->duplicates = $duplicates;
	}
	
	public function getDuplicates()
	{
		return $this->duplicates;
	}

	/**
	 * @param PSX\ActivityStream\LinkBuilder $icon
	 */
	public function setIcon($icon)
	{
		$this->icon = $icon;
	}
	
	public function getIcon()
	{
		return $this->icon;
	}

	/**
	 * @param PSX\ActivityStream\LinkBuilder $image
	 */
	public function setImage($image)
	{
		$this->image = $image;
	}
	
	public function getImage()
	{
		return $this->image;
	}

	/**
	 * @param PSX\ActivityStream\LinkBuilder $location
	 */
	public function setLocation($location)
	{
		$this->location = $location;
	}
	
	public function getLocation()
	{
		return $this->location;
	}

	/**
	 * @param DateTime $published
	 */
	public function setPublished(DateTime $published)
	{
		$this->published = $published;
	}
	
	public function getPublished()
	{
		return $this->published;
	}

	/**
	 * @param PSX\ActivityStream\LinkBuilder $generator
	 */
	public function setGenerator($generator)
	{
		$this->generator = $generator;
	}
	
	public function getGenerator()
	{
		return $this->generator;
	}

	/**
	 * @param PSX\ActivityStream\LinkBuilder $provider
	 */
	public function setProvider($provider)
	{
		$this->provider = $provider;
	}
	
	public function getProvider()
	{
		return $this->provider;
	}

	/**
	 * @param PSX\ActivityStream\Language $summary
	 */
	public function setSummary($summary)
	{
		$this->summary = $summary;
	}
	
	public function getSummary()
	{
		return $this->summary;
	}

	/**
	 * @param DateTime $updated
	 */
	public function setUpdated(DateTime $updated)
	{
		$this->updated = $updated;
	}
	
	public function getUpdated()
	{
		return $this->updated;
	}

	/**
	 * @param DateTime $startTime
	 */
	public function setStartTime(DateTime $startTime)
	{
		$this->startTime = $startTime;
	}
	
	public function getStartTime()
	{
		return $this->startTime;
	}

	/**
	 * @param DateTime $endTime
	 */
	public function setEndTime(DateTime $endTime)
	{
		$this->endTime = $endTime;
	}
	
	public function getEndTime()
	{
		return $this->endTime;
	}

	public function setRating($rating)
	{
		$this->rating = $rating;
	}
	
	public function getRating()
	{
		return $this->rating;
	}

	/**
	 * @param PSX\ActivityStream\LinkBuilder $tags
	 */
	public function setTags($tags)
	{
		$this->tags = $tags;
	}
	
	public function getTags()
	{
		return $this->tags;
	}

	/**
	 * @param PSX\ActivityStream\Language $title
	 */
	public function setTitle($title)
	{
		$this->title = $title;
	}
	
	public function getTitle()
	{
		return $this->title;
	}

	public function setDuration($duration)
	{
		$this->duration = $duration;
	}
	
	public function getDuration()
	{
		return $this->duration;
	}

	/**
	 * @param integer $height
	 */
	public function setHeight($height)
	{
		$this->height = $height;
	}
	
	public function getHeight()
	{
		return $this->height;
	}

	/**
	 * @param integer $width
	 */
	public function setWidth($width)
	{
		$this->width = $width;
	}
	
	public function getWidth()
	{
		return $this->width;
	}

	/**
	 * @param PSX\ActivityStream\LinkBuilder $inReplyTo
	 */
	public function setInReplyTo($inReplyTo)
	{
		$this->inReplyTo = $inReplyTo;
	}
	
	public function getInReplyTo()
	{
		return $this->inReplyTo;
	}

	/**
	 * @param PSX\ActivityStream\ActionBuilder $actions
	 */
	public function setActions(array $actions)
	{
		$this->actions = $actions;
	}

	public function getActions()
	{
		return $this->actions;
	}

	public function addAction($key, $action)
	{
		$this->actions[$key] = $action;
	}

	public function getAction($key)
	{
		return isset($this->actions[$key]) ? $this->actions[$key] : null;
	}

	public function removeAction($key)
	{
		unset($this->actions[$key]);
	}

	/**
	 * @param PSX\ActivityStream\LinkBuilder $scope
	 */
	public function setScope($scope)
	{
		$this->scope = $scope;
	}
	
	public function getScope()
	{
		return $this->scope;
	}
}

