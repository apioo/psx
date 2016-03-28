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

namespace PSX\Api\Tests\Generator\Html\Sample\Loader;

use PSX\Api\Generator\Html\Sample\Loader\XmlFile;
use PSX\Api\Generator\HtmlAbstract;

/**
 * XmlFileTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class XmlFileTest extends \PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $loader = new XmlFile(__DIR__ . '/sample.xml');

        $this->assertEquals('<pre><code class="http">get-request</code></pre>', $loader->get(HtmlAbstract::TYPE_REQUEST, 'GET', '/foo/bar'));
        $this->assertEquals('<pre><code class="http">get-response</code></pre>', $loader->get(HtmlAbstract::TYPE_RESPONSE, 'GET', '/foo/bar', 200));
        $this->assertEquals('<pre><code class="http">post-request</code></pre>', $loader->get(HtmlAbstract::TYPE_REQUEST, 'POST', '/foo/bar'));
        $this->assertEquals('<pre><code class="http">post-response</code></pre>', $loader->get(HtmlAbstract::TYPE_RESPONSE, 'POST', '/foo/bar', 201));
        $this->assertEquals('<pre><code class="http">put-request</code></pre>', $loader->get(HtmlAbstract::TYPE_REQUEST, 'PUT', '/foo/bar'));
        $this->assertEquals('<pre><code class="http">put-response</code></pre>', $loader->get(HtmlAbstract::TYPE_RESPONSE, 'PUT', '/foo/bar', 200));
        $this->assertEquals('<pre><code class="http">delete-request</code></pre>', $loader->get(HtmlAbstract::TYPE_REQUEST, 'DELETE', '/foo/bar'));
        $this->assertEquals('<pre><code class="http">delete-response</code></pre>', $loader->get(HtmlAbstract::TYPE_RESPONSE, 'DELETE', '/foo/bar', 200));

        $this->assertEquals('<pre><code class="http">get-request-detail</code></pre>', $loader->get(HtmlAbstract::TYPE_REQUEST, 'GET', '/population/:id'));
        $this->assertEquals('<pre><code class="http">get-response-detail</code></pre>', $loader->get(HtmlAbstract::TYPE_RESPONSE, 'GET', '/population/:id', 500));
    }

    public function testGetUnknownType()
    {
        $loader = new XmlFile(__DIR__ . '/sample.xml');

        $this->assertEmpty($loader->get(0, 'GET', '/foo/bar'));
        $this->assertEmpty($loader->get('', 'GET', '/foo/bar'));
    }

    public function testGetUnknownRequestMethod()
    {
        $loader = new XmlFile(__DIR__ . '/sample.xml');

        $this->assertEmpty($loader->get(HtmlAbstract::TYPE_REQUEST, 'FOO', '/foo/bar'));
    }

    public function testGetUnknownPath()
    {
        $loader = new XmlFile(__DIR__ . '/sample.xml');

        $this->assertEmpty($loader->get(HtmlAbstract::TYPE_REQUEST, 'GET', null));
        $this->assertEmpty($loader->get(HtmlAbstract::TYPE_RESPONSE, 'GET', '/foo'));
    }

    public function testGetInvalidRequestMethod()
    {
        $loader = new XmlFile(__DIR__ . '/sample.xml');

        $this->assertEmpty($loader->get(HtmlAbstract::TYPE_REQUEST, 'GET', '/bar/method'));
    }
}
