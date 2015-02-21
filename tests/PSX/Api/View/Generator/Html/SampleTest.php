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

namespace PSX\Api\View\Generator\Html;

use PSX\Api\View\Generator\GeneratorTestCase;
use PSX\Api\View\Generator\Html\Sample\Loader;

/**
 * SampleTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class SampleTest extends GeneratorTestCase
{
	public function testGenerate()
	{
		$generator = new Sample(new Loader\XmlFile(__DIR__ . '/Sample/Loader/sample.xml'));
		$html      = $generator->generate($this->getView());

		$expect = <<<XML
<div class="view psx-api-view-generator-html-sample" data-status="0" data-path="/foo/bar">
	<h4>Example</h4>
	<div class="view-schema" data-modifier="33">
		<h5>GET Response</h5>
		<div class="view-schema-content">
			<pre>
				<code class="http">get-response</code>
			</pre>
		</div>
	</div>
	<div class="view-schema" data-modifier="18">
		<h5>POST Request</h5>
		<div class="view-schema-content">
			<pre>
				<code class="http">post-request</code>
			</pre>
		</div>
	</div>
	<div class="view-schema" data-modifier="34">
		<h5>POST Response</h5>
		<div class="view-schema-content">
			<pre>
				<code class="http">post-response</code>
			</pre>
		</div>
	</div>
	<div class="view-schema" data-modifier="20">
		<h5>PUT Request</h5>
		<div class="view-schema-content">
			<pre>
				<code class="http">put-request</code>
			</pre>
		</div>
	</div>
	<div class="view-schema" data-modifier="36">
		<h5>PUT Response</h5>
		<div class="view-schema-content">
			<pre>
				<code class="http">put-response</code>
			</pre>
		</div>
	</div>
	<div class="view-schema" data-modifier="24">
		<h5>DELETE Request</h5>
		<div class="view-schema-content">
			<pre>
				<code class="http">delete-request</code>
			</pre>
		</div>
	</div>
	<div class="view-schema" data-modifier="40">
		<h5>DELETE Response</h5>
		<div class="view-schema-content">
			<pre>
				<code class="http">delete-response</code>
			</pre>
		</div>
	</div>
</div>
XML;

		$this->assertXmlStringEqualsXmlString($expect, $html);
	}
}
