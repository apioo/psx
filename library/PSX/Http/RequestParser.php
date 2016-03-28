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

use PSX\Http\Stream\StringStream;
use PSX\Uri\Uri;
use PSX\Uri\UriResolver;
use PSX\Uri\Url;

/**
 * RequestParser
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class RequestParser extends ParserAbstract
{
    protected $baseUrl;

    public function __construct(Url $baseUrl = null, $mode = self::MODE_STRICT)
    {
        parent::__construct($mode);

        $this->baseUrl = $baseUrl;
    }

    /**
     * Converts an raw http request into an PSX\Http\Request object
     *
     * @param string $content
     * @return \PSX\Http\Request
     */
    public function parse($content)
    {
        $content = $this->normalize($content);

        list($method, $path, $scheme) = $this->getStatus($content);

        // resolve uri path
        if ($this->baseUrl !== null) {
            $path = UriResolver::resolve($this->baseUrl, new Uri($path));
        } else {
            $path = new Uri($path);
        }

        $request = new Request($path, $method);
        $request->setProtocolVersion($scheme);

        list($header, $body) = $this->splitMessage($content);

        $this->headerToArray($request, $header);

        $request->setBody(new StringStream($body));

        return $request;
    }

    protected function getStatus($request)
    {
        $line = $this->getStatusLine($request);

        if ($line !== false) {
            $parts = explode(' ', $line, 3);

            if (isset($parts[0]) && isset($parts[1]) && isset($parts[2])) {
                $method = $parts[0];
                $path   = $parts[1];
                $scheme = $parts[2];

                return array($method, $path, $scheme);
            } else {
                throw new ParseException('Invalid status line format');
            }
        } else {
            throw new ParseException('Couldnt find status line');
        }
    }

    /**
     * @param \PSX\Http\RequestInterface $request
     * @return string
     */
    public static function buildStatusLine(RequestInterface $request)
    {
        $method   = $request->getMethod();
        $target   = $request->getRequestTarget();
        $protocol = $request->getProtocolVersion();

        if (empty($target)) {
            throw new \RuntimeException('Target not set');
        }

        $method   = !empty($method) ? $method : 'GET';
        $protocol = !empty($protocol) ? $protocol : 'HTTP/1.1';

        return $method . ' ' . $target . ' ' . $protocol;
    }

    /**
     * Parses an raw http request into an PSX\Http\Request object. Throws an
     * exception if the request has not an valid format
     *
     * @param string $content
     * @param \PSX\Uri\Url $baseUrl
     * @param integer $mode
     * @return \PSX\Http\Request
     */
    public static function convert($content, Url $baseUrl = null, $mode = ParserAbstract::MODE_STRICT)
    {
        $parser = new self($baseUrl, $mode);

        return $parser->parse($content);
    }
}
