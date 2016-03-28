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

namespace PSX\Api\Tests\Generator\Html;

use PSX\Api\Generator\Html\Schema;
use PSX\Api\Tests\Generator\GeneratorTestCase;
use PSX\Schema\Generator\Html as GeneratorHtml;

/**
 * SchemaTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class SchemaTest extends GeneratorTestCase
{
    public function testGenerate()
    {
        $generator = new Schema(new GeneratorHtml());
        $html      = $generator->generate($this->getResource());

        $expect = <<<XML
<div class="psx-resource psx-api-generator-html-schema" data-status="1" data-path="/foo/bar">
	<h4>Schema</h4>
	<div class="psx-resource-description">lorem ipsum</div>
	<div class="psx-resource-method" data-method="GET">
		<div class="psx-resource-method-description">Returns a collection</div>
		<div class="psx-resource-data psx-resource-query">
			<h5>GET Path-Parameters</h5>
			<div class="psx-resource-data-content">
				<div id="psx-type-324d9c87eb6ee494de5207f005abddb8" class="psx-complex-type">
					<h1>path</h1>
					<div class="psx-type-description"/>
					<table class="table psx-type-properties">
						<colgroup>
							<col width="20%" />
							<col width="20%" />
							<col width="40%" />
							<col width="20%" />
						</colgroup>
						<thead>
							<tr>
								<th>Property</th>
								<th>Type</th>
								<th>Description</th>
								<th>Constraints</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<span class="psx-property-name psx-property-optional">name</span>
								</td>
								<td>
									<span class="psx-property-type psx-property-type-string">String</span>
								</td>
								<td>
									<span class="psx-property-description">Name parameter</span>
								</td>
								<td>
									<dl class="psx-property-constraint">
										<dt>Pattern</dt>
										<dd>
											<span class="psx-constraint-pattern">[A-z]+</span>
										</dd>
										<dt>Minimum</dt>
										<dd>
											<span class="psx-constraint-minimum">0</span>
										</dd>
										<dt>Maximum</dt>
										<dd>
											<span class="psx-constraint-maximum">16</span>
										</dd>
									</dl>
								</td>
							</tr>
							<tr>
								<td>
									<span class="psx-property-name psx-property-optional">type</span>
								</td>
								<td>
									<span class="psx-property-type psx-property-type-string">String</span>
								</td>
								<td>
									<span class="psx-property-description"/>
								</td>
								<td>
									<dl class="psx-property-constraint">
										<dt>Enumeration</dt>
										<dd>
											<span class="psx-constraint-enumeration">
												<ul class="psx-property-enumeration">
													<li>
														<span class="psx-constraint-enumeration-value">foo</span>
													</li>
													<li>
														<span class="psx-constraint-enumeration-value">bar</span>
													</li>
												</ul>
											</span>
										</dd>
									</dl>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="psx-resource-data psx-resource-query">
			<h5>GET Query-Parameters</h5>
			<div class="psx-resource-data-content">
				<div id="psx-type-85f5cb99d4cb24e97943e04989396c8e" class="psx-complex-type">
					<h1>query</h1>
					<div class="psx-type-description"/>
					<table class="table psx-type-properties">
						<colgroup>
							<col width="20%" />
							<col width="20%" />
							<col width="40%" />
							<col width="20%" />
						</colgroup>
						<thead>
							<tr>
								<th>Property</th>
								<th>Type</th>
								<th>Description</th>
								<th>Constraints</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<span class="psx-property-name psx-property-optional">startIndex</span>
								</td>
								<td>
									<span class="psx-property-type psx-property-type-integer">Integer</span>
								</td>
								<td>
									<span class="psx-property-description">startIndex parameter</span>
								</td>
								<td>
									<dl class="psx-property-constraint">
										<dt>Minimum</dt>
										<dd>
											<span class="psx-constraint-minimum">0</span>
										</dd>
										<dt>Maximum</dt>
										<dd>
											<span class="psx-constraint-maximum">32</span>
										</dd>
									</dl>
								</td>
							</tr>
							<tr>
								<td>
									<span class="psx-property-name psx-property-optional">float</span>
								</td>
								<td>
									<span class="psx-property-type psx-property-type-float">Float</span>
								</td>
								<td>
									<span class="psx-property-description"/>
								</td>
								<td/>
							</tr>
							<tr>
								<td>
									<span class="psx-property-name psx-property-optional">boolean</span>
								</td>
								<td>
									<span class="psx-property-type psx-property-type-boolean">Boolean</span>
								</td>
								<td>
									<span class="psx-property-description"/>
								</td>
								<td/>
							</tr>
							<tr>
								<td>
									<span class="psx-property-name psx-property-optional">date</span>
								</td>
								<td>
									<span class="psx-property-type psx-property-type-date">
										<a href="http://tools.ietf.org/html/rfc3339#section-5.6" title="RFC3339">Date</a>
									</span>
								</td>
								<td>
									<span class="psx-property-description"/>
								</td>
								<td/>
							</tr>
							<tr>
								<td>
									<span class="psx-property-name psx-property-optional">datetime</span>
								</td>
								<td>
									<span class="psx-property-type psx-property-type-datetime">
										<a href="http://tools.ietf.org/html/rfc3339#section-5.6" title="RFC3339">DateTime</a>
									</span>
								</td>
								<td>
									<span class="psx-property-description"/>
								</td>
								<td/>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="psx-resource-data psx-resource-response">
			<h5>GET Response - 200 OK</h5>
			<div class="psx-resource-data-content">
				<div id="psx-type-ae7d4b5627a9dbac0c99945ecef66e17" class="psx-complex-type">
					<h1>collection</h1>
					<div class="psx-type-description"/>
					<table class="table psx-type-properties">
						<colgroup>
							<col width="20%" />
							<col width="20%" />
							<col width="40%" />
							<col width="20%" />
						</colgroup>
						<thead>
							<tr>
								<th>Property</th>
								<th>Type</th>
								<th>Description</th>
								<th>Constraints</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<span class="psx-property-name psx-property-optional">entry</span>
								</td>
								<td>
									<span class="psx-property-type psx-property-type-array">Array&lt;<span class="psx-property-type psx-property-type-complex">
											<a href="#psx-type-7bde1c36c5f13fd4cf10c2864f8e8a75">item</a>
										</span>&gt;</span>
								</td>
								<td>
									<span class="psx-property-description"/>
								</td>
								<td/>
							</tr>
						</tbody>
					</table>
				</div>
				<div id="psx-type-7bde1c36c5f13fd4cf10c2864f8e8a75" class="psx-complex-type">
					<h1>item</h1>
					<div class="psx-type-description"/>
					<table class="table psx-type-properties">
						<colgroup>
							<col width="20%" />
							<col width="20%" />
							<col width="40%" />
							<col width="20%" />
						</colgroup>
						<thead>
							<tr>
								<th>Property</th>
								<th>Type</th>
								<th>Description</th>
								<th>Constraints</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<span class="psx-property-name psx-property-optional">id</span>
								</td>
								<td>
									<span class="psx-property-type psx-property-type-integer">Integer</span>
								</td>
								<td>
									<span class="psx-property-description"/>
								</td>
								<td/>
							</tr>
							<tr>
								<td>
									<span class="psx-property-name psx-property-optional">userId</span>
								</td>
								<td>
									<span class="psx-property-type psx-property-type-integer">Integer</span>
								</td>
								<td>
									<span class="psx-property-description"/>
								</td>
								<td/>
							</tr>
							<tr>
								<td>
									<span class="psx-property-name psx-property-optional">title</span>
								</td>
								<td>
									<span class="psx-property-type psx-property-type-string">String</span>
								</td>
								<td>
									<span class="psx-property-description"/>
								</td>
								<td>
									<dl class="psx-property-constraint">
										<dt>Pattern</dt>
										<dd>
											<span class="psx-constraint-pattern">[A-z]+</span>
										</dd>
										<dt>Minimum</dt>
										<dd>
											<span class="psx-constraint-minimum">3</span>
										</dd>
										<dt>Maximum</dt>
										<dd>
											<span class="psx-constraint-maximum">16</span>
										</dd>
									</dl>
								</td>
							</tr>
							<tr>
								<td>
									<span class="psx-property-name psx-property-optional">date</span>
								</td>
								<td>
									<span class="psx-property-type psx-property-type-datetime">
										<a href="http://tools.ietf.org/html/rfc3339#section-5.6" title="RFC3339">DateTime</a>
									</span>
								</td>
								<td>
									<span class="psx-property-description"/>
								</td>
								<td/>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<div class="psx-resource-method" data-method="POST">
		<div class="psx-resource-data psx-resource-query">
			<h5>POST Path-Parameters</h5>
			<div class="psx-resource-data-content">
				<div id="psx-type-324d9c87eb6ee494de5207f005abddb8" class="psx-complex-type">
					<h1>path</h1>
					<div class="psx-type-description"/>
					<table class="table psx-type-properties">
						<colgroup>
							<col width="20%" />
							<col width="20%" />
							<col width="40%" />
							<col width="20%" />
						</colgroup>
						<thead>
							<tr>
								<th>Property</th>
								<th>Type</th>
								<th>Description</th>
								<th>Constraints</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<span class="psx-property-name psx-property-optional">name</span>
								</td>
								<td>
									<span class="psx-property-type psx-property-type-string">String</span>
								</td>
								<td>
									<span class="psx-property-description">Name parameter</span>
								</td>
								<td>
									<dl class="psx-property-constraint">
										<dt>Pattern</dt>
										<dd>
											<span class="psx-constraint-pattern">[A-z]+</span>
										</dd>
										<dt>Minimum</dt>
										<dd>
											<span class="psx-constraint-minimum">0</span>
										</dd>
										<dt>Maximum</dt>
										<dd>
											<span class="psx-constraint-maximum">16</span>
										</dd>
									</dl>
								</td>
							</tr>
							<tr>
								<td>
									<span class="psx-property-name psx-property-optional">type</span>
								</td>
								<td>
									<span class="psx-property-type psx-property-type-string">String</span>
								</td>
								<td>
									<span class="psx-property-description"/>
								</td>
								<td>
									<dl class="psx-property-constraint">
										<dt>Enumeration</dt>
										<dd>
											<span class="psx-constraint-enumeration">
												<ul class="psx-property-enumeration">
													<li>
														<span class="psx-constraint-enumeration-value">foo</span>
													</li>
													<li>
														<span class="psx-constraint-enumeration-value">bar</span>
													</li>
												</ul>
											</span>
										</dd>
									</dl>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="psx-resource-data psx-resource-request">
			<h5>POST Request</h5>
			<div class="psx-resource-data-content">
				<div id="psx-type-70152cdfc48a8a3969f10e9e4fe3b239" class="psx-complex-type">
					<h1>item</h1>
					<div class="psx-type-description"/>
					<table class="table psx-type-properties">
						<colgroup>
							<col width="20%" />
							<col width="20%" />
							<col width="40%" />
							<col width="20%" />
						</colgroup>
						<thead>
							<tr>
								<th>Property</th>
								<th>Type</th>
								<th>Description</th>
								<th>Constraints</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<span class="psx-property-name psx-property-optional">id</span>
								</td>
								<td>
									<span class="psx-property-type psx-property-type-integer">Integer</span>
								</td>
								<td>
									<span class="psx-property-description"/>
								</td>
								<td/>
							</tr>
							<tr>
								<td>
									<span class="psx-property-name psx-property-optional">userId</span>
								</td>
								<td>
									<span class="psx-property-type psx-property-type-integer">Integer</span>
								</td>
								<td>
									<span class="psx-property-description"/>
								</td>
								<td/>
							</tr>
							<tr>
								<td>
									<span class="psx-property-name psx-property-required">title</span>
								</td>
								<td>
									<span class="psx-property-type psx-property-type-string">String</span>
								</td>
								<td>
									<span class="psx-property-description"/>
								</td>
								<td>
									<dl class="psx-property-constraint">
										<dt>Pattern</dt>
										<dd>
											<span class="psx-constraint-pattern">[A-z]+</span>
										</dd>
										<dt>Minimum</dt>
										<dd>
											<span class="psx-constraint-minimum">3</span>
										</dd>
										<dt>Maximum</dt>
										<dd>
											<span class="psx-constraint-maximum">16</span>
										</dd>
									</dl>
								</td>
							</tr>
							<tr>
								<td>
									<span class="psx-property-name psx-property-required">date</span>
								</td>
								<td>
									<span class="psx-property-type psx-property-type-datetime">
										<a href="http://tools.ietf.org/html/rfc3339#section-5.6" title="RFC3339">DateTime</a>
									</span>
								</td>
								<td>
									<span class="psx-property-description"/>
								</td>
								<td/>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="psx-resource-data psx-resource-response">
			<h5>POST Response - 201 Created</h5>
			<div class="psx-resource-data-content">
				<div id="psx-type-31ead4d236fd038a7d55a40e2ca1171e" class="psx-complex-type">
					<h1>message</h1>
					<div class="psx-type-description"/>
					<table class="table psx-type-properties">
						<colgroup>
							<col width="20%" />
							<col width="20%" />
							<col width="40%" />
							<col width="20%" />
						</colgroup>
						<thead>
							<tr>
								<th>Property</th>
								<th>Type</th>
								<th>Description</th>
								<th>Constraints</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<span class="psx-property-name psx-property-optional">success</span>
								</td>
								<td>
									<span class="psx-property-type psx-property-type-boolean">Boolean</span>
								</td>
								<td>
									<span class="psx-property-description"/>
								</td>
								<td/>
							</tr>
							<tr>
								<td>
									<span class="psx-property-name psx-property-optional">message</span>
								</td>
								<td>
									<span class="psx-property-type psx-property-type-string">String</span>
								</td>
								<td>
									<span class="psx-property-description"/>
								</td>
								<td/>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<div class="psx-resource-method" data-method="PUT">
		<div class="psx-resource-data psx-resource-query">
			<h5>PUT Path-Parameters</h5>
			<div class="psx-resource-data-content">
				<div id="psx-type-324d9c87eb6ee494de5207f005abddb8" class="psx-complex-type">
					<h1>path</h1>
					<div class="psx-type-description"/>
					<table class="table psx-type-properties">
						<colgroup>
							<col width="20%" />
							<col width="20%" />
							<col width="40%" />
							<col width="20%" />
						</colgroup>
						<thead>
							<tr>
								<th>Property</th>
								<th>Type</th>
								<th>Description</th>
								<th>Constraints</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<span class="psx-property-name psx-property-optional">name</span>
								</td>
								<td>
									<span class="psx-property-type psx-property-type-string">String</span>
								</td>
								<td>
									<span class="psx-property-description">Name parameter</span>
								</td>
								<td>
									<dl class="psx-property-constraint">
										<dt>Pattern</dt>
										<dd>
											<span class="psx-constraint-pattern">[A-z]+</span>
										</dd>
										<dt>Minimum</dt>
										<dd>
											<span class="psx-constraint-minimum">0</span>
										</dd>
										<dt>Maximum</dt>
										<dd>
											<span class="psx-constraint-maximum">16</span>
										</dd>
									</dl>
								</td>
							</tr>
							<tr>
								<td>
									<span class="psx-property-name psx-property-optional">type</span>
								</td>
								<td>
									<span class="psx-property-type psx-property-type-string">String</span>
								</td>
								<td>
									<span class="psx-property-description"/>
								</td>
								<td>
									<dl class="psx-property-constraint">
										<dt>Enumeration</dt>
										<dd>
											<span class="psx-constraint-enumeration">
												<ul class="psx-property-enumeration">
													<li>
														<span class="psx-constraint-enumeration-value">foo</span>
													</li>
													<li>
														<span class="psx-constraint-enumeration-value">bar</span>
													</li>
												</ul>
											</span>
										</dd>
									</dl>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="psx-resource-data psx-resource-request">
			<h5>PUT Request</h5>
			<div class="psx-resource-data-content">
				<div id="psx-type-774a7a4ece700fad7bb605e81c61fea7" class="psx-complex-type">
					<h1>item</h1>
					<div class="psx-type-description"/>
					<table class="table psx-type-properties">
						<colgroup>
							<col width="20%" />
							<col width="20%" />
							<col width="40%" />
							<col width="20%" />
						</colgroup>
						<thead>
							<tr>
								<th>Property</th>
								<th>Type</th>
								<th>Description</th>
								<th>Constraints</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<span class="psx-property-name psx-property-required">id</span>
								</td>
								<td>
									<span class="psx-property-type psx-property-type-integer">Integer</span>
								</td>
								<td>
									<span class="psx-property-description"/>
								</td>
								<td/>
							</tr>
							<tr>
								<td>
									<span class="psx-property-name psx-property-optional">userId</span>
								</td>
								<td>
									<span class="psx-property-type psx-property-type-integer">Integer</span>
								</td>
								<td>
									<span class="psx-property-description"/>
								</td>
								<td/>
							</tr>
							<tr>
								<td>
									<span class="psx-property-name psx-property-optional">title</span>
								</td>
								<td>
									<span class="psx-property-type psx-property-type-string">String</span>
								</td>
								<td>
									<span class="psx-property-description"/>
								</td>
								<td>
									<dl class="psx-property-constraint">
										<dt>Pattern</dt>
										<dd>
											<span class="psx-constraint-pattern">[A-z]+</span>
										</dd>
										<dt>Minimum</dt>
										<dd>
											<span class="psx-constraint-minimum">3</span>
										</dd>
										<dt>Maximum</dt>
										<dd>
											<span class="psx-constraint-maximum">16</span>
										</dd>
									</dl>
								</td>
							</tr>
							<tr>
								<td>
									<span class="psx-property-name psx-property-optional">date</span>
								</td>
								<td>
									<span class="psx-property-type psx-property-type-datetime">
										<a href="http://tools.ietf.org/html/rfc3339#section-5.6" title="RFC3339">DateTime</a>
									</span>
								</td>
								<td>
									<span class="psx-property-description"/>
								</td>
								<td/>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="psx-resource-data psx-resource-response">
			<h5>PUT Response - 200 OK</h5>
			<div class="psx-resource-data-content">
				<div id="psx-type-31ead4d236fd038a7d55a40e2ca1171e" class="psx-complex-type">
					<h1>message</h1>
					<div class="psx-type-description"/>
					<table class="table psx-type-properties">
						<colgroup>
							<col width="20%" />
							<col width="20%" />
							<col width="40%" />
							<col width="20%" />
						</colgroup>
						<thead>
							<tr>
								<th>Property</th>
								<th>Type</th>
								<th>Description</th>
								<th>Constraints</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<span class="psx-property-name psx-property-optional">success</span>
								</td>
								<td>
									<span class="psx-property-type psx-property-type-boolean">Boolean</span>
								</td>
								<td>
									<span class="psx-property-description"/>
								</td>
								<td/>
							</tr>
							<tr>
								<td>
									<span class="psx-property-name psx-property-optional">message</span>
								</td>
								<td>
									<span class="psx-property-type psx-property-type-string">String</span>
								</td>
								<td>
									<span class="psx-property-description"/>
								</td>
								<td/>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<div class="psx-resource-method" data-method="DELETE">
		<div class="psx-resource-data psx-resource-query">
			<h5>DELETE Path-Parameters</h5>
			<div class="psx-resource-data-content">
				<div id="psx-type-324d9c87eb6ee494de5207f005abddb8" class="psx-complex-type">
					<h1>path</h1>
					<div class="psx-type-description"/>
					<table class="table psx-type-properties">
						<colgroup>
							<col width="20%" />
							<col width="20%" />
							<col width="40%" />
							<col width="20%" />
						</colgroup>
						<thead>
							<tr>
								<th>Property</th>
								<th>Type</th>
								<th>Description</th>
								<th>Constraints</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<span class="psx-property-name psx-property-optional">name</span>
								</td>
								<td>
									<span class="psx-property-type psx-property-type-string">String</span>
								</td>
								<td>
									<span class="psx-property-description">Name parameter</span>
								</td>
								<td>
									<dl class="psx-property-constraint">
										<dt>Pattern</dt>
										<dd>
											<span class="psx-constraint-pattern">[A-z]+</span>
										</dd>
										<dt>Minimum</dt>
										<dd>
											<span class="psx-constraint-minimum">0</span>
										</dd>
										<dt>Maximum</dt>
										<dd>
											<span class="psx-constraint-maximum">16</span>
										</dd>
									</dl>
								</td>
							</tr>
							<tr>
								<td>
									<span class="psx-property-name psx-property-optional">type</span>
								</td>
								<td>
									<span class="psx-property-type psx-property-type-string">String</span>
								</td>
								<td>
									<span class="psx-property-description"/>
								</td>
								<td>
									<dl class="psx-property-constraint">
										<dt>Enumeration</dt>
										<dd>
											<span class="psx-constraint-enumeration">
												<ul class="psx-property-enumeration">
													<li>
														<span class="psx-constraint-enumeration-value">foo</span>
													</li>
													<li>
														<span class="psx-constraint-enumeration-value">bar</span>
													</li>
												</ul>
											</span>
										</dd>
									</dl>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="psx-resource-data psx-resource-request">
			<h5>DELETE Request</h5>
			<div class="psx-resource-data-content">
				<div id="psx-type-774a7a4ece700fad7bb605e81c61fea7" class="psx-complex-type">
					<h1>item</h1>
					<div class="psx-type-description"/>
					<table class="table psx-type-properties">
						<colgroup>
							<col width="20%" />
							<col width="20%" />
							<col width="40%" />
							<col width="20%" />
						</colgroup>
						<thead>
							<tr>
								<th>Property</th>
								<th>Type</th>
								<th>Description</th>
								<th>Constraints</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<span class="psx-property-name psx-property-required">id</span>
								</td>
								<td>
									<span class="psx-property-type psx-property-type-integer">Integer</span>
								</td>
								<td>
									<span class="psx-property-description"/>
								</td>
								<td/>
							</tr>
							<tr>
								<td>
									<span class="psx-property-name psx-property-optional">userId</span>
								</td>
								<td>
									<span class="psx-property-type psx-property-type-integer">Integer</span>
								</td>
								<td>
									<span class="psx-property-description"/>
								</td>
								<td/>
							</tr>
							<tr>
								<td>
									<span class="psx-property-name psx-property-optional">title</span>
								</td>
								<td>
									<span class="psx-property-type psx-property-type-string">String</span>
								</td>
								<td>
									<span class="psx-property-description"/>
								</td>
								<td>
									<dl class="psx-property-constraint">
										<dt>Pattern</dt>
										<dd>
											<span class="psx-constraint-pattern">[A-z]+</span>
										</dd>
										<dt>Minimum</dt>
										<dd>
											<span class="psx-constraint-minimum">3</span>
										</dd>
										<dt>Maximum</dt>
										<dd>
											<span class="psx-constraint-maximum">16</span>
										</dd>
									</dl>
								</td>
							</tr>
							<tr>
								<td>
									<span class="psx-property-name psx-property-optional">date</span>
								</td>
								<td>
									<span class="psx-property-type psx-property-type-datetime">
										<a href="http://tools.ietf.org/html/rfc3339#section-5.6" title="RFC3339">DateTime</a>
									</span>
								</td>
								<td>
									<span class="psx-property-description"/>
								</td>
								<td/>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="psx-resource-data psx-resource-response">
			<h5>DELETE Response - 200 OK</h5>
			<div class="psx-resource-data-content">
				<div id="psx-type-31ead4d236fd038a7d55a40e2ca1171e" class="psx-complex-type">
					<h1>message</h1>
					<div class="psx-type-description"/>
					<table class="table psx-type-properties">
						<colgroup>
							<col width="20%" />
							<col width="20%" />
							<col width="40%" />
							<col width="20%" />
						</colgroup>
						<thead>
							<tr>
								<th>Property</th>
								<th>Type</th>
								<th>Description</th>
								<th>Constraints</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<span class="psx-property-name psx-property-optional">success</span>
								</td>
								<td>
									<span class="psx-property-type psx-property-type-boolean">Boolean</span>
								</td>
								<td>
									<span class="psx-property-description"/>
								</td>
								<td/>
							</tr>
							<tr>
								<td>
									<span class="psx-property-name psx-property-optional">message</span>
								</td>
								<td>
									<span class="psx-property-type psx-property-type-string">String</span>
								</td>
								<td>
									<span class="psx-property-description"/>
								</td>
								<td/>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<div class="psx-resource-method" data-method="PATCH">
		<div class="psx-resource-data psx-resource-query">
			<h5>PATCH Path-Parameters</h5>
			<div class="psx-resource-data-content">
				<div id="psx-type-324d9c87eb6ee494de5207f005abddb8" class="psx-complex-type">
					<h1>path</h1>
					<div class="psx-type-description"/>
					<table class="table psx-type-properties">
						<colgroup>
							<col width="20%" />
							<col width="20%" />
							<col width="40%" />
							<col width="20%" />
						</colgroup>
						<thead>
							<tr>
								<th>Property</th>
								<th>Type</th>
								<th>Description</th>
								<th>Constraints</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<span class="psx-property-name psx-property-optional">name</span>
								</td>
								<td>
									<span class="psx-property-type psx-property-type-string">String</span>
								</td>
								<td>
									<span class="psx-property-description">Name parameter</span>
								</td>
								<td>
									<dl class="psx-property-constraint">
										<dt>Pattern</dt>
										<dd>
											<span class="psx-constraint-pattern">[A-z]+</span>
										</dd>
										<dt>Minimum</dt>
										<dd>
											<span class="psx-constraint-minimum">0</span>
										</dd>
										<dt>Maximum</dt>
										<dd>
											<span class="psx-constraint-maximum">16</span>
										</dd>
									</dl>
								</td>
							</tr>
							<tr>
								<td>
									<span class="psx-property-name psx-property-optional">type</span>
								</td>
								<td>
									<span class="psx-property-type psx-property-type-string">String</span>
								</td>
								<td>
									<span class="psx-property-description"/>
								</td>
								<td>
									<dl class="psx-property-constraint">
										<dt>Enumeration</dt>
										<dd>
											<span class="psx-constraint-enumeration">
												<ul class="psx-property-enumeration">
													<li>
														<span class="psx-constraint-enumeration-value">foo</span>
													</li>
													<li>
														<span class="psx-constraint-enumeration-value">bar</span>
													</li>
												</ul>
											</span>
										</dd>
									</dl>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="psx-resource-data psx-resource-request">
			<h5>PATCH Request</h5>
			<div class="psx-resource-data-content">
				<div id="psx-type-774a7a4ece700fad7bb605e81c61fea7" class="psx-complex-type">
					<h1>item</h1>
					<div class="psx-type-description"/>
					<table class="table psx-type-properties">
						<colgroup>
							<col width="20%" />
							<col width="20%" />
							<col width="40%" />
							<col width="20%" />
						</colgroup>
						<thead>
							<tr>
								<th>Property</th>
								<th>Type</th>
								<th>Description</th>
								<th>Constraints</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<span class="psx-property-name psx-property-required">id</span>
								</td>
								<td>
									<span class="psx-property-type psx-property-type-integer">Integer</span>
								</td>
								<td>
									<span class="psx-property-description"/>
								</td>
								<td/>
							</tr>
							<tr>
								<td>
									<span class="psx-property-name psx-property-optional">userId</span>
								</td>
								<td>
									<span class="psx-property-type psx-property-type-integer">Integer</span>
								</td>
								<td>
									<span class="psx-property-description"/>
								</td>
								<td/>
							</tr>
							<tr>
								<td>
									<span class="psx-property-name psx-property-optional">title</span>
								</td>
								<td>
									<span class="psx-property-type psx-property-type-string">String</span>
								</td>
								<td>
									<span class="psx-property-description"/>
								</td>
								<td>
									<dl class="psx-property-constraint">
										<dt>Pattern</dt>
										<dd>
											<span class="psx-constraint-pattern">[A-z]+</span>
										</dd>
										<dt>Minimum</dt>
										<dd>
											<span class="psx-constraint-minimum">3</span>
										</dd>
										<dt>Maximum</dt>
										<dd>
											<span class="psx-constraint-maximum">16</span>
										</dd>
									</dl>
								</td>
							</tr>
							<tr>
								<td>
									<span class="psx-property-name psx-property-optional">date</span>
								</td>
								<td>
									<span class="psx-property-type psx-property-type-datetime">
										<a href="http://tools.ietf.org/html/rfc3339#section-5.6" title="RFC3339">DateTime</a>
									</span>
								</td>
								<td>
									<span class="psx-property-description"/>
								</td>
								<td/>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="psx-resource-data psx-resource-response">
			<h5>PATCH Response - 200 OK</h5>
			<div class="psx-resource-data-content">
				<div id="psx-type-31ead4d236fd038a7d55a40e2ca1171e" class="psx-complex-type">
					<h1>message</h1>
					<div class="psx-type-description"/>
					<table class="table psx-type-properties">
						<colgroup>
							<col width="20%" />
							<col width="20%" />
							<col width="40%" />
							<col width="20%" />
						</colgroup>
						<thead>
							<tr>
								<th>Property</th>
								<th>Type</th>
								<th>Description</th>
								<th>Constraints</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<span class="psx-property-name psx-property-optional">success</span>
								</td>
								<td>
									<span class="psx-property-type psx-property-type-boolean">Boolean</span>
								</td>
								<td>
									<span class="psx-property-description"/>
								</td>
								<td/>
							</tr>
							<tr>
								<td>
									<span class="psx-property-name psx-property-optional">message</span>
								</td>
								<td>
									<span class="psx-property-type psx-property-type-string">String</span>
								</td>
								<td>
									<span class="psx-property-description"/>
								</td>
								<td/>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
XML;

        $this->assertXmlStringEqualsXmlString($expect, $html, $html);
    }
}
