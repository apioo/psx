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

use PSX\Data\SchemaInterface;

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
        $doc = Raml::fromFile(__DIR__ . '/test.raml', '/foo');

        $this->assertInstanceOf('PSX\Api\DocumentationInterface', $doc);
        $this->assertEquals('World Music API', $doc->getDescription());
        $this->assertEquals(2, $doc->getLatestVersion());

        $resource = $doc->getResource(2);

        $this->assertInstanceOf('PSX\Api\Resource', $resource);
        $this->assertEquals(array('GET', 'POST'), $resource->getAllowedMethods());
        $this->assertEquals('Bar', $resource->getTitle());
        $this->assertEquals('Some description', $resource->getDescription());

        // check GET
        $this->assertEquals('Informations about the method', $resource->getMethod('GET')->getDescription());

        $this->assertInstanceOf('PSX\Data\SchemaInterface', $resource->getMethod('GET')->getQueryParameters());
        $this->assertInstanceOf('PSX\Data\Schema\Property\ComplexType', $resource->getMethod('GET')->getQueryParameters()->getDefinition());
        $this->assertInstanceOf('PSX\Data\Schema\Property\FloatType', $resource->getMethod('GET')->getQueryParameters()->getDefinition()->get('pages'));
        $this->assertEquals('The number of pages to return', $resource->getMethod('GET')->getQueryParameters()->getDefinition()->get('pages')->getDescription());
        $this->assertInstanceOf('PSX\Data\Schema\Property\IntegerType', $resource->getMethod('GET')->getQueryParameters()->getDefinition()->get('param_integer'));
        $this->assertInstanceOf('PSX\Data\Schema\Property\FloatType', $resource->getMethod('GET')->getQueryParameters()->getDefinition()->get('param_number'));
        $this->assertEquals('The number', $resource->getMethod('GET')->getQueryParameters()->getDefinition()->get('param_number')->getDescription());
        $this->assertInstanceOf('PSX\Data\Schema\Property\DateTimeType', $resource->getMethod('GET')->getQueryParameters()->getDefinition()->get('param_date'));
        $this->assertInstanceOf('PSX\Data\Schema\Property\BooleanType', $resource->getMethod('GET')->getQueryParameters()->getDefinition()->get('param_boolean'));
        $this->assertInstanceOf('PSX\Data\Schema\Property\StringType', $resource->getMethod('GET')->getQueryParameters()->getDefinition()->get('param_string'));
        $this->assertParameters($resource->getMethod('GET')->getQueryParameters());

        // check POST
        $this->assertInstanceOf('PSX\Api\Resource\Post', $resource->getMethod('POST'));

        $this->assertInstanceOf('PSX\Data\Schema\Property\ComplexType', $resource->getMethod('POST')->getQueryParameters()->getDefinition());

        $this->assertInstanceOf('PSX\Data\SchemaInterface', $resource->getMethod('POST')->getResponse(200));

        $property = $resource->getMethod('POST')->getRequest()->getDefinition();

        $this->assertInstanceOf('PSX\Data\Schema\Property\ComplexType', $property);
        $this->assertEquals('A canonical song', $property->getDescription());
        $this->assertInstanceOf('PSX\Data\Schema\Property\StringType', $property->get('title'));
        $this->assertInstanceOf('PSX\Data\Schema\Property\StringType', $property->get('artist'));

        $property = $resource->getMethod('POST')->getResponse(200)->getDefinition();

        $this->assertInstanceOf('PSX\Data\Schema\Property\ComplexType', $property);
        $this->assertEquals('A canonical song', $property->getDescription());
        $this->assertInstanceOf('PSX\Data\Schema\Property\StringType', $property->get('title'));
        $this->assertInstanceOf('PSX\Data\Schema\Property\StringType', $property->get('artist'));
    }

    public function testParsePath()
    {
        $doc = Raml::fromFile(__DIR__ . '/test.raml', '/bar/:bar_id');

        $this->assertInstanceOf('PSX\Api\DocumentationInterface', $doc);
        $this->assertEquals('World Music API', $doc->getDescription());
        $this->assertEquals(2, $doc->getLatestVersion());

        $resource = $doc->getResource(2);

        $this->assertInstanceOf('PSX\Api\Resource', $resource);
        $this->assertEquals(array('GET'), $resource->getAllowedMethods());
        $this->assertEquals('Returns details about bar', $resource->getDescription());

        $this->assertInstanceOf('PSX\Data\SchemaInterface', $resource->getPathParameters());
        $this->assertInstanceOf('PSX\Data\Schema\Property\ComplexType', $resource->getPathParameters()->getDefinition());
        $this->assertParameters($resource->getPathParameters());

        $this->assertInstanceOf('PSX\Data\SchemaInterface', $resource->getMethod('GET')->getResponse(200));

        $property = $resource->getMethod('GET')->getResponse(200)->getDefinition();

        $this->assertInstanceOf('PSX\Data\Schema\Property\ComplexType', $property);
        $this->assertEquals('A canonical song', $property->getDescription());
        $this->assertInstanceOf('PSX\Data\Schema\Property\StringType', $property->get('title'));
        $this->assertInstanceOf('PSX\Data\Schema\Property\StringType', $property->get('artist'));
    }

    public function testParseNested()
    {
        $doc = Raml::fromFile(__DIR__ . '/test.raml', '/foo/bar');

        $this->assertInstanceOf('PSX\Api\DocumentationInterface', $doc);
        $this->assertEquals('World Music API', $doc->getDescription());
        $this->assertEquals(2, $doc->getLatestVersion());

        $resource = $doc->getResource(2);

        $this->assertInstanceOf('PSX\Api\Resource', $resource);
        $this->assertEquals(array('GET'), $resource->getAllowedMethods());
        $this->assertEquals('Some description', $resource->getDescription());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testParseResponseWithoutSchema()
    {
        $doc      = Raml::fromFile(__DIR__ . '/test.raml', '/foo');
        $resource = $doc->getResource(2);

        $resource->getMethod('POST')->getResponse(500);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testParseInvalidPath()
    {
        Raml::fromFile(__DIR__ . '/test.raml', '/test');
    }

    /**
     * @expectedException \PSX\Exception
     */
    public function testParseInvalidSchema()
    {
        Raml::fromFile(__DIR__ . '/test.raml', '/invalid_schema');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testParseInvalidSchemaReference()
    {
        Raml::fromFile(__DIR__ . '/test.raml', '/invalid_reference');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testFromFileNotExistingFile()
    {
        Raml::fromFile(__DIR__ . '/foo.raml', '/bar/:bar_id');
    }

    protected function assertParameters(SchemaInterface $parameters)
    {
        $this->assertInstanceOf('PSX\Data\Schema\Property\IntegerType', $parameters->getDefinition()->get('param_integer'));
        $this->assertEquals(true, $parameters->getDefinition()->get('param_integer')->isRequired());
        $this->assertEquals(8, $parameters->getDefinition()->get('param_integer')->getMin());
        $this->assertEquals(16, $parameters->getDefinition()->get('param_integer')->getMax());

        $this->assertInstanceOf('PSX\Data\Schema\Property\FloatType', $parameters->getDefinition()->get('param_number'));
        $this->assertEquals(false, $parameters->getDefinition()->get('param_number')->isRequired());
        $this->assertEquals('The number', $parameters->getDefinition()->get('param_number')->getDescription());

        $this->assertInstanceOf('PSX\Data\Schema\Property\DateTimeType', $parameters->getDefinition()->get('param_date'));
        $this->assertEquals(false, $parameters->getDefinition()->get('param_date')->isRequired());

        $this->assertInstanceOf('PSX\Data\Schema\Property\BooleanType', $parameters->getDefinition()->get('param_boolean'));
        $this->assertEquals(true, $parameters->getDefinition()->get('param_boolean')->isRequired());

        $this->assertInstanceOf('PSX\Data\Schema\Property\StringType', $parameters->getDefinition()->get('param_string'));
        $this->assertEquals(false, $parameters->getDefinition()->get('param_string')->isRequired());
        $this->assertEquals(8, $parameters->getDefinition()->get('param_string')->getMinLength());
        $this->assertEquals(16, $parameters->getDefinition()->get('param_string')->getMaxLength());
        $this->assertEquals('[A-z]+', $parameters->getDefinition()->get('param_string')->getPattern());
        $this->assertEquals(['foo', 'bar'], $parameters->getDefinition()->get('param_string')->getEnumeration());
    }
}
