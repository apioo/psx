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
 * Activity
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Activity extends ObjectType
{
    /**
     * @Type("\PSX\Model\ActivityStream\ObjectType")
     * @Required
     */
    protected $actor;

    /**
     * @Type("string")
     */
    protected $content;

    /**
     * @Type("\PSX\Model\ActivityStream\ObjectType")
     */
    protected $generator;

    /**
     * @Type("\PSX\Model\ActivityStream\MediaLink")
     */
    protected $icon;

    /**
     * @Type("string")
     */
    protected $id;

    /**
     * @Type("choice<\PSX\Model\ActivityStream\ObjectTypeRevealer>")
     */
    protected $object;

    /**
     * @Type("datetime")
     */
    protected $published;

    /**
     * @Type("\PSX\Model\ActivityStream\ObjectType")
     */
    protected $provider;

    /**
     * @Type("\PSX\Model\ActivityStream\ObjectType")
     */
    protected $target;

    /**
     * @Type("string")
     */
    protected $title;

    /**
     * @Type("datetime")
     */
    protected $updated;

    /**
     * @Type("string")
     */
    protected $url;

    /**
     * @Type("string")
     */
    protected $verb;

    public function __construct()
    {
        $this->objectType = 'activity';
    }

    public function getActor()
    {
        return $this->actor;
    }

    public function setActor(ObjectType $actor)
    {
        $this->actor = $actor;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getGenerator()
    {
        return $this->generator;
    }

    public function setGenerator(ObjectType $generator)
    {
        $this->generator = $generator;
    }

    public function getIcon()
    {
        return $this->icon;
    }

    public function setIcon(MediaLink $icon)
    {
        $this->icon = $icon;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getObject()
    {
        return $this->object;
    }

    public function setObject(ObjectType $object)
    {
        $this->object = $object;
    }

    public function getPublished()
    {
        return $this->published;
    }

    public function setPublished(DateTime $published)
    {
        $this->published = $published;
    }

    public function getProvider()
    {
        return $this->provider;
    }

    public function setProvider(ObjectType $provider)
    {
        $this->provider = $provider;
    }

    public function getTarget()
    {
        return $this->target;
    }

    public function setTarget(ObjectType $target)
    {
        $this->target = $target;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getUpdated()
    {
        return $this->updated;
    }

    public function setUpdated(DateTime $updated)
    {
        $this->updated = $updated;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function getVerb()
    {
        return $this->verb;
    }

    public function setVerb($verb)
    {
        $this->verb = $verb;
    }
}
