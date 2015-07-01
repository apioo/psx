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

use DOMDocument;
use DOMElement;
use PSX\Exception;
use PSX\Http;
use PSX\Http\HandlerInterface;
use PSX\Http\Options;
use PSX\Http\RequestInterface;

/**
 * Handler wich captures all http requests into an xml file wich can be loaded
 * by the Mock handler
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class MockCapture implements HandlerInterface
{
    protected $file;
    protected $handler;

    public function __construct($file, HandlerInterface $handler = null)
    {
        $this->file    = $file;
        $this->handler = $handler ?: new Curl();
    }

    public function request(RequestInterface $request, Options $options)
    {
        $response = $this->handler->request($request, $options);

        $dom = new DOMDocument();
        $dom->formatOutput = true;
        $dom->preserveWhiteSpace = false;

        if (is_file($this->file)) {
            $dom->load($this->file);
        }

        $rootElement = $dom->documentElement;

        if (!$rootElement instanceof DOMElement) {
            $rootElement = $dom->createElement('resources');
        }

        $resources = $rootElement->getElementsByTagName('resource');
        $replaced  = false;

        foreach ($resources as $resource) {
            $method = $resource->getElementsByTagName('method')->item(0);
            $url    = $resource->getElementsByTagName('url')->item(0);

            if ($method instanceof DOMElement && $url instanceof DOMElement) {
                if ($method->nodeValue == $request->getMethod() && $url->nodeValue == $request->getUri()->toString()) {
                    $element = $resource->getElementsByTagName('response')->item(0);

                    if ($element instanceof DOMElement) {
                        $element->nodeValue = base64_encode((string) $response);
                    } else {
                        $element = $dom->createElement('response');
                        $element->appendChild($dom->createTextNode(base64_encode((string) $response)));
                        $resource->appendChild($element);
                    }

                    $replaced = true;
                }
            }
        }

        if ($replaced === false) {
            $resource = $dom->createElement('resource');

            $element = $dom->createElement('method');
            $element->appendChild($dom->createTextNode($request->getMethod()));
            $resource->appendChild($element);

            $element = $dom->createElement('url');
            $element->appendChild($dom->createTextNode($request->getUri()->toString()));
            $resource->appendChild($element);

            $element = $dom->createElement('response');
            $element->appendChild($dom->createTextNode(base64_encode((string) $response)));
            $resource->appendChild($element);

            $rootElement->appendChild($resource);
        }

        $dom->appendChild($rootElement);
        $dom->save($this->file);

        return $response;
    }
}
