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

namespace PSX\Api\Documentation\Parser;

use PSX\Data\SchemaInterface;
use PSX\Test\Environment;

/**
 * AnnotationTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class AnnotationTest extends \PHPUnit_Framework_TestCase
{
    public function testParse()
    {
        $annotation = new Annotation(
            Environment::getService('annotation_reader'), 
            Environment::getService('schema_manager')
        );

        $versions = $annotation->parse(new Annotation\TestController(), '/foo');
        $resource = $versions->getResource(1);

        $this->assertEquals('/foo', $resource->getPath());

        $path = $resource->getPathParameters();

        $this->assertInstanceOf('PSX\Data\Schema\Property\ComplexType', $path->getDefinition());
        $this->assertInstanceOf('PSX\Data\Schema\Property\StringType', $path->getDefinition()->get('fooId'));

        $methods = $resource->getMethods();

        $this->assertEquals(['GET'], array_keys($methods));

        $query = $methods['GET']->getQueryParameters();

        $this->assertEquals('foo', $query->getDefinition()->get('foo')->getName());
        $this->assertEquals('Test', $query->getDefinition()->get('foo')->getDescription());
        $this->assertEquals('bar', $query->getDefinition()->get('bar')->getName());
        $this->assertEquals(true, $query->getDefinition()->get('bar')->isRequired());
        $this->assertEquals('baz', $query->getDefinition()->get('baz')->getName());
        $this->assertEquals(['foo', 'bar'], $query->getDefinition()->get('baz')->getEnumeration());
        $this->assertEquals('boz', $query->getDefinition()->get('boz')->getName());
        $this->assertEquals('[A-z]+', $query->getDefinition()->get('boz')->getPattern());
        $this->assertInstanceOf('PSX\Data\Schema\Property\ComplexType', $query->getDefinition());
        $this->assertInstanceOf('PSX\Data\Schema\Property\IntegerType', $query->getDefinition()->get('integer'));
        $this->assertInstanceOf('PSX\Data\Schema\Property\FloatType', $query->getDefinition()->get('number'));
        $this->assertInstanceOf('PSX\Data\Schema\Property\DateTimeType', $query->getDefinition()->get('date'));
        $this->assertInstanceOf('PSX\Data\Schema\Property\BooleanType', $query->getDefinition()->get('boolean'));
        $this->assertInstanceOf('PSX\Data\Schema\Property\StringType', $query->getDefinition()->get('string'));

        $request = $methods['GET']->getRequest();

        $this->assertInstanceOf('PSX\Data\SchemaInterface', $request);

        $response = $methods['GET']->getResponse(200);

        $this->assertInstanceOf('PSX\Data\SchemaInterface', $response);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testParseInvalid()
    {
        $annotation = new Annotation(
            Environment::getService('annotation_reader'), 
            Environment::getService('schema_manager')
        );

        $annotation->parse('foo', '/foo');
    }
}

