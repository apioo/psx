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

namespace PSX\Api\Resource\Generator\Wsdl;

/**
 * Operation
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Operation
{
    protected $name;
    protected $path;
    protected $method;
    protected $in;
    protected $out;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setMethod($method)
    {
        $this->method = $method;
    }
    
    public function getMethod()
    {
        return $this->method;
    }

    public function setIn($in)
    {
        $this->in = $in;
    }

    public function getIn()
    {
        return $this->in;
    }

    public function hasIn()
    {
        return !empty($this->in);
    }

    public function setOut($out)
    {
        $this->out = $out;
    }
    
    public function getOut()
    {
        return $this->out;
    }

    public function hasOut()
    {
        return !empty($this->out);
    }

    public function hasOperation()
    {
        return !empty($this->in) || !empty($this->out);
    }

    public function isInOnly()
    {
        return !empty($this->in) && empty($this->out);
    }

    public function isOutOnly()
    {
        return empty($this->in) && !empty($this->out);
    }

    public function isInOut()
    {
        return !empty($this->in) && !empty($this->out);
    }
}
