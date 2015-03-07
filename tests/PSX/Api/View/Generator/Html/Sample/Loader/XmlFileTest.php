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

namespace PSX\Api\View\Generator\Html\Sample\Loader;

use PSX\Api\View;

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

		$this->assertEquals('<pre><code class="http">get-request</code></pre>', $loader->get(View::METHOD_GET | View::TYPE_REQUEST, '/foo/bar'));
		$this->assertEquals('<pre><code class="http">get-response</code></pre>', $loader->get(View::METHOD_GET | View::TYPE_RESPONSE, '/foo/bar'));
		$this->assertEquals('<pre><code class="http">post-request</code></pre>', $loader->get(View::METHOD_POST | View::TYPE_REQUEST, '/foo/bar'));
		$this->assertEquals('<pre><code class="http">post-response</code></pre>', $loader->get(View::METHOD_POST | View::TYPE_RESPONSE, '/foo/bar'));
		$this->assertEquals('<pre><code class="http">put-request</code></pre>', $loader->get(View::METHOD_PUT | View::TYPE_REQUEST, '/foo/bar'));
		$this->assertEquals('<pre><code class="http">put-response</code></pre>', $loader->get(View::METHOD_PUT | View::TYPE_RESPONSE, '/foo/bar'));
		$this->assertEquals('<pre><code class="http">delete-request</code></pre>', $loader->get(View::METHOD_DELETE | View::TYPE_REQUEST, '/foo/bar'));
		$this->assertEquals('<pre><code class="http">delete-response</code></pre>', $loader->get(View::METHOD_DELETE | View::TYPE_RESPONSE, '/foo/bar'));

		$this->assertEquals('<pre><code class="http">get-request-detail</code></pre>', $loader->get(View::METHOD_GET | View::TYPE_REQUEST, '/population/:id'));
		$this->assertEquals('<pre><code class="http">get-response-detail</code></pre>', $loader->get(View::METHOD_GET | View::TYPE_RESPONSE, '/population/:id'));
	}

	public function testGetUnknownModifier()
	{
		$loader = new XmlFile(__DIR__ . '/sample.xml');

		$this->assertEmpty($loader->get(0, '/foo/bar'));
		$this->assertEmpty($loader->get('', '/foo/bar'));
		$this->assertEmpty($loader->get('GET', '/foo/bar'));
	}

	public function testGetUnknownPath()
	{
		$loader = new XmlFile(__DIR__ . '/sample.xml');

		$this->assertEmpty($loader->get(View::METHOD_GET | View::TYPE_REQUEST, null));
		$this->assertEmpty($loader->get(View::METHOD_GET | View::TYPE_REQUEST, '/foo'));
	}

	public function testGetInvalidType()
	{
		$loader = new XmlFile(__DIR__ . '/sample.xml');

		$this->assertEmpty($loader->get(View::METHOD_GET | View::TYPE_REQUEST, '/bar/type'));
	}

	public function testGetInvalidRequestMethod()
	{
		$loader = new XmlFile(__DIR__ . '/sample.xml');

		$this->assertEmpty($loader->get(View::METHOD_GET | View::TYPE_REQUEST, '/bar/method'));
	}
}
