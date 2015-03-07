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

use PSX\Api\View;
use PSX\Api\View\Generator\GeneratorTestCase;
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
		$html      = $generator->generate($this->getView());

		$expect = <<<XML
<div class="view psx-api-view-generator-html-schema" data-status="0" data-path="/foo/bar">
	<h4>Schema</h4>
	<div class="view-schema" data-modifier="33">
		<h5>GET Response</h5>
		<div class="view-schema-content">
			<div id="type-9499a45cc2cb810ebcb38cc840bebf51" class="type">
				<h1>collection</h1>
				<div class="type-description"/>
				<table class="table type-properties">
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
								<span class="property-name property-optional">entry</span>
							</td>
							<td>
								<span class="property-type type-array">Array&lt;<span class="property-type type-object">
										<a href="#type-7738db4616810154ab42db61b65f74aa">item</a>
									</span>&gt;</span>
							</td>
							<td>
								<span class="property-description"/>
							</td>
							<td/>
						</tr>
					</tbody>
				</table>
			</div>
			<div id="type-7738db4616810154ab42db61b65f74aa" class="type">
				<h1>item</h1>
				<div class="type-description"/>
				<table class="table type-properties">
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
								<span class="property-name property-optional">id</span>
							</td>
							<td>
								<span class="property-type property-type-integer">Integer</span>
							</td>
							<td>
								<span class="property-description"/>
							</td>
							<td/>
						</tr>
						<tr>
							<td>
								<span class="property-name property-optional">userId</span>
							</td>
							<td>
								<span class="property-type property-type-integer">Integer</span>
							</td>
							<td>
								<span class="property-description"/>
							</td>
							<td/>
						</tr>
						<tr>
							<td>
								<span class="property-name property-optional">title</span>
							</td>
							<td>
								<span class="property-type property-type-string">String</span>
							</td>
							<td>
								<span class="property-description"/>
							</td>
							<td>
								<dl class="property-constraint">
									<dt>Pattern</dt>
									<dd>
										<span class="constraint-pattern">[A-z]+</span>
									</dd>
									<dt>Minimum</dt>
									<dd>
										<span class="constraint-minimum">3</span>
									</dd>
									<dt>Maximum</dt>
									<dd>
										<span class="constraint-maximum">16</span>
									</dd>
								</dl>
							</td>
						</tr>
						<tr>
							<td>
								<span class="property-name property-optional">date</span>
							</td>
							<td>
								<span class="property-type property-type-datetime">
									<a href="http://tools.ietf.org/html/rfc3339#section-5.6" title="RFC3339">DateTime</a>
								</span>
							</td>
							<td>
								<span class="property-description"/>
							</td>
							<td/>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="view-schema" data-modifier="18">
		<h5>POST Request</h5>
		<div class="view-schema-content">
			<div id="type-5bd2953081685075d567417f01494700" class="type">
				<h1>item</h1>
				<div class="type-description"/>
				<table class="table type-properties">
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
								<span class="property-name property-optional">id</span>
							</td>
							<td>
								<span class="property-type property-type-integer">Integer</span>
							</td>
							<td>
								<span class="property-description"/>
							</td>
							<td/>
						</tr>
						<tr>
							<td>
								<span class="property-name property-optional">userId</span>
							</td>
							<td>
								<span class="property-type property-type-integer">Integer</span>
							</td>
							<td>
								<span class="property-description"/>
							</td>
							<td/>
						</tr>
						<tr>
							<td>
								<span class="property-name property-required">title</span>
							</td>
							<td>
								<span class="property-type property-type-string">String</span>
							</td>
							<td>
								<span class="property-description"/>
							</td>
							<td>
								<dl class="property-constraint">
									<dt>Pattern</dt>
									<dd>
										<span class="constraint-pattern">[A-z]+</span>
									</dd>
									<dt>Minimum</dt>
									<dd>
										<span class="constraint-minimum">3</span>
									</dd>
									<dt>Maximum</dt>
									<dd>
										<span class="constraint-maximum">16</span>
									</dd>
								</dl>
							</td>
						</tr>
						<tr>
							<td>
								<span class="property-name property-required">date</span>
							</td>
							<td>
								<span class="property-type property-type-datetime">
									<a href="http://tools.ietf.org/html/rfc3339#section-5.6" title="RFC3339">DateTime</a>
								</span>
							</td>
							<td>
								<span class="property-description"/>
							</td>
							<td/>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="view-schema" data-modifier="34">
		<h5>POST Response</h5>
		<div class="view-schema-content">
			<div id="type-a394c0a8d56c158f0f29acf97cbdc8f6" class="type">
				<h1>message</h1>
				<div class="type-description"/>
				<table class="table type-properties">
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
								<span class="property-name property-optional">success</span>
							</td>
							<td>
								<span class="property-type property-type-boolean">Boolean</span>
							</td>
							<td>
								<span class="property-description"/>
							</td>
							<td/>
						</tr>
						<tr>
							<td>
								<span class="property-name property-optional">message</span>
							</td>
							<td>
								<span class="property-type property-type-string">String</span>
							</td>
							<td>
								<span class="property-description"/>
							</td>
							<td/>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="view-schema" data-modifier="20">
		<h5>PUT Request</h5>
		<div class="view-schema-content">
			<div id="type-cb65873d8f84681e263c50e2260d7bb9" class="type">
				<h1>item</h1>
				<div class="type-description"/>
				<table class="table type-properties">
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
								<span class="property-name property-required">id</span>
							</td>
							<td>
								<span class="property-type property-type-integer">Integer</span>
							</td>
							<td>
								<span class="property-description"/>
							</td>
							<td/>
						</tr>
						<tr>
							<td>
								<span class="property-name property-optional">userId</span>
							</td>
							<td>
								<span class="property-type property-type-integer">Integer</span>
							</td>
							<td>
								<span class="property-description"/>
							</td>
							<td/>
						</tr>
						<tr>
							<td>
								<span class="property-name property-optional">title</span>
							</td>
							<td>
								<span class="property-type property-type-string">String</span>
							</td>
							<td>
								<span class="property-description"/>
							</td>
							<td>
								<dl class="property-constraint">
									<dt>Pattern</dt>
									<dd>
										<span class="constraint-pattern">[A-z]+</span>
									</dd>
									<dt>Minimum</dt>
									<dd>
										<span class="constraint-minimum">3</span>
									</dd>
									<dt>Maximum</dt>
									<dd>
										<span class="constraint-maximum">16</span>
									</dd>
								</dl>
							</td>
						</tr>
						<tr>
							<td>
								<span class="property-name property-optional">date</span>
							</td>
							<td>
								<span class="property-type property-type-datetime">
									<a href="http://tools.ietf.org/html/rfc3339#section-5.6" title="RFC3339">DateTime</a>
								</span>
							</td>
							<td>
								<span class="property-description"/>
							</td>
							<td/>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="view-schema" data-modifier="36">
		<h5>PUT Response</h5>
		<div class="view-schema-content">
			<div id="type-a394c0a8d56c158f0f29acf97cbdc8f6" class="type">
				<h1>message</h1>
				<div class="type-description"/>
				<table class="table type-properties">
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
								<span class="property-name property-optional">success</span>
							</td>
							<td>
								<span class="property-type property-type-boolean">Boolean</span>
							</td>
							<td>
								<span class="property-description"/>
							</td>
							<td/>
						</tr>
						<tr>
							<td>
								<span class="property-name property-optional">message</span>
							</td>
							<td>
								<span class="property-type property-type-string">String</span>
							</td>
							<td>
								<span class="property-description"/>
							</td>
							<td/>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="view-schema" data-modifier="24">
		<h5>DELETE Request</h5>
		<div class="view-schema-content">
			<div id="type-cb65873d8f84681e263c50e2260d7bb9" class="type">
				<h1>item</h1>
				<div class="type-description"/>
				<table class="table type-properties">
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
								<span class="property-name property-required">id</span>
							</td>
							<td>
								<span class="property-type property-type-integer">Integer</span>
							</td>
							<td>
								<span class="property-description"/>
							</td>
							<td/>
						</tr>
						<tr>
							<td>
								<span class="property-name property-optional">userId</span>
							</td>
							<td>
								<span class="property-type property-type-integer">Integer</span>
							</td>
							<td>
								<span class="property-description"/>
							</td>
							<td/>
						</tr>
						<tr>
							<td>
								<span class="property-name property-optional">title</span>
							</td>
							<td>
								<span class="property-type property-type-string">String</span>
							</td>
							<td>
								<span class="property-description"/>
							</td>
							<td>
								<dl class="property-constraint">
									<dt>Pattern</dt>
									<dd>
										<span class="constraint-pattern">[A-z]+</span>
									</dd>
									<dt>Minimum</dt>
									<dd>
										<span class="constraint-minimum">3</span>
									</dd>
									<dt>Maximum</dt>
									<dd>
										<span class="constraint-maximum">16</span>
									</dd>
								</dl>
							</td>
						</tr>
						<tr>
							<td>
								<span class="property-name property-optional">date</span>
							</td>
							<td>
								<span class="property-type property-type-datetime">
									<a href="http://tools.ietf.org/html/rfc3339#section-5.6" title="RFC3339">DateTime</a>
								</span>
							</td>
							<td>
								<span class="property-description"/>
							</td>
							<td/>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="view-schema" data-modifier="40">
		<h5>DELETE Response</h5>
		<div class="view-schema-content">
			<div id="type-a394c0a8d56c158f0f29acf97cbdc8f6" class="type">
				<h1>message</h1>
				<div class="type-description"/>
				<table class="table type-properties">
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
								<span class="property-name property-optional">success</span>
							</td>
							<td>
								<span class="property-type property-type-boolean">Boolean</span>
							</td>
							<td>
								<span class="property-description"/>
							</td>
							<td/>
						</tr>
						<tr>
							<td>
								<span class="property-name property-optional">message</span>
							</td>
							<td>
								<span class="property-type property-type-string">String</span>
							</td>
							<td>
								<span class="property-description"/>
							</td>
							<td/>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
XML;

		$this->assertXmlStringEqualsXmlString($expect, $html);
	}
}
