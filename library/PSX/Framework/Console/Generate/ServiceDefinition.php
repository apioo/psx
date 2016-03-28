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

namespace PSX\Framework\Console\Generate;

/**
 * ServiceDefinition
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ServiceDefinition
{
    protected $namespace;
    protected $className;
    protected $services;
    protected $isDryRun;

    public function __construct($namespace, $className, $isDryRun)
    {
        $this->namespace = $namespace;
        $this->className = $className;
        $this->isDryRun  = $isDryRun;
    }

    public function getNamespace()
    {
        return $this->namespace;
    }

    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

    public function getClassName()
    {
        return $this->className;
    }

    public function setClassName($className)
    {
        $this->className = $className;
    }

    public function getServices()
    {
        return $this->services;
    }

    public function setServices(array $services)
    {
        $this->services = $services;
    }

    public function isDryRun()
    {
        return $this->isDryRun;
    }

    public function getPath()
    {
        return PSX_PATH_LIBRARY . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $this->namespace);
    }

    public function getClass()
    {
        return $this->namespace . '\\' . $this->className;
    }
}
