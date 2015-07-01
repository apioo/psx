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
 * Rss
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Rss implements TransformerInterface
{
    public function accept(MediaType $contentType)
    {
        return $contentType->getName() == 'application/rss+xml';
    }

    public function transform($data)
    {
        if (!$data instanceof DOMDocument) {
            throw new InvalidArgumentException('Data must be an instanceof DOMDocument');
        }

        $name = strtolower($data->documentElement->localName);

        if ($name == 'rss') {
            $channel = $data->documentElement->getElementsByTagName('channel');

            if ($channel->item(0) instanceof DOMElement) {
                return $this->parseChannelElement($channel->item(0));
            } else {
                throw new InvalidArgumentException('Found no channel element');
            }
        } elseif ($name == 'item') {
            $rss = new \stdClass();
            $rss->item = array($this->parseItemElement($data->documentElement));

            return $rss;
        } else {
            throw new InvalidArgumentException('Found no rss or item element');
        }
    }

    protected function parseChannelElement(DOMElement $channel)
    {
        $result = new \stdClass();
        $result->type = 'rss';

        $childNodes = $channel->childNodes;

        for ($i = 0; $i < $childNodes->length; $i++) {
            $item = $childNodes->item($i);

            if ($item->nodeType != XML_ELEMENT_NODE) {
                continue;
            }

            $name = strtolower($item->localName);

            switch ($name) {
                case 'title':
                case 'link':
                case 'description':
                case 'language':
                case 'copyright':
                case 'generator':
                case 'docs':
                case 'ttl':
                case 'image':
                case 'rating':
                    $result->$name = $item->nodeValue;
                    break;

                case 'managingeditor':
                    $result->managingEditor = $item->nodeValue;
                    break;

                case 'webmaster':
                    $result->webMaster = $item->nodeValue;
                    break;

                case 'category':
                    $result->category = self::categoryConstruct($item);
                    break;

                case 'pubdate':
                    $result->pubDate = $item->nodeValue;
                    break;

                case 'lastbuilddate':
                    $result->lastBuildDate = $item->nodeValue;
                    break;

                case 'item':
                    $result->item[] = $this->parseItemElement($item);
                    break;
            }
        }

        return $result;
    }

    protected function parseItemElement(DOMElement $element)
    {
        $result = new \stdClass();
        $result->type = 'item';

        for ($i = 0; $i < $element->childNodes->length; $i++) {
            $item = $element->childNodes->item($i);

            if ($item->nodeType != XML_ELEMENT_NODE) {
                continue;
            }

            $name = strtolower($item->localName);

            switch ($name) {
                case 'title':
                case 'link':
                case 'description':
                case 'author':
                case 'comments':
                case 'guid':
                    $result->$name = $item->nodeValue;
                    break;

                case 'category':
                    $result->$name = self::categoryConstruct($item);
                    break;

                case 'enclosure':
                    $result->enclosure = new \stdClass();
                    $result->enclosure->url = $item->getAttribute('url');
                    $result->enclosure->length = $item->getAttribute('length');
                    $result->enclosure->type = $item->getAttribute('type');
                    break;

                case 'pubdate':
                    $result->pubDate = $item->nodeValue;
                    break;

                case 'source':
                    $result->source = new \stdClass();
                    $result->source->text = $item->nodeValue;
                    $result->source->url = $item->getAttribute('url');
                    break;
            }
        }

        return $result;
    }

    public static function categoryConstruct(DOMElement $category)
    {
        $result = new \stdClass();
        $result->text = $category->nodeValue;

        $domain = $category->getAttribute('domain');
        if (!empty($domain)) {
            $result->domain = $domain;
        }

        return $result;
    }
}
