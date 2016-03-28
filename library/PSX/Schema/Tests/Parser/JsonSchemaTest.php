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

namespace PSX\Schema\Tests\Parser;

use PSX\Http;
use PSX\Schema\Parser\JsonSchema;

/**
 * JsonSchemaTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class JsonSchemaTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The offical json schema is recursive so we check whether we can parse it
     * without a problem
     */
    public function testParseRecursion()
    {
        $schema   = JsonSchema::fromFile(__DIR__ . '/JsonSchema/schema.json');
        $property = $schema->getDefinition();

        $this->assertInstanceOf('PSX\Schema\PropertyInterface', $property);
    }

    public function testParseExternalResource()
    {
        $handler  = Http\Handler\Mock::getByXmlDefinition(__DIR__ . '/JsonSchema/http_mock.xml');
        $http     = new Http\Client($handler);
        $resolver = JsonSchema\RefResolver::createDefault($http);

        $parser   = new JsonSchema(__DIR__ . '/JsonSchema', $resolver);
        $schema   = $parser->parse(file_get_contents(__DIR__ . '/JsonSchema/test_schema.json'));
        $property = $schema->getDefinition();

        $this->assertInstanceOf('PSX\Schema\PropertyInterface', $property);
        $this->assertEquals('record', $property->getName());
        $this->assertInstanceOf('PSX\Schema\Property\IntegerType', $property->get('id'));
        $this->assertInstanceOf('PSX\Schema\Property\ComplexType', $property->get('bar'));
        $this->assertInstanceOf('PSX\Schema\Property\ArrayType', $property->get('bar')->get('number'));
        $this->assertInstanceOf('PSX\Schema\Property\IntegerType', $property->get('bar')->get('number')->getPrototype());
        $this->assertEquals(4, $property->get('bar')->get('number')->getPrototype()->getMin());
        $this->assertInstanceOf('PSX\Schema\Property\IntegerType', $property->get('foo'));
        $this->assertEquals(4, $property->get('foo')->getMin());
        $this->assertEquals(12, $property->get('foo')->getMax());
        $this->assertInstanceOf('PSX\Schema\Property\IntegerType', $property->get('value'));
        $this->assertEquals(0, $property->get('value')->getMin());
        $this->assertInstanceOf('PSX\Schema\Property\ComplexType', $property->get('test'));
        $this->assertEquals(true, $property->get('test')->get('index')->isRequired());
        $this->assertInstanceOf('PSX\Schema\Property\IntegerType', $property->get('test')->get('index'));
        $this->assertEquals(true, $property->get('test')->get('foo')->isRequired());
        $this->assertInstanceOf('PSX\Schema\Property\StringType', $property->get('test')->get('foo'));
        $this->assertInstanceOf('PSX\Schema\Property\ComplexType', $property->get('normal'));
        $this->assertEquals(false, $property->get('normal')->get('index')->isRequired());
        $this->assertInstanceOf('PSX\Schema\Property\IntegerType', $property->get('normal')->get('index'));
        $this->assertEquals(false, $property->get('normal')->get('foo')->isRequired());
        $this->assertInstanceOf('PSX\Schema\Property\StringType', $property->get('normal')->get('foo'));

        $this->assertInstanceOf('PSX\Schema\Property\ComplexType', $property->get('object'));
        $this->assertEquals('description', $property->get('object')->getDescription());
        $this->assertInstanceOf('PSX\Schema\Property\ArrayType', $property->get('array'));
        $this->assertEquals(1, $property->get('array')->getMinLength());
        $this->assertEquals(9, $property->get('array')->getMaxLength());
        $this->assertInstanceOf('PSX\Schema\Property\BooleanType', $property->get('boolean'));
        $this->assertInstanceOf('PSX\Schema\Property\IntegerType', $property->get('integer'));
        $this->assertEquals(1, $property->get('integer')->getMin());
        $this->assertEquals(4, $property->get('integer')->getMax());
        $this->assertInstanceOf('PSX\Schema\Property\FloatType', $property->get('number'));
        $this->assertInstanceOf('PSX\Schema\Property\StringType', $property->get('string'));
        $this->assertEquals('[A-z]+', $property->get('string')->getPattern());
        $this->assertEquals(['foo', 'bar'], $property->get('string')->getEnumeration());
        $this->assertEquals(2, $property->get('string')->getMinLength());
        $this->assertEquals(4, $property->get('string')->getMaxLength());
        $this->assertInstanceOf('PSX\Schema\Property\DateType', $property->get('date'));
        $this->assertInstanceOf('PSX\Schema\Property\DateTimeType', $property->get('datetime'));
        $this->assertInstanceOf('PSX\Schema\Property\DurationType', $property->get('duration'));
        $this->assertInstanceOf('PSX\Schema\Property\TimeType', $property->get('time'));
        $this->assertInstanceOf('PSX\Schema\Property\StringType', $property->get('unknown'));

        $this->assertInstanceOf('PSX\Schema\Property\ChoiceType', $property->get('choice'));
        $this->assertEquals(2, count($property->get('choice')->getProperties()));
        $this->assertInstanceOf('PSX\Schema\Property\ComplexType', $property->get('choice')->get('foo'));
        $this->assertInstanceOf('PSX\Schema\Property\ComplexType', $property->get('choice')->get('bar'));
        $this->assertInstanceOf('PSX\Schema\Property\StringType', $property->get('choice')->get('foo')->get('foo'));
        $this->assertInstanceOf('PSX\Schema\Property\StringType', $property->get('choice')->get('bar')->get('bar'));
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessageRegExp /^Could not load json schema (.*)$/
     */
    public function testParseInvalidFile()
    {
        JsonSchema::fromFile(__DIR__ . '/JsonSchema/foo.json');
    }

    /**
     * @expectedException \PSX\Schema\Parser\JsonSchema\UnsupportedVersionException
     * @expectedExceptionMessage Invalid version requires http://json-schema.org/draft-04/schema#
     */
    public function testParseInvalidVersion()
    {
        JsonSchema::fromFile(__DIR__ . '/JsonSchema/wrong_version_schema.json');
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessageRegExp /^Could not load external schema (.*)$/
     */
    public function testParseInvalidFileRef()
    {
        JsonSchema::fromFile(__DIR__ . '/JsonSchema/invalid_file_ref_schema.json');
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Could not load external schema http://localhost/foo/bar#/definitions/bar received 404
     */
    public function testParseInvalidHttpRef()
    {
        $handler  = Http\Handler\Mock::getByXmlDefinition(__DIR__ . '/JsonSchema/http_mock.xml');
        $http     = new Http\Client($handler);
        $resolver = JsonSchema\RefResolver::createDefault($http);

        $parser   = new JsonSchema(__DIR__, $resolver);
        $parser->parse(file_get_contents(__DIR__ . '/JsonSchema/invalid_http_ref_schema.json'));
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Unknown protocol scheme foo
     */
    public function testParseInvalidSchemaRef()
    {
        JsonSchema::fromFile(__DIR__ . '/JsonSchema/unknown_protocol_ref_schema.json');
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Could not resolve pointer /definitions/bar
     */
    public function testParseInvalidDocumentRef()
    {
        JsonSchema::fromFile(__DIR__ . '/JsonSchema/invalid_document_ref_schema.json');
    }

}
