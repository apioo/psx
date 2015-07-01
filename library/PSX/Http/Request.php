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

namespace PSX\Http;

use PSX\Exception;
use PSX\Http;
use PSX\Uri;

/**
 * Request
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Request extends Message implements RequestInterface
{
    protected $requestTarget;
    protected $method;
    protected $uri;
    protected $attributes;

    /**
     * @param \PSX\Uri $uri
     * @param string $method
     * @param array $headers
     * @param string $body
     */
    public function __construct(Uri $uri, $method, array $headers = array(), $body = null)
    {
        parent::__construct($headers, $body);

        $this->uri    = $uri;
        $this->method = $method;
    }

    /**
     * Returns the request target
     *
     * @return string
     */
    public function getRequestTarget()
    {
        if ($this->requestTarget !== null) {
            return $this->requestTarget;
        }

        $target = $this->uri->getPath();
        if (empty($target)) {
            $target = '/';
        }

        $query = $this->uri->getQuery();
        if (!empty($query)) {
            $target.= '?' . $query;
        }

        return $target;
    }

    /**
     * Sets the request target
     *
     * @param string $requestTarget
     */
    public function setRequestTarget($requestTarget)
    {
        $this->requestTarget = $requestTarget;
    }

    /**
     * Returns the request method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Sets the request method
     *
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * Returns the request uri
     *
     * @return \PSX\Uri
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Sets the request uri
     *
     * @param \PSX\Uri $uri
     */
    public function setUri(Uri $uri)
    {
        $this->uri = $uri;
    }

    /**
     * Converts the request object to an http request string
     *
     * @return string
     */
    public function toString()
    {
        $request = RequestParser::buildStatusLine($this) . Http::$newLine;
        $headers = RequestParser::buildHeaderFromMessage($this);

        foreach ($headers as $header) {
            $request.= $header . Http::$newLine;
        }

        $request.= Http::$newLine;
        $request.= (string) $this->getBody();

        return $request;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function getAttribute($name)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
    }

    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    public function removeAttribute($name)
    {
        if (isset($this->attributes[$name])) {
            unset($this->attributes[$name]);
        }
    }

    public function __toString()
    {
        return $this->toString();
    }
}
