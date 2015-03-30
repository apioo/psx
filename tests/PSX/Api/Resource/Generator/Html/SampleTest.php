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

namespace PSX\Api\Resource\Generator\Html;

use PSX\Api\Resource\Generator\GeneratorTestCase;
use PSX\Api\Resource\Generator\Html\Sample\Loader;

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
		$html      = $generator->generate($this->getResource());

		$expect = <<<XML
<div class="psx-resource psx-api-resource-generator-html-sample" data-path="/foo/bar" data-status="1">
	<h4>Example</h4>
	<div class="psx-resource-method" data-method="GET">
		<div class="psx-resource-data psx-resource-query">
			<h5>GET Query-Parameters</h5>
			<div class="psx-resource-data-content">
				<pre>
					<code class="http">get-query</code>
				</pre>
			</div>
		</div>
		<div class="psx-resource-data psx-resource-response">
			<h5>GET Response - 200 OK</h5>
			<div class="psx-resource-data-content">
				<pre>
					<code class="http">get-response</code>
				</pre>
			</div>
		</div>
	</div>
	<div class="psx-resource-method" data-method="POST">
		<div class="psx-resource-data psx-resource-query">
			<h5>POST Query-Parameters</h5>
			<div class="psx-resource-data-content">
				<pre>
					<code class="http">get-query</code>
				</pre>
			</div>
		</div>
		<div class="psx-resource-data psx-resource-request">
			<h5>POST Request</h5>
			<div class="psx-resource-data-content">
				<pre>
					<code class="http">post-request</code>
				</pre>
			</div>
		</div>
		<div class="psx-resource-data psx-resource-response">
			<h5>POST Response - 200 OK</h5>
			<div class="psx-resource-data-content">
				<pre>
					<code class="http">post-response</code>
				</pre>
			</div>
		</div>
	</div>
	<div class="psx-resource-method" data-method="PUT">
		<div class="psx-resource-data psx-resource-query">
			<h5>PUT Query-Parameters</h5>
			<div class="psx-resource-data-content">
				<pre>
					<code class="http">get-query</code>
				</pre>
			</div>
		</div>
		<div class="psx-resource-data psx-resource-request">
			<h5>PUT Request</h5>
			<div class="psx-resource-data-content">
				<pre>
					<code class="http">put-request</code>
				</pre>
			</div>
		</div>
		<div class="psx-resource-data psx-resource-response">
			<h5>PUT Response - 200 OK</h5>
			<div class="psx-resource-data-content">
				<pre>
					<code class="http">put-response</code>
				</pre>
			</div>
		</div>
	</div>
	<div class="psx-resource-method" data-method="DELETE">
		<div class="psx-resource-data psx-resource-query">
			<h5>DELETE Query-Parameters</h5>
			<div class="psx-resource-data-content">
				<pre>
					<code class="http">get-query</code>
				</pre>
			</div>
		</div>
		<div class="psx-resource-data psx-resource-request">
			<h5>DELETE Request</h5>
			<div class="psx-resource-data-content">
				<pre>
					<code class="http">delete-request</code>
				</pre>
			</div>
		</div>
		<div class="psx-resource-data psx-resource-response">
			<h5>DELETE Response - 200 OK</h5>
			<div class="psx-resource-data-content">
				<pre>
					<code class="http">delete-response</code>
				</pre>
			</div>
		</div>
	</div>
</div>
XML;

		$this->assertXmlStringEqualsXmlString($expect, $html);
	}
}
