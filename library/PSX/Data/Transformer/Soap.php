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

namespace PSX\Data\Transformer;

use DOMDocument;
use DOMElement;
use InvalidArgumentException;
use PSX\Http\MediaType;
use RuntimeException;

/**
 * Transforms an incomming SOAP request
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Soap extends XmlArray
{
    const ENVELOPE_NS = 'http://schemas.xmlsoap.org/soap/envelope/';

    public function __construct($namespace)
    {
        $this->namespace = $namespace;
    }

    public function accept(MediaType $contentType)
    {
        return $contentType->getName() == 'application/soap+xml';
    }

    public function transform($data)
    {
        if (!$data instanceof DOMDocument) {
            throw new InvalidArgumentException('Data must be an instanceof DOMDocument');
        }

        return $this->extractBody($data->documentElement);
    }

    protected function extractBody(DOMElement $element)
    {
        $body = $element->getElementsByTagNameNS(self::ENVELOPE_NS, 'Body')->item(0);

        if ($body instanceof DOMElement) {
            $root = $this->findFirstElement($body);

            if ($root instanceof DOMElement) {
                return $this->recToXml($root);
            }

            return new \stdClass();
        } else {
            throw new RuntimeException('Found no SOAP (' . self::ENVELOPE_NS . ') Body element');
        }
    }

    protected function findFirstElement(DOMElement $element)
    {
        foreach ($element->childNodes as $childNode) {
            if ($childNode->nodeType == XML_ELEMENT_NODE && $childNode->namespaceURI == $this->namespace) {
                return $childNode;
            }
        }

        return null;
    }
}
