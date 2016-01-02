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

namespace PSX\Swagger;

use PSX\Data\RecordAbstract;

/**
 * Model
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Model extends RecordAbstract
{
    protected $id;
    protected $description;
    protected $required;
    protected $discriminator;
    protected $properties;
    protected $subTypes;

    public function __construct($id = null, $description = null, array $required = null)
    {
        $this->id          = $id;
        $this->description = $description;
        $this->required    = $required;
        $this->properties  = new \stdClass();
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setRequired($required)
    {
        $this->required = $required;
    }

    public function getRequired()
    {
        return $this->required;
    }

    public function setDiscriminator($discriminator)
    {
        $this->discriminator = $discriminator;
    }
    
    public function getDiscriminator()
    {
        return $this->discriminator;
    }

    public function setProperties($properties)
    {
        $this->properties = $properties;
    }
    
    public function getProperties()
    {
        return $this->properties;
    }

    public function addProperty(Property $property)
    {
        $this->properties->{$property->getId()} = $property;
    }

    public function setSubTypes($subTypes)
    {
        $this->subTypes = $subTypes;
    }
    
    public function getSubTypes()
    {
        return $this->subTypes;
    }

    public function addSubType($subType)
    {
        $this->subTypes[] = $subType;
    }
}
