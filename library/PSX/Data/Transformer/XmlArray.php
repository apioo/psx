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

namespace PSX\Data\Transformer;

use DOMDocument;
use DOMElement;
use InvalidArgumentException;
use PSX\Data\TransformerInterface;
use PSX\Http\MediaType;

/**
 * Takes an DOMDocument and formats it into an stdClass structure which can be
 * used by an importer. It takes the approach that if XML child elements have
 * the same name they become an array
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class XmlArray implements TransformerInterface
{
    protected $namespace;

    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

    public function accept(MediaType $contentType)
    {
        return in_array($contentType->getName(), MediaType\Xml::getMediaTypes()) ||
            substr($contentType->getSubType(), -4) == '+xml' ||
            substr($contentType->getSubType(), -4) == '/xml';
    }

    public function transform($data)
    {
        if (!$data instanceof DOMDocument) {
            throw new InvalidArgumentException('Data must be an instanceof DOMDocument');
        }

        return $this->recToXml($data->documentElement);
    }

    protected function recToXml(DOMElement $element)
    {
        $result = new \stdClass();

        foreach ($element->childNodes as $node) {
            if ($node->nodeType !== XML_ELEMENT_NODE) {
                continue;
            }

            if ($this->namespace !== null && $node->namespaceURI != $this->namespace) {
                continue;
            }

            if (isset($result->{$node->localName}) && !is_array($result->{$node->localName})) {
                $result->{$node->localName} = array($result->{$node->localName});
            }

            if ($this->hasChildElements($node, $this->namespace)) {
                $value = $this->recToXml($node);
            } else {
                if ($this->namespace !== null && $this->hasChildElements($node, null)) {
                    // if we need an specific namespace and the node has child
                    // elements add the complete node as value since we have no
                    // idea howto handle the data
                    $value = $node;
                } else {
                    $value = $node->textContent;
                }
            }

            if (isset($result->{$node->localName}) && is_array($result->{$node->localName})) {
                $result->{$node->localName}[] = $value;
            } else {
                $result->{$node->localName} = $value;
            }
        }

        return $result;
    }

    protected function hasChildElements(DOMElement $element, $namespace)
    {
        foreach ($element->childNodes as $node) {
            if ($node->nodeType === XML_ELEMENT_NODE && ($namespace === null || $node->namespaceURI == $namespace)) {
                return true;
            }
        }

        return false;
    }
}
