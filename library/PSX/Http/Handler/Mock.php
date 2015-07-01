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

namespace PSX\Http\Handler;

use Closure;
use PSX\Exception;
use PSX\Http;
use PSX\Http\HandlerInterface;
use PSX\Http\Options;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseParser;
use PSX\Url;

/**
 * Mock handler where you can register urls wich return a specific response on
 * request
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Mock implements HandlerInterface
{
    protected $resources;

    public function __construct()
    {
        $this->resources = array();
    }

    public function getResources()
    {
        return $this->resources;
    }

    public function add($method, $url, Closure $handler)
    {
        if (!in_array($method, array('GET', 'POST', 'PUT', 'DELETE'))) {
            throw new Exception('Invalid http request method');
        }

        foreach ($this->resources as $resource) {
            if ($resource['method'] == $method && $resource['url'] == $url) {
                throw new Exception('Resource already exists');
            }
        }

        $this->resources[] = array(
            'method'  => $method,
            'url'     => $url,
            'handler' => $handler,
        );
    }

    public function request(RequestInterface $request, Options $options)
    {
        $url = $request->getUri();

        foreach ($this->resources as $resource) {
            $resourceUrl = new Url($resource['url']);

            if ($resource['method'] == $request->getMethod() &&
                $resourceUrl->getHost() == $url->getHost() &&
                $resourceUrl->getPath() == $url->getPath() &&
                $resourceUrl->getQuery() == $url->getQuery()) {
                $response = $resource['handler']($request);

                return ResponseParser::convert($response);
            }
        }

        throw new Exception('Resource not available ' . $request->getMethod() . ' ' . $url);
    }

    public static function getByXmlDefinition($file)
    {
        if (!is_file($file)) {
            throw new Exception('Could not load mock xml definition ' . $file);
        }

        $mock = new self();
        $xml  = simplexml_load_file($file);

        foreach ($xml->resource as $resource) {
            $method   = (string) $resource->method;
            $url      = (string) $resource->url;
            $response = (string) $resource->response;

            $mock->add($method, $url, function ($request) use ($response) {
                return base64_decode($response);
            });
        }

        return $mock;
    }
}
