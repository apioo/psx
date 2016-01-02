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

namespace PSX\Http;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface as PsrStreamInterface;
use PSX\Http\Stream;

/**
 * Message
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Message implements MessageInterface
{
    protected $headers;
    protected $body;
    protected $protocol;

    /**
     * @param array $headers
     * @param \Psr\Http\Message\StreamInterface|string|resource $body
     */
    public function __construct(array $headers = array(), $body = null)
    {
        $this->headers = $this->prepareHeaders($headers);
        $this->body    = $this->prepareBody($body);
    }

    public function getProtocolVersion()
    {
        return $this->protocol;
    }

    public function setProtocolVersion($protocol)
    {
        $this->protocol = $protocol;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function setHeaders(array $headers)
    {
        $this->headers = $this->prepareHeaders($headers);
    }

    public function hasHeader($name)
    {
        return array_key_exists(strtolower($name), $this->headers);
    }

    public function getHeader($name)
    {
        $lines = $this->getHeaderLines($name);

        return $lines ? implode(', ', $lines) : null;
    }

    public function getHeaderLines($name)
    {
        $name = strtolower($name);

        if (!$this->hasHeader($name)) {
            return array();
        }

        return $this->headers[$name];
    }

    public function setHeader($name, $value)
    {
        $this->headers[strtolower($name)] = $this->normalizeHeaderValue($value);
    }

    public function addHeader($name, $value)
    {
        $name = strtolower($name);

        if ($this->hasHeader($name)) {
            $this->setHeader($name, array_merge($this->headers[$name], $this->normalizeHeaderValue($value)));
        } else {
            $this->setHeader($name, $value);
        }
    }

    public function removeHeader($name)
    {
        $name = strtolower($name);

        if ($this->hasHeader($name)) {
            unset($this->headers[$name]);
        }
    }

    public function getBody()
    {
        return $this->body;
    }

    public function setBody(PsrStreamInterface $body)
    {
        $this->body = $body;
    }

    protected function prepareHeaders(array $headers)
    {
        return array_map(array($this, 'normalizeHeaderValue'), array_change_key_case($headers));
    }

    protected function normalizeHeaderValue($value)
    {
        return is_array($value) ? array_map('strval', $value) : [(string) $value];
    }

    protected function prepareBody($body)
    {
        if ($body instanceof PsrStreamInterface) {
            return $body;
        } elseif ($body === null) {
            return new Stream\StringStream();
        } elseif (is_string($body)) {
            return new Stream\StringStream($body);
        } elseif (is_resource($body)) {
            return new Stream\TempStream($body);
        } else {
            throw new InvalidArgumentException('Body must be either an PSX\Http\StreamInterface, string or resource');
        }
    }
}
