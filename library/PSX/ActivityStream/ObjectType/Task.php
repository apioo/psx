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

namespace PSX\ActivityStream\ObjectType;

use PSX\ActivityStream\ObjectType;

/**
 * Task
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Task extends ObjectType
{
    protected $actor;
    protected $by;
    protected $object;
    protected $prerequisites;
    protected $required;
    protected $supersedes;
    protected $verb;

    public function __construct()
    {
        $this->objectType = 'task';
    }

    /**
     * @param \PSX\ActivityStream\ObjectFactory $actor
     */
    public function setActor($actor)
    {
        $this->actor = $actor;
    }
    
    public function getActor()
    {
        return $this->actor;
    }

    public function setBy($by)
    {
        $this->by = $by;
    }
    
    public function getBy()
    {
        return $this->by;
    }

    /**
     * @param \PSX\ActivityStream\ObjectFactory $object
     */
    public function setObject($object)
    {
        $this->object = $object;
    }
    
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @param \PSX\ActivityStream\ObjectFactory $prerequisites
     */
    public function setPrerequisites($prerequisites)
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

    /**
     * @param \PSX\ActivityStream\ObjectFactory $supersedes
     */
    public function setSupersedes($supersedes)
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
