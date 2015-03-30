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

namespace PSX\Api\Documentation\Parser;

/**
 * RamlTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class RamlTest extends \PHPUnit_Framework_TestCase
{
	public function testParse()
	{
		$parser = new Raml();
		$doc    = $parser->parse(__DIR__ . '/test.raml', '/foo');

		$this->assertInstanceOf('PSX\Api\DocumentationInterface', $doc);
		$this->assertEquals('World Music API', $doc->getDescription());
		$this->assertEquals(2, $doc->getLatestVersion());

		$resource = $doc->getResource(2);

		$this->assertInstanceOf('PSX\Api\Resource', $resource);
		$this->assertEquals(array('GET', 'POST'), $resource->getAllowedMethods());
		$this->assertEquals('Some description', $resource->getDescription());

		// check GET
		$this->assertEquals('Informations about the method', $resource->getMethod('GET')->getDescription());

		$this->assertInstanceOf('PSX\Data\Schema\Property\ComplexType', $resource->getMethod('GET')->getQueryParameters()->getDefinition());
		$this->assertEquals('The number of pages to return', $resource->getMethod('GET')->getQueryParameters()->getDefinition()->get('pages')->getDescription());
		$this->assertEquals('Sets the start index', $resource->getMethod('GET')->getQueryParameters()->getDefinition()->get('startIndex')->getDescription());

		// check POST
		$this->assertInstanceOf('PSX\Api\Resource\Post', $resource->getMethod('POST'));

		$this->assertInstanceOf('PSX\Data\Schema\Property\ComplexType', $resource->getMethod('POST')->getQueryParameters()->getDefinition());

		$this->assertInstanceOf('PSX\Data\SchemaInterface', $resource->getMethod('POST')->getResponse(200));

		$property = $resource->getMethod('POST')->getRequest()->getDefinition();

		$this->assertInstanceOf('PSX\Data\Schema\Property\ComplexType', $property);
		$this->assertEquals('A canonical song', $property->getDescription());
		$this->assertInstanceOf('PSX\Data\Schema\Property\String', $property->get('title'));
		$this->assertInstanceOf('PSX\Data\Schema\Property\String', $property->get('artist'));

		$property = $resource->getMethod('POST')->getResponse(200)->getDefinition();

		$this->assertInstanceOf('PSX\Data\Schema\Property\ComplexType', $property);
		$this->assertEquals('A canonical song', $property->getDescription());
		$this->assertInstanceOf('PSX\Data\Schema\Property\String', $property->get('title'));
		$this->assertInstanceOf('PSX\Data\Schema\Property\String', $property->get('artist'));
	}

	public function testParsePath()
	{
		$parser = new Raml();
		$doc    = $parser->parse(__DIR__ . '/test.raml', '/bar/:bar_id');

		$this->assertInstanceOf('PSX\Api\DocumentationInterface', $doc);
		$this->assertEquals('World Music API', $doc->getDescription());
		$this->assertEquals(2, $doc->getLatestVersion());

		$resource = $doc->getResource(2);

		$this->assertInstanceOf('PSX\Api\Resource', $resource);
		$this->assertEquals(array('GET'), $resource->getAllowedMethods());
		$this->assertEquals('Returns details about bar', $resource->getDescription());

		$this->assertInstanceOf('PSX\Data\Schema\Property\ComplexType', $resource->getPathParameters()->getDefinition());
		$this->assertEquals('The id of bar', $resource->getPathParameters()->getDefinition()->get('bar_id')->getDescription());

		$this->assertInstanceOf('PSX\Data\SchemaInterface', $resource->getMethod('GET')->getResponse(200));

		$property = $resource->getMethod('GET')->getResponse(200)->getDefinition();

		$this->assertInstanceOf('PSX\Data\Schema\Property\ComplexType', $property);
		$this->assertEquals('A canonical song', $property->getDescription());
		$this->assertInstanceOf('PSX\Data\Schema\Property\String', $property->get('title'));
		$this->assertInstanceOf('PSX\Data\Schema\Property\String', $property->get('artist'));
	}
}
