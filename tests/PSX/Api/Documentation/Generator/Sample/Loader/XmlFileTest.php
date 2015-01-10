<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
 *
 * This file is part of psx. psx is free software: you can
 * redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or any later version.
 *
 * psx is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with psx. If not, see <http://www.gnu.org/licenses/>.
 */

namespace PSX\Api\Documentation\Generator\Sample\Loader;

use PSX\Api\View;

/**
 * XmlFileTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class XmlFileTest extends \PHPUnit_Framework_TestCase
{
	public function testGet()
	{
		$loader = new XmlFile(__DIR__ . '/sample.xml');

		$this->assertEquals('<pre><code class="http">get-request</code></pre>', $loader->get(View::METHOD_GET | View::TYPE_REQUEST, '/population'));
		$this->assertEquals('<pre><code class="http">get-response</code></pre>', $loader->get(View::METHOD_GET | View::TYPE_RESPONSE, '/population'));
		$this->assertEquals('<pre><code class="http">post-request</code></pre>', $loader->get(View::METHOD_POST | View::TYPE_REQUEST, '/population'));
		$this->assertEquals('<pre><code class="http">post-response</code></pre>', $loader->get(View::METHOD_POST | View::TYPE_RESPONSE, '/population'));
		$this->assertEquals('<pre><code class="http">put-request</code></pre>', $loader->get(View::METHOD_PUT | View::TYPE_REQUEST, '/population'));
		$this->assertEquals('<pre><code class="http">put-response</code></pre>', $loader->get(View::METHOD_PUT | View::TYPE_RESPONSE, '/population'));
		$this->assertEquals('<pre><code class="http">delete-request</code></pre>', $loader->get(View::METHOD_DELETE | View::TYPE_REQUEST, '/population'));
		$this->assertEquals('<pre><code class="http">delete-response</code></pre>', $loader->get(View::METHOD_DELETE | View::TYPE_RESPONSE, '/population'));

		$this->assertEquals('<pre><code class="http">get-request-detail</code></pre>', $loader->get(View::METHOD_GET | View::TYPE_REQUEST, '/population/:id'));
		$this->assertEquals('<pre><code class="http">get-response-detail</code></pre>', $loader->get(View::METHOD_GET | View::TYPE_RESPONSE, '/population/:id'));
	}

	public function testGetUnknownModifier()
	{
		$loader = new XmlFile(__DIR__ . '/sample.xml');

		$this->assertEmpty($loader->get(0, '/population'));
		$this->assertEmpty($loader->get('', '/population'));
		$this->assertEmpty($loader->get('GET', '/population'));
	}

	public function testGetUnknownPath()
	{
		$loader = new XmlFile(__DIR__ . '/sample.xml');

		$this->assertEmpty($loader->get(View::METHOD_GET | View::TYPE_REQUEST, null));
		$this->assertEmpty($loader->get(View::METHOD_GET | View::TYPE_REQUEST, '/foo'));
	}

}
