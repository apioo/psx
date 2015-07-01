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

namespace PSX\ActivityStream;

use DateTime;
use PSX\ActivityStream\ObjectType\Collection;

/**
 * AdditionalObjectPropertiesTrait
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
trait AdditionalObjectPropertiesTrait
{
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
    protected $validFrom;
    protected $validAfter;
    protected $validUntil;
    protected $validBefore;
    protected $rating;
    protected $tags;
    protected $title;
    protected $duration;
    protected $height;
    protected $width;
    protected $inReplyTo;
    protected $scope;
    protected $replies;

    public function setAlias($alias)
    {
        $this->alias = $alias;
    }
    
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param \PSX\ActivityStream\ObjectFactory $attachments
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
     * @param \PSX\ActivityStream\ObjectFactory $author
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
     * @param \PSX\ActivityStream\Language $content
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
     * @param \PSX\ActivityStream\ObjectFactory $duplicates
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
     * @param \PSX\ActivityStream\ObjectFactory $icon
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
     * @param \PSX\ActivityStream\ObjectFactory $image
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
     * @param \PSX\ActivityStream\ObjectFactory $location
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
     * @param \PSX\DateTime $published
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
     * @param \PSX\ActivityStream\ObjectFactory $generator
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
     * @param \PSX\ActivityStream\ObjectFactory $provider
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
     * @param \PSX\ActivityStream\Language $summary
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
     * @param \PSX\DateTime $updated
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
     * @param \PSX\DateTime $startTime
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
     * @param \PSX\DateTime $endTime
     */
    public function setEndTime(DateTime $endTime)
    {
        $this->endTime = $endTime;
    }
    
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * @param \PSX\DateTime $validFrom
     */
    public function setValidFrom(DateTime $validFrom)
    {
        $this->validFrom = $validFrom;
    }
    
    public function getValidFrom()
    {
        return $this->validFrom;
    }

    /**
     * @param \PSX\DateTime $validAfter
     */
    public function setValidAfter(DateTime $validAfter)
    {
        $this->validAfter = $validAfter;
    }
    
    public function getValidAfter()
    {
        return $this->validAfter;
    }

    /**
     * @param \PSX\DateTime $validUntil
     */
    public function setValidUntil(DateTime $validUntil)
    {
        $this->validUntil = $validUntil;
    }
    
    public function getValidUntil()
    {
        return $this->validUntil;
    }

    /**
     * @param \PSX\DateTime $validBefore
     */
    public function setValidBefore(DateTime $validBefore)
    {
        $this->validBefore = $validBefore;
    }
    
    public function getValidBefore()
    {
        return $this->validBefore;
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
     * @param \PSX\ActivityStream\ObjectFactory $tags
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
     * @param \PSX\ActivityStream\Language $title
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
     * @param \PSX\ActivityStream\ObjectFactory $inReplyTo
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
     * @param \PSX\ActivityStream\ObjectFactory $scope
     */
    public function setScope($scope)
    {
        $this->scope = $scope;
    }
    
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * @param \PSX\ActivityStream\ObjectType\Collection $replies
     */
    public function setReplies(Collection $replies)
    {
        $this->replies = $replies;
    }
    
    public function getReplies()
    {
        return $this->replies;
    }
}
