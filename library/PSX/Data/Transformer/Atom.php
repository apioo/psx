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
use PSX\Data\TransformerInterface;
use PSX\Data\Writer\Atom\Writer as AtomWriter;
use PSX\Http\MediaType;

/**
 * Atom
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Atom implements TransformerInterface
{
    public function transform($data)
    {
        if (!$data instanceof DOMDocument) {
            throw new InvalidArgumentException('Data must be an instanceof DOMDocument');
        }

        $name = strtolower($data->documentElement->localName);

        if ($name == 'feed') {
            return $this->parseFeedElement($data->documentElement);
        } elseif ($name == 'entry') {
            $feed = new \stdClass();
            $feed->entry = array($this->parseEntryElement($data->documentElement));

            return $feed;
        } else {
            throw new InvalidArgumentException('Found no feed or entry element');
        }
    }

    protected function parseFeedElement(DOMElement $feed)
    {
        $result = new \stdClass();

        for ($i = 0; $i < $feed->childNodes->length; $i++) {
            $item = $feed->childNodes->item($i);

            if ($item->nodeType != XML_ELEMENT_NODE) {
                continue;
            }

            $name = strtolower($item->localName);

            switch ($name) {
                case 'author':
                    $result->author[] = self::personConstruct($item);
                    break;

                case 'contributor':
                    $result->contributor[] = self::personConstruct($item);
                    break;

                case 'category':
                    $result->category[] = self::categoryConstruct($item);
                    break;

                case 'generator':
                    $result->generator = new \stdClass();
                    $result->generator->text = $item->nodeValue;
                    $result->generator->uri = $item->getAttribute('uri');
                    $result->generator->version = $item->getAttribute('version');
                    break;

                case 'icon':
                case 'logo':
                case 'id':
                case 'rights':
                case 'title':
                    $result->$name = $item->nodeValue;
                    break;

                case 'updated':
                    $result->updated = $item->nodeValue;
                    break;

                case 'link':
                    $result->link[] = self::linkConstruct($item);
                    break;

                case 'subtitle':
                    $result->subTitle = self::textConstruct($item);
                    break;

                case 'entry':
                    $result->entry[] = $this->parseEntryElement($item);
                    break;
            }
        }

        return $result;
    }

    protected function parseEntryElement(DOMElement $entry)
    {
        $result = new \stdClass();

        for ($i = 0; $i < $entry->childNodes->length; $i++) {
            $item = $entry->childNodes->item($i);

            if ($item->nodeType != XML_ELEMENT_NODE) {
                continue;
            }

            $name = strtolower($item->localName);

            switch ($name) {
                case 'author':
                    $result->author[] = self::personConstruct($item);
                    break;

                case 'contributor':
                    $result->contributor[] = self::personConstruct($item);
                    break;

                case 'category':
                    $result->category[] = self::categoryConstruct($item);
                    break;

                case 'content':
                    $result->content = self::textConstruct($item);
                    break;

                case 'id':
                case 'rights':
                case 'title':
                case 'published':
                    $result->$name = $item->nodeValue;
                    break;

                case 'updated':
                    $result->updated = $item->nodeValue;
                    break;

                case 'link':
                    $result->link[] = self::linkConstruct($item);
                    break;

                case 'source':
                    $dom  = new DOMDocument();
                    $feed = $dom->createElementNS(AtomWriter::$xmlns, 'feed');

                    foreach ($item->childNodes as $node) {
                        // the source node must not contain entry elements
                        if ($node->nodeType == XML_ELEMENT_NODE && $node->nodeName != 'entry') {
                            $feed->appendChild($dom->importNode($node, true));
                        }
                    }

                    $dom->appendChild($feed);

                    $result->source = $this->parseFeedElement($dom->documentElement);
                    break;

                case 'summary':
                    $result->summary = self::textConstruct($item);
                    break;
            }
        }

        return $result;
    }

    public static function textConstruct(DOMElement $el)
    {
        $text = new \stdClass();
        $type = strtolower($el->getAttribute('type'));

        if (empty($type) || $type == 'text' || $type == 'html' || substr($type, 0, 5) == 'text/') {
            $content = $el->nodeValue;
        } elseif ($type == 'xhtml' || in_array($type, MediaType\Xml::getMediaTypes()) || substr($type, -4) == '+xml' || substr($type, -4) == '/xml') {
            // get first child element
            $child = null;
            foreach ($el->childNodes as $node) {
                if ($node->nodeType == XML_ELEMENT_NODE) {
                    $child = $node;
                    break;
                }
            }

            if ($child !== null) {
                $content = $el->ownerDocument->saveXML($child);
            } else {
                $content = null;
            }
        } else {
            $content = base64_decode($el->nodeValue);
        }

        if (!empty($type)) {
            $text->type = $type;
        }

        $text->content = $content;

        return $text;
    }

    public static function personConstruct(DOMElement $el)
    {
        $person = new \stdClass();

        for ($i = 0; $i < $el->childNodes->length; $i++) {
            $item = $el->childNodes->item($i);

            if ($item->nodeType != XML_ELEMENT_NODE) {
                continue;
            }

            $name = strtolower($item->nodeName);

            switch ($name) {
                case 'name':
                case 'uri':
                case 'email':
                    $person->$name = $item->nodeValue;
                    break;
            }
        }

        return $person;
    }

    public static function categoryConstruct(DOMElement $el)
    {
        $category = new \stdClass();

        if ($el->hasAttribute('term')) {
            $category->term = $el->getAttribute('term');
        }

        if ($el->hasAttribute('scheme')) {
            $category->scheme = $el->getAttribute('scheme');
        }

        if ($el->hasAttribute('label')) {
            $category->label = $el->getAttribute('label');
        }

        return $category;
    }

    public static function linkConstruct(DOMElement $el)
    {
        $link = new \stdClass();

        if ($el->hasAttribute('href')) {
            $link->href = $el->getAttribute('href');
        }

        if ($el->hasAttribute('rel')) {
            $link->rel = $el->getAttribute('rel');
        }

        if ($el->hasAttribute('type')) {
            $link->type = $el->getAttribute('type');
        }

        if ($el->hasAttribute('hreflang')) {
            $link->hreflang = $el->getAttribute('hreflang');
        }

        if ($el->hasAttribute('title')) {
            $link->title = $el->getAttribute('title');
        }

        if ($el->hasAttribute('length')) {
            $link->length = $el->getAttribute('length');
        }

        return $link;
    }
}
