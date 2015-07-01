<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
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

use InvalidArgumentException;
use PSX\Data\RecordAbstract;

/**
 * Operation
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Operation extends RecordAbstract
{
    protected $method;
    protected $nickname;
    protected $summary;
    protected $notes;
    protected $parameters       = array();
    protected $responseMessages = array();

    public function __construct($method = null, $nickname = null, $summary = null)
    {
        $this->nickname = $nickname;
        $this->summary  = $summary;

        if ($method !== null) {
            $this->setMethod($method);
        }
    }

    public function setMethod($method)
    {
        if (!in_array($method, array('GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'))) {
            throw new InvalidArgumentException('Invalid method');
        }

        $this->method = $method;
    }
    
    public function getMethod()
    {
        return $this->method;
    }

    public function setNickname($nickname)
    {
        $this->nickname = $nickname;
    }
    
    public function getNickname()
    {
        return $this->nickname;
    }

    public function setSummary($summary)
    {
        $this->summary = $summary;
    }
    
    public function getSummary()
    {
        return $this->summary;
    }

    public function setNotes($notes)
    {
        $this->notes = $notes;
    }
    
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @param \PSX\Swagger\ResponseMessage[] $responseMessages
     */
    public function setResponseMessages($responseMessages)
    {
        $this->responseMessages = $responseMessages;
    }
    
    public function getResponseMessages()
    {
        return $this->responseMessages;
    }

    public function addResponseMessage(ResponseMessage $responseMessage)
    {
        $this->responseMessages[] = $responseMessage;
    }

    /**
     * @param \PSX\Swagger\Parameter[] $parameters
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }
    
    public function getParameters()
    {
        return $this->parameters;
    }

    public function addParameter(Parameter $parameter)
    {
        $this->parameters[] = $parameter;
    }
}
