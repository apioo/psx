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

use PSX\Api\Resource;
use PSX\Api\Resource\Generator\GeneratorTestCase;
use PSX\Data\Schema\Generator\Html as HtmlGenerator;

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
		$generator = new Schema(new HtmlGenerator());
		$html      = $generator->generate($this->getResource());

		$expect = <<<XML
<div class="psx-resource psx-api-resource-generator-html-schema" data-path="/foo/bar" data-status="1">
	<h4>Schema</h4>
	<div class="psx-resource-method" data-method="GET">
		<div class="psx-resource-data psx-resource-response">
			<h5>GET Response - 200 OK</h5>
			<div class="psx-resource-data-content">
				<div class="psx-complex-type" id="psx-type-9499a45cc2cb810ebcb38cc840bebf51">
					<h1>collection</h1>
					<div class="psx-type-description"/>
					<table class="table psx-type-properties">
						<colgroup>
							<col width="20%"/>
							<col width="20%"/>
							<col width="40%"/>
							<col width="20%"/>
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
									<span class="psx-property-type psx-property-type-array">Array&lt;<span class="psx-property-type psx-property-type-complex"><a href="#psx-type-7738db4616810154ab42db61b65f74aa">item</a></span>&gt;</span>
								</td>
								<td>
									<span class="psx-property-description"/>
								</td>
								<td/>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="psx-complex-type" id="psx-type-7738db4616810154ab42db61b65f74aa">
					<h1>item</h1>
					<div class="psx-type-description"/>
					<table class="table psx-type-properties">
						<colgroup>
							<col width="20%"/>
							<col width="20%"/>
							<col width="40%"/>
							<col width="20%"/>
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
		<div class="psx-resource-data psx-resource-request">
			<h5>POST Request</h5>
			<div class="psx-resource-data-content">
				<div class="psx-complex-type" id="psx-type-5bd2953081685075d567417f01494700">
					<h1>item</h1>
					<div class="psx-type-description"/>
					<table class="table psx-type-properties">
						<colgroup>
							<col width="20%"/>
							<col width="20%"/>
							<col width="40%"/>
							<col width="20%"/>
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
			<h5>POST Response - 200 OK</h5>
			<div class="psx-resource-data-content">
				<div class="psx-complex-type" id="psx-type-a394c0a8d56c158f0f29acf97cbdc8f6">
					<h1>message</h1>
					<div class="psx-type-description"/>
					<table class="table psx-type-properties">
						<colgroup>
							<col width="20%"/>
							<col width="20%"/>
							<col width="40%"/>
							<col width="20%"/>
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
		<div class="psx-resource-data psx-resource-request">
			<h5>PUT Request</h5>
			<div class="psx-resource-data-content">
				<div class="psx-complex-type" id="psx-type-cb65873d8f84681e263c50e2260d7bb9">
					<h1>item</h1>
					<div class="psx-type-description"/>
					<table class="table psx-type-properties">
						<colgroup>
							<col width="20%"/>
							<col width="20%"/>
							<col width="40%"/>
							<col width="20%"/>
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
				<div class="psx-complex-type" id="psx-type-a394c0a8d56c158f0f29acf97cbdc8f6">
					<h1>message</h1>
					<div class="psx-type-description"/>
					<table class="table psx-type-properties">
						<colgroup>
							<col width="20%"/>
							<col width="20%"/>
							<col width="40%"/>
							<col width="20%"/>
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
		<div class="psx-resource-data psx-resource-request">
			<h5>DELETE Request</h5>
			<div class="psx-resource-data-content">
				<div class="psx-complex-type" id="psx-type-cb65873d8f84681e263c50e2260d7bb9">
					<h1>item</h1>
					<div class="psx-type-description"/>
					<table class="table psx-type-properties">
						<colgroup>
							<col width="20%"/>
							<col width="20%"/>
							<col width="40%"/>
							<col width="20%"/>
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
				<div class="psx-complex-type" id="psx-type-a394c0a8d56c158f0f29acf97cbdc8f6">
					<h1>message</h1>
					<div class="psx-type-description"/>
					<table class="table psx-type-properties">
						<colgroup>
							<col width="20%"/>
							<col width="20%"/>
							<col width="40%"/>
							<col width="20%"/>
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

		$this->assertXmlStringEqualsXmlString($expect, $html);
	}
}
