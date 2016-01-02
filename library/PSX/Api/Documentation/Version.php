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

namespace PSX\Api\Documentation;

use PSX\Api\DocumentationInterface;
use PSX\Api\Resource;

/**
 * Version
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Version implements DocumentationInterface
{
    protected $resources = array();
    protected $description;

    public function __construct($description = null)
    {
        $this->description = $description;
    }

    public function addResource($version, Resource $view)
    {
        $this->resources[$version] = $view;
    }

    public function hasResource($version)
    {
        return isset($this->resources[$version]);
    }

    /**
     * @param integer $version
     * @return \PSX\Api\Resource
     */
    public function getResource($version)
    {
        return isset($this->resources[$version]) ? $this->resources[$version] : null;
    }

    public function getResources()
    {
        return $this->resources;
    }

    public function getLatestVersion()
    {
        if (count($this->resources) > 0) {
            return max(array_keys($this->resources));
        } else {
            return 1;
        }
    }

    public function isVersionRequired()
    {
        return true;
    }

    public function getDescription()
    {
        return $this->description;
    }
}
