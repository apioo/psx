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

namespace PSX\Api\Generator\Html\Sample\Loader;

use DOMDocument;
use DOMElement;
use PSX\Api\Generator\HtmlAbstract;
use PSX\Api\Generator\Html\Sample\LoaderInterface;

/**
 * XmlFile
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class XmlFile implements LoaderInterface
{
    protected $file;

    private $_doc;

    public function __construct($file)
    {
        $this->file = $file;
    }

    public function get($type, $method, $path, $statusCode = null)
    {
        $samples = $this->getDocument()->getElementsByTagName('sample');

        foreach ($samples as $sample) {
            $sampleMethod = strtoupper($sample->getAttribute('method'));
            $samplePath   = $sample->getAttribute('path');

            if ($sampleMethod == $method && $samplePath == $path) {
                $node = null;
                if ($type == HtmlAbstract::TYPE_PATH) {
                    $node = $sample->getElementsByTagName('path')->item(0);
                } elseif ($type == HtmlAbstract::TYPE_QUERY) {
                    $node = $sample->getElementsByTagName('query')->item(0);
                } elseif ($type == HtmlAbstract::TYPE_REQUEST) {
                    $node = $sample->getElementsByTagName('request')->item(0);
                } elseif ($type == HtmlAbstract::TYPE_RESPONSE) {
                    $responses = $sample->getElementsByTagName('response');
                    foreach ($responses as $response) {
                        if ($response->getAttribute('status-code') == $statusCode) {
                            $node = $response;
                            break;
                        }
                    }
                }

                if ($node instanceof DOMElement) {
                    $lang = $node->getAttribute('lang') ?: 'text';

                    return '<pre><code class="' . $lang . '">' . htmlspecialchars($node->textContent) . '</code></pre>';
                }
            }
        }

        return null;
    }

    protected function getDocument()
    {
        if ($this->_doc !== null) {
            return $this->_doc;
        }

        $this->_doc = new DOMDocument();
        $this->_doc->load($this->file);

        return $this->_doc;
    }
}
