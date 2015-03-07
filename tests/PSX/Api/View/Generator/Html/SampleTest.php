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

namespace PSX\Api\View\Generator\Html;

use PSX\Api\View\Generator\GeneratorTestCase;
use PSX\Api\View\Generator\Html\Sample\Loader;

/**
 * SampleTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
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
