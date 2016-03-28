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

namespace PSX\Model\ActivityStream\ObjectType;

use DateTime;
use PSX\Model\ActivityStream\ObjectType;

/**
 * Task
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Task extends ObjectType
{
    /**
     * @Type("\PSX\Model\ActivityStream\ObjectType")
     */
    protected $actor;

    /**
     * @Type("datetime")
     */
    protected $by;

    /**
     * @Type("choice<\PSX\Model\ActivityStream\ObjectTypeRevealer>")
     */
    protected $object;

    /**
     * @Type("array<\PSX\Model\ActivityStream\ObjectType\Task>")
     */
    protected $prerequisites;

    /**
     * @Type("boolean")
     */
    protected $required;

    /**
     * @Type("array<\PSX\Model\ActivityStream\ObjectType\Task>")
     */
    protected $supersedes;

    /**
     * @Type("string")
     */
    protected $verb;

    public function __construct()
    {
        $this->objectType = 'task';
    }

    public function setActor(ObjectType $actor)
    {
        $this->actor = $actor;
    }
    
    public function getActor()
    {
        return $this->actor;
    }

    public function setBy(DateTime $by)
    {
        $this->by = $by;
    }
    
    public function getBy()
    {
        return $this->by;
    }

    public function setObject(ObjectType $object)
    {
        $this->object = $object;
    }
    
    public function getObject()
    {
        return $this->object;
    }

    public function setPrerequisites(array $prerequisites)
    {
        $this->prerequisites = $prerequisites;
    }
    
    public function getPrerequisites()
    {
        return $this->prerequisites;
    }

    public function setRequired($required)
    {
        $this->required = $required;
    }
    
    public function getRequired()
    {
        return $this->required;
    }

    public function setSupersedes(array $supersedes)
    {
        $this->supersedes = $supersedes;
    }
    
    public function getSupersedes()
    {
        return $this->supersedes;
    }

    public function setVerb($verb)
    {
        $this->verb = $verb;
    }
    
    public function getVerb()
    {
        return $this->verb;
    }
}
