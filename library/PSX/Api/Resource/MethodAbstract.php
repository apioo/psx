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

namespace PSX\Api\Resource;

use PSX\Schema\Property;
use PSX\Schema\PropertySimpleAbstract;
use PSX\Schema\Schema;
use PSX\Schema\SchemaInterface;
use RuntimeException;

/**
 * MethodAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link	http://phpsx.org
 */
abstract class MethodAbstract
{
    /**
     * @var string
     */
    protected $description;

    /**
     * @var \PSX\Schema\Property\ComplexType
     */
    protected $queryParameters;

    /**
     * @var \PSX\Schema\SchemaInterface
     */
    protected $request;

    /**
     * @var array
     */
    protected $responses;

    public function __construct()
    {
        $this->queryParameters = Property::getComplex('query')->setAdditionalProperties(true);
        $this->responses       = array();
    }

    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function addQueryParameter(PropertySimpleAbstract $property)
    {
        $this->queryParameters->add($property);

        return $this;
    }

    public function getQueryParameters()
    {
        return new Schema($this->queryParameters);
    }

    public function hasQueryParameters()
    {
        return count($this->queryParameters) > 0;
    }

    public function setRequest(SchemaInterface $schema)
    {
        $this->request = $schema;

        return $this;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function hasRequest()
    {
        return $this->request instanceof SchemaInterface;
    }

    public function addResponse($statusCode, SchemaInterface $schema)
    {
        $this->responses[$statusCode] = $schema;

        return $this;
    }

    public function getResponses()
    {
        return $this->responses;
    }

    public function getResponse($statusCode)
    {
        if (isset($this->responses[$statusCode])) {
            return $this->responses[$statusCode];
        } else {
            throw new RuntimeException('Status code response ' . $statusCode . ' is not available for this resource');
        }
    }

    public function hasResponse($statusCode)
    {
        return isset($this->responses[$statusCode]);
    }

    /**
     * Returns the uppercase name of the method
     *
     * @return string
     */
    abstract public function getName();
}
