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

namespace PSX\Model\Rss;

/**
 * Cloud
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Cloud
{
    /**
     * @Type("string")
     */
    protected $domain;

    /**
     * @Type("integer")
     */
    protected $port;

    /**
     * @Type("string")
     */
    protected $path;

    /**
     * @Type("string")
     */
    protected $registerProcedure;

    /**
     * @Type("string")
     */
    protected $protocol;

    public function __construct($domain = null, $port = null, $path = null, $registerProcedure = null, $protocol = null)
    {
        if ($domain !== null) {
            $this->setDomain($domain);
        }

        if ($port !== null) {
            $this->setPort($port);
        }

        if ($path !== null) {
            $this->setPath($path);
        }

        if ($registerProcedure !== null) {
            $this->setRegisterProcedure($registerProcedure);
        }

        if ($protocol !== null) {
            $this->setProtocol($protocol);
        }
    }

    public function setDomain($domain)
    {
        $this->domain = $domain;
    }
    
    public function getDomain()
    {
        return $this->domain;
    }

    public function setPort($port)
    {
        $this->port = $port;
    }
    
    public function getPort()
    {
        return $this->port;
    }

    public function setPath($path)
    {
        $this->path = $path;
    }
    
    public function getPath()
    {
        return $this->path;
    }

    public function setRegisterProcedure($registerProcedure)
    {
        $this->registerProcedure = $registerProcedure;
    }
    
    public function getRegisterProcedure()
    {
        return $this->registerProcedure;
    }

    public function setProtocol($protocol)
    {
        $this->protocol = $protocol;
    }
    
    public function getProtocol()
    {
        return $this->protocol;
    }
}
