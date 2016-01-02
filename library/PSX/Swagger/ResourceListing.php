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
 * ResourceListing
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ResourceListing extends RecordAbstract
{
    protected $swaggerVersion;
    protected $apiVersion;
    protected $info;
    protected $authorizations;
    protected $apis = array();

    public function __construct($apiVersion = null)
    {
        $this->swaggerVersion = Swagger::VERSION;
        $this->apiVersion     = $apiVersion;
    }

    public function setApiVersion($apiVersion)
    {
        $this->apiVersion = $apiVersion;
    }
    
    public function getApiVersion()
    {
        return $this->apiVersion;
    }

    /**
     * @param \PSX\Swagger\InfoObject $info
     */
    public function setInfo(InfoObject $info)
    {
        $this->info = $info;
    }

    public function getInfo()
    {
        return $this->info;
    }

    /**
     * @param \PSX\Swagger\ResourceObject[] $apis
     */
    public function setApis(array $apis)
    {
        $this->apis = $apis;
    }
    
    public function getApis()
    {
        return $this->apis;
    }

    public function addResource(ResourceObject $resourceObject)
    {
        $this->apis[] = $resourceObject;
    }
}
