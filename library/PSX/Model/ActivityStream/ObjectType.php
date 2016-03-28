<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2016 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Model\ActivityStream;

use DateTime;

/**
 * ObjectType
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ObjectType
{
    /**
     * @Type("array<\PSX\Model\ActivityStream\ObjectType>")
     */
    protected $attachments;

    /**
     * @Type("\PSX\Model\ActivityStream\ObjectType")
     */
    protected $author;

    /**
     * @Type("string")
     */
    protected $content;

    /**
     * @Type("string")
     */
    protected $displayName;

    /**
     * @Type("array<string>")
     */
    protected $downstreamDuplicates;

    /**
     * @Type("string")
     */
    protected $id;

    /**
     * @Type("string")
     */
    protected $image;

    /**
     * @Type("string")
     */
    protected $objectType;

    /**
     * @Type("datetime")
     */
    protected $published;

    /**
     * @Type("string")
     */
    protected $summary;

    /**
     * @Type("datetime")
     */
    protected $updated;

    /**
     * @Type("array<string>")
     */
    protected $upstreamDuplicates;

    /**
     * @Type("string")
     */
    protected $url;

    public function getAttachments()
    {
        return $this->attachments;
    }

    public function setAttachments(array $attachments)
    {
        $this->attachments = $attachments;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function setAuthor(ObjectType $author)
    {
        $this->author = $author;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getDisplayName()
    {
        return $this->displayName;
    }

    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
    }

    public function getDownstreamDuplicates()
    {
        return $this->downstreamDuplicates;
    }

    public function setDownstreamDuplicates(array $downstreamDuplicates)
    {
        $this->downstreamDuplicates = $downstreamDuplicates;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image)
    {
        $this->image = $image;
    }

    public function getObjectType()
    {
        return $this->objectType;
    }

    public function setObjectType($objectType)
    {
        $this->objectType = $objectType;
    }

    public function getPublished()
    {
        return $this->published;
    }

    public function setPublished(DateTime $published)
    {
        $this->published = $published;
    }

    public function getSummary()
    {
        return $this->summary;
    }

    public function setSummary($summary)
    {
        $this->summary = $summary;
    }

    public function getUpdated()
    {
        return $this->updated;
    }

    public function setUpdated(DateTime $updated)
    {
        $this->updated = $updated;
    }

    public function getUpstreamDuplicates()
    {
        return $this->upstreamDuplicates;
    }

    public function setUpstreamDuplicates(array $upstreamDuplicates)
    {
        $this->upstreamDuplicates = $upstreamDuplicates;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }
}
